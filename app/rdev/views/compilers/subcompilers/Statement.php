<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the RDev statement sub-compiler
 */
namespace RDev\Views\Compilers\SubCompilers;
use RDev\Views;
use RDev\Views\Compilers;
use RDev\Views\Factories;

class Statement extends SubCompiler
{
    /** @var Factories\ITemplateFactory The factory that creates templates */
    private $templateFactory = null;

    /**
     * {@inheritdoc}
     * @param Factories\ITemplateFactory $templateFactory The factory that creates templates
     */
    public function __construct(Compilers\ICompiler $parentCompiler, Factories\ITemplateFactory $templateFactory)
    {
        parent::__construct($parentCompiler);

        $this->templateFactory = $templateFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function compile(Views\ITemplate $template, $content)
    {
        // Need to compile the extends before the parts so that we have all part statements in template
        $content = $this->compileExtendStatements($template, $content);
        $content = $this->compilePartStatements($template, $content);

        return $this->compileShowStatements($template, $content);
    }

    /**
     * Compiles extend statements
     *
     * @param Views\ITemplate $template The template to compile
     * @param string $content The compiled contents
     * @return string The compiled contents
     */
    private function compileExtendStatements(Views\ITemplate $template, $content)
    {
        $parentStack = [];
        $callback = function($matches) use (&$parentStack)
        {
            $parentTemplate = $this->templateFactory->create($matches[2]);
            $parentStack[] = $parentTemplate;

            return $parentTemplate->getContents();
        };
        $regex = sprintf(
            '/(?<!%s)%s\s*extend\((["|\'])([^\1]+)\1\)\s*%s/sU',
            preg_quote("\\", "/"),
            preg_quote($template->getStatementOpenTag(), "/"),
            preg_quote($template->getStatementCloseTag(), "/")
        );

        /**
         * By putting this in a loop, we handle templates that extend templates that ...
         */
        $count = 1;

        do
        {
            $content = preg_replace_callback($regex, $callback, $content, -1, $count);
        }
        while($count > 0);

        // The nearest parents' tags and values take precedence over further ones
        while(count($parentStack) > 0)
        {
            /** @var Views\ITemplate $parentTemplate */
            $parentTemplate = array_pop($parentStack);
            $template->setParts($parentTemplate->getParts());
            $template->setTags($parentTemplate->getTags());
            $template->setVars($parentTemplate->getVars());
        }

        return $content;
    }

    /**
     * Compiles part statements
     *
     * @param Views\ITemplate $template The template to compile
     * @param string $content The compiled contents
     * @return string The compiled contents
     */
    private function compilePartStatements(Views\ITemplate $template, $content)
    {
        $callback = function($matches) use ($template)
        {
            $template->setPart($matches[2], $matches[3]);

            return "";
        };
        $regex = sprintf(
            '/(?<!%s)%s\s*part\((["|\'])([^\1]+)\1\)\s*%s(.*)%s\s*endpart\s*%s/sU',
            preg_quote("\\", "/"),
            preg_quote($template->getStatementOpenTag(), "/"),
            preg_quote($template->getStatementCloseTag(), "/"),
            preg_quote($template->getStatementOpenTag(), "/"),
            preg_quote($template->getStatementCloseTag(), "/")
        );
        $content = preg_replace_callback($regex, $callback, $content);

        return $content;
    }

    /**
     * Compiles show statements
     *
     * @param Views\ITemplate $template The template to compile
     * @param string $content The compiled contents
     * @return string The compiled contents
     */
    private function compileShowStatements(Views\ITemplate $template, $content)
    {
        $count = 1;

        // Doing this in a loop allows us to compile statements that return statements
        do
        {
            $callback = function ($matches) use ($template)
            {
                return $template->getPart($matches[2]);
            };
            $regex = sprintf(
                '/(?<!%s)%s\s*show\((["|\'])([^\1]+)\1\)\s*%s/sU',
                preg_quote("\\", "/"),
                preg_quote($template->getStatementOpenTag(), "/"),
                preg_quote($template->getStatementCloseTag(), "/")
            );
            $content = preg_replace_callback($regex, $callback, $content, -1, $count);
        }
        while($count > 0);

        return $content;
    }
}