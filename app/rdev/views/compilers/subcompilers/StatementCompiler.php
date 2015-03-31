<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the RDev statement sub-compiler
 */
namespace RDev\Views\Compilers\SubCompilers;
use RDev\Views\Compilers\ICompiler;
use RDev\Views\Factories\ITemplateFactory;
use RDev\Views\ITemplate;

class StatementCompiler extends SubCompiler
{
    /** @var ITemplateFactory The factory that creates templates */
    private $templateFactory = null;
    /** @var array The list of control structures */
    private $controlStructures = [
        "part"
    ];

    /**
     * {@inheritdoc}
     * @param ITemplateFactory $templateFactory The factory that creates templates
     */
    public function __construct(ICompiler $parentCompiler, ITemplateFactory $templateFactory)
    {
        parent::__construct($parentCompiler);

        $this->templateFactory = $templateFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function compile(ITemplate $template, $content)
    {
        $content = $this->compileExtendStatements($template, $content);
        $content = $this->compileIncludeStatements($template, $content);
        $content = $this->compileControlStructures($template, $content);
        $content = $this->compileShowStatements($template, $content);
        $content = $this->compileParentStatements($template, $content);

        return $this->cleanupStatements($template, $content);
    }

    /**
     * Cleans up any un-executed statements
     *
     * @param ITemplate $template The template to cleanup
     * @param string $content The content to cleanup
     * @return string The cleaned up contents
     */
    private function cleanupStatements(ITemplate $template, $content)
    {
        // Match anything
        $closeStatementDelimiter = $template->getDelimiters(ITemplate::DELIMITER_TYPE_STATEMENT)[1];
        $statement = sprintf(
            "(?:(?:(?!%s).)*)",
            preg_quote($closeStatementDelimiter)
        );
        // Clean closed statements
        $content = preg_replace($this->getStatementRegex($template, $statement, false, false), "", $content);
        // Clean self-closed statements
        $content = preg_replace($this->getStatementRegex($template, $statement, false, true), "", $content);

        return $content;
    }

    /**
     * Compiles control structures
     *
     * @param ITemplate $template The template to compile
     * @param string $content The compiled contents
     * @return string The compiled contents
     */
    private function compileControlStructures(ITemplate $template, $content)
    {
        $callback = function($matches) use ($template)
        {
            switch($matches[1])
            {
                case "part":
                    $template->setPart($matches[3], $matches[4]);

                    break;
            }

            return "";
        };
        $regex = $this->getStatementRegex($template, $this->controlStructures, true, false);
        $content = preg_replace_callback($regex, $callback, $content);

        return $content;
    }

    /**
     * Compiles extend statements
     *
     * @param ITemplate $template The template to compile
     * @param string $content The compiled contents
     * @return string The compiled contents
     */
    private function compileExtendStatements(ITemplate $template, $content)
    {
        $parentStack = [];
        $callback = function($matches) use (&$parentStack)
        {
            $parentTemplate = $this->templateFactory->create($matches[3]);
            $parentContents = $this->compileControlStructures($parentTemplate, $parentTemplate->getContents());
            $parentStack[] = $parentTemplate;

            return $parentContents;
        };
        $regex = $this->getStatementRegex($template, "extends", true, true);

        /**
         * By putting this in a loop, we handle templates that extend templates that ...
         */
        $count = 1;

        do
        {
            $content = preg_replace_callback($regex, $callback, $content, -1, $count);
        }
        while($count > 0);

        $currChildTemplate = $template;

        // Inherit the parents' parts, tags, and vars
        // The nearest parents' tags and values take precedence over further ones
        while(count($parentStack) > 0)
        {
            /** @var ITemplate $parentTemplate */
            $parentTemplate = array_shift($parentStack);
            $currChildTemplate->setParent($parentTemplate);
            $currChildTemplate = $parentTemplate;
        }

        return $content;
    }

    /**
     * Compiles include statements
     *
     * @param ITemplate $template The template to compile
     * @param string $content The compiled contents
     * @return string The compiled contents
     */
    private function compileIncludeStatements(ITemplate $template, $content)
    {
        $callback = function($matches) use (&$inheritanceStack)
        {
            $includedTemplate = $this->templateFactory->create($matches[3]);

            return $includedTemplate->getContents();
        };
        $regex = $this->getStatementRegex($template, "include", true, true);

        /**
         * By putting this in a loop, we handle templates that include templates that ...
         */
        $count = 1;

        do
        {
            $content = preg_replace_callback($regex, $callback, $content, -1, $count);
        }
        while($count > 0);

        return $content;
    }

    /**
     * Compiles parent statements
     *
     * @param ITemplate $template The template to compile
     * @param string $content The compiled contents
     * @return string The compiled contents
     */
    private function compileParentStatements(ITemplate $template, $content)
    {
        $count = 1;

        // Doing this in a loop allows us to compile statements that return statements
        do
        {
            $callback = function ($matches) use ($template)
            {
                $currTemplate = $template;

                while($currTemplate->getParent() !== null)
                {
                    foreach($currTemplate->getParent()->getParts() as $name => $content)
                    {
                        if($matches[3] == $name)
                        {
                            // In the case that the content contains a call to a higher-up parent, compile the content
                            return $this->compileParentStatements($currTemplate->getParent(), $content);
                        }
                    }

                    $currTemplate = $currTemplate->getParent();
                }

                return "";
            };
            $regex = $this->getStatementRegex($template, "parent", true, true);
            $content = preg_replace_callback($regex, $callback, $content, -1, $count);
        }
        while($count > 0);

        return $content;
    }

    /**
     * Compiles show statements
     *
     * @param ITemplate $template The template to compile
     * @param string $content The compiled contents
     * @return string The compiled contents
     */
    private function compileShowStatements(ITemplate $template, $content)
    {
        $count = 1;

        // Doing this in a loop allows us to compile statements that return statements
        do
        {
            $callback = function ($matches) use ($template)
            {
                return $template->getPart($matches[3]);
            };
            $regex = $this->getStatementRegex($template, "show", true, true);
            $content = preg_replace_callback($regex, $callback, $content, -1, $count);
        }
        while($count > 0);

        return $content;
    }

    /**
     * Gets the regex to compile a statement
     *
     * @param ITemplate $template The template whose statements we're compiling
     * @param string|array $statement The statement or list of statements whose regex we are building
     * @param bool $escapeStatement Whether or not we want to escape the statement in the regex
     * @param bool $isSelfClosed Whether or not the statement is self-closed
     * @return string The regex
     */
    private function getStatementRegex(ITemplate $template, $statement, $escapeStatement, $isSelfClosed)
    {
        $openStatementRegex = '(?<!%s)%s\s*(%s)\((?:(["|\'])([^\2]+)\2)?\)\s*%s';
        $closeStatementRegex = '(.*)%s\s*end\1\s*%s';
        $statementDelimiters = $template->getDelimiters(ITemplate::DELIMITER_TYPE_STATEMENT);

        if(is_array($statement))
        {
            if($escapeStatement)
            {
                $statement = implode("|", $this->pregQuoteArray($statement));
            }
            else
            {
                $statement = implode("|", $statement);
            }
        }
        elseif($escapeStatement)
        {
            $statement = preg_quote($statement, "/");
        }

        $regex = $openStatementRegex;
        $sprintfArgs = [
            preg_quote("\\", "/"),
            preg_quote($statementDelimiters[0], "/"),
            $statement,
            preg_quote($statementDelimiters[1], "/")
        ];

        if(!$isSelfClosed)
        {
            $regex .= $closeStatementRegex;
            $sprintfArgs[] = preg_quote($statementDelimiters[0], "/");
            $sprintfArgs[] = preg_quote($statementDelimiters[1], "/");
        }

        // Add the regex to the beginning of the argument list
        array_unshift($sprintfArgs, $regex);
        $regex = call_user_func_array("sprintf", $sprintfArgs);
        $regex = '/' . $regex . '/sU';

        return $regex;
    }

    /**
     * Preg-quotes each entry in an array
     *
     * @param array $array The array whose contents will be quoted
     * @return array The quoted array
     */
    private function pregQuoteArray(array $array)
    {
        $callback = function($value)
        {
            return preg_quote($value, "/");
        };

        return array_map($callback, $array);
    }
}