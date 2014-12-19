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

class StatementCompiler extends SubCompiler
{
    /** @var Factories\ITemplateFactory The factory that creates templates */
    private $templateFactory = null;
    /** @var array The list of control structures */
    private $controlStructures = [
        "part"
    ];

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
        // Need to compile the extends before the parts so that we have all part statements in the template
        $content = $this->compileExtendStatements($template, $content);
        $content = $this->compileIncludeStatements($template, $content);
        $content = $this->compileControlStructures($template, $content);
        $content = $this->compileShowStatements($template, $content);

        return $this->cleanupStatements($template, $content);
    }

    /**
     * Cleans up any un-executed statements
     *
     * @param Views\ITemplate $template The template to cleanup
     * @param string $content The content to cleanup
     * @return string The cleaned up contents
     */
    private function cleanupStatements(Views\ITemplate $template, $content)
    {
        // Match anything
        $closeStatementDelimiter = $template->getDelimiters(Views\ITemplate::DELIMITER_TYPE_STATEMENT)[1];
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
     * @param Views\ITemplate $template The template to compile
     * @param string $content The compiled contents
     * @return string The compiled contents
     */
    private function compileControlStructures(Views\ITemplate $template, $content)
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
     * @param Views\ITemplate $template The template to compile
     * @param string $content The compiled contents
     * @return string The compiled contents
     */
    private function compileExtendStatements(Views\ITemplate $template, $content)
    {
        /**
         * We don't want the parents to overwrite values in the child
         * So, we push a clone with all the original data to the inheritance stack
         * This way, none of the original data from the child will be overwritten by parents
         */
        $inheritanceStack = [clone $template];
        $callback = function($matches) use (&$inheritanceStack)
        {
            $parentTemplate = $this->templateFactory->create($matches[3]);
            $inheritanceStack[] = $parentTemplate;

            return $parentTemplate->getContents();
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

        // Inherit the parents' parts, tags, and vars
        // The nearest parents' tags and values take precedence over further ones
        while(count($inheritanceStack) > 0)
        {
            /** @var Views\ITemplate $parentTemplate */
            $parentTemplate = array_pop($inheritanceStack);
            $template->setParts($parentTemplate->getParts());
            $template->setTags($parentTemplate->getTags());
            $template->setVars($parentTemplate->getVars());
        }

        return $content;
    }

    /**
     * Compiles include statements
     *
     * @param Views\ITemplate $template The template to compile
     * @param string $content The compiled contents
     * @return string The compiled contents
     */
    private function compileIncludeStatements(Views\ITemplate $template, $content)
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
     * @param Views\ITemplate $template The template whose statements we're compiling
     * @param string|array $statement The statement or list of statements whose regex we are building
     * @param bool $escapeStatement Whether or not we want to escape the statement in the regex
     * @param bool $isSelfClosed Whether or not the statement is self-closed
     * @return string The regex
     */
    private function getStatementRegex(Views\ITemplate $template, $statement, $escapeStatement, $isSelfClosed)
    {
        $openStatementRegex = '(?<!%s)%s\s*(%s)\((?:(["|\'])([^\2]+)\2)?\)\s*%s';
        $closeStatementRegex = '(.*)%s\s*end\1\s*%s';
        $statementDelimiters = $template->getDelimiters(Views\ITemplate::DELIMITER_TYPE_STATEMENT);

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
        $sPrintFArgs = [
            preg_quote("\\", "/"),
            preg_quote($statementDelimiters[0], "/"),
            $statement,
            preg_quote($statementDelimiters[1], "/")
        ];

        if(!$isSelfClosed)
        {
            $regex .= $closeStatementRegex;
            $sPrintFArgs[] = preg_quote($statementDelimiters[0], "/");
            $sPrintFArgs[] = preg_quote($statementDelimiters[1], "/");
        }

        // Add the regex to the beginning of the argument list
        array_unshift($sPrintFArgs, $regex);
        $regex = call_user_func_array("sprintf", $sPrintFArgs);
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