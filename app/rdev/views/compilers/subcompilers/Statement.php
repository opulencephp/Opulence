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

        return $this->compilePartStatements($template, $content);
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
        $callback = function($matches) use ($template)
        {
            $parentTemplate = $this->templateFactory->create($matches[2]);

            // Copy parent's tags to child
            foreach($parentTemplate->getTags() as $name => $value)
            {
                $template->setTag($name, $value);
            }

            // Copy parent's vars to child
            foreach($parentTemplate->getVars() as $name => $value)
            {
                $template->setVar($name, $value);
            }

            return $parentTemplate->getContents();
        };
        $regex = sprintf(
            '/(?<!%s)%s\s*extend\((["|\'])([^\1]+)\1\)\s*%s/sU',
            preg_quote("\\", "/"),
            preg_quote($template->getStatementOpenTag(), "/"),
            preg_quote($template->getStatementCloseTag(), "/")
        );
        $count = 10000;

        do
        {
            $content = preg_replace_callback($regex, $callback, $content, -1, $count);
        }
        while($count > 0);

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
            $template->setTag($matches[2], $matches[3]);

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
}