<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the RDev tag sub-compiler
 */
namespace RDev\Views\Compilers\SubCompilers;
use RDev\Views;
use RDev\Views\Compilers;
use RDev\Views\Filters;

class Tag extends SubCompiler
{
    /** @var Filters\IFilter The cross-site scripting filter */
    private $xssFilter = null;

    /**
     * {@inheritdoc}
     * @param Filters\IFilter $xssFilter The cross-site scripting filter
     */
    public function __construct(Compilers\ICompiler $parentCompiler, Filters\IFilter $xssFilter)
    {
        parent::__construct($parentCompiler);

        $this->xssFilter = $xssFilter;
    }

    /**
     * {@inheritdoc}
     */
    public function compile(Views\ITemplate $template, $content)
    {
        $content = $this->compileTags($template, $content);

        return $this->cleanupTags($template, $content);
    }

    /**
     * Cleans up unused tags and escape characters before tags in a template
     *
     * @param Views\ITemplate $template The template whose tags we're compiling
     * @param string $content The actual content to compile
     * @return string The compiled template
     */
    private function cleanupTags(Views\ITemplate $template, $content)
    {
        // Holds the tags, with the longest-length opening tag first
        $tags = [];

        // In the case that one open tag is a substring of another (eg "{{" and "{{{"), handle the longer one first
        // If they're the same length, they cannot be substrings of one another unless they're equal
        if(strlen($template->getEscapedOpenTag()) > strlen($template->getUnescapedOpenTag()))
        {
            $tags[] = [$template->getEscapedOpenTag(), $template->getEscapedCloseTag()];
            $tags[] = [$template->getUnescapedOpenTag(), $template->getUnescapedCloseTag()];
        }
        else
        {
            $tags[] = [$template->getUnescapedOpenTag(), $template->getUnescapedCloseTag()];
            $tags[] = [$template->getEscapedOpenTag(), $template->getEscapedCloseTag()];
        }

        /**
         * The reason we cannot combine this loop and the next is that we must remove all unused tags before
         * removing their escape characters
         */
        foreach($tags as $tagsByType)
        {
            // Remove unused tags
            $content = preg_replace(
                sprintf(
                    "/(?<!%s)%s((?!%s).)*%s/",
                    preg_quote("\\", "/"),
                    preg_quote($tagsByType[0], "/"),
                    preg_quote($tagsByType[1], "/"),
                    preg_quote($tagsByType[1], "/")
                ),
                "",
                $content
            );
        }

        foreach($tags as $tagsByType)
        {
            // Remove the escape character (eg "\" from "\{{foo}}")
            $content = preg_replace(
                sprintf(
                    "/%s(%s\s*((?!%s).)*\s*%s)/U",
                    preg_quote("\\", "/"),
                    preg_quote($tagsByType[0], "/"),
                    preg_quote($tagsByType[1], "/"),
                    preg_quote($tagsByType[1], "/")
                ),
                "$1",
                $content
            );
        }

        return $content;
    }

    /**
     * Compiles tags in a template
     *
     * @param Views\ITemplate $template The template whose tags we're compiling
     * @param string $content The actual content to compile
     * @return string The compiled template
     */
    private function compileTags(Views\ITemplate $template, $content)
    {
        // Holds the tags as well as the callbacks to callbacks to execute in the case of string literals or tag names
        $tagData = [
            [
                "tags" => [$template->getEscapedOpenTag(), $template->getEscapedCloseTag()],
                "stringLiteralCallback" => function ($stringLiteral) use ($template)
                {
                    return $this->xssFilter->run(trim($stringLiteral, $stringLiteral[0]));
                },
                "tagNameCallback" => function ($tagName) use ($template)
                {
                    return $this->xssFilter->run($template->getTag($tagName));
                }
            ],
            [
                "tags" => [$template->getUnescapedOpenTag(), $template->getUnescapedCloseTag()],
                "stringLiteralCallback" => function ($stringLiteral) use ($template)
                {
                    return trim($stringLiteral, $stringLiteral[0]);
                },
                "tagNameCallback" => function ($tagName) use ($template)
                {
                    return $template->getTag($tagName);
                }
            ]
        ];

        foreach($tagData as $tagDataByType)
        {
            // Create the regexes to find escaped tags with bookends
            $arrayMapCallback = function ($tagName) use ($content, $tagDataByType)
            {
                return sprintf(
                    "/(?<!%s)%s\s*(%s)\s*%s/U",
                    preg_quote("\\", "/"),
                    preg_quote($tagDataByType["tags"][0], "/"),
                    preg_quote($tagName, "/"),
                    preg_quote($tagDataByType["tags"][1], "/")
                );
            };

            // Filter the values
            $regexCallback = function ($matches) use ($tagDataByType)
            {
                $tagName = $matches[1];

                // If the tag name is a string literal
                if(isset($tagName) && $tagName[0] == $tagName[strlen($tagName) - 1]
                    && ($tagName[0] == "'" || $tagName[0] == '"')
                )
                {
                    return call_user_func_array($tagDataByType["stringLiteralCallback"], [$tagName]);
                }

                return call_user_func_array($tagDataByType["tagNameCallback"], [$tagName]);
            };

            // Replace string literals
            $content = preg_replace_callback(
                sprintf(
                    "/(?<!%s)%s\s*((([\"'])[^\\3]*\\3))\s*%s/U",
                    preg_quote("\\", "/"),
                    preg_quote($tagDataByType["tags"][0], "/"),
                    preg_quote($tagDataByType["tags"][1], "/")
                ),
                $regexCallback,
                $content
            );

            // Replace the tags with their values
            $regexes = array_map($arrayMapCallback, array_keys($template->getTags()));
            $content = preg_replace_callback($regexes, $regexCallback, $content);
        }

        return $content;
    }
}