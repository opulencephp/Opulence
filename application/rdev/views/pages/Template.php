<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines methods common to all website page templates
 */
namespace RDev\Views\Pages;
use RDev\Views;
use RDev\Views\Pages\Security;

class Template implements Views\IView
{
    /** The default string used to denote the beginning of a tag name in a template */
    const DEFAULT_OPEN_TAG_PLACEHOLDER = "{{";
    /** The default string used to denote the end of a tag name in a template */
    const DEFAULT_CLOSE_TAG_PLACEHOLDER = "}}";
    /** The string used to denote the beginning of a safe tag name in a template */
    const SAFE_OPEN_TAG_PLACEHOLDER = "{{{";
    /** The string used to denote the end of a safe tag name in a template */
    const SAFE_CLOSE_TAG_PLACEHOLDER = "}}}";

    /** @var string The path to the template */
    protected $templatePath = "";
    /** @var array The mapping of tag (placeholder) names to their values */
    protected $tags = [];
    /** @var array The mapping of PHP variable names to their values */
    protected $vars = [];
    /** @var string The open tag placeholder */
    private $openTagPlaceholder = self::DEFAULT_OPEN_TAG_PLACEHOLDER;
    /** @var string The close tag placeholder */
    private $closeTagPlaceholder = self::DEFAULT_CLOSE_TAG_PLACEHOLDER;
    /** @var array The list of custom compile functions */
    private $customCompileFunctions = [];

    /**
     * @param string $templatePath The path to the template to use
     */
    public function __construct($templatePath = "")
    {
        $this->setTemplatePath($templatePath);
    }

    /**
     * @return string
     */
    public function getCloseTagPlaceholder()
    {
        return $this->closeTagPlaceholder;
    }

    /**
     * Gets the regular expression to use to match custom functions that appear in the template
     * Any parameters passed into the function in the template are backreferenced, starting with $1
     *
     * @param string $functionName The name of the function to match
     * @return string The regular expression that will match the input function
     */
    public function getFunctionMatcher($functionName)
    {
        return "/" . preg_quote($this->openTagPlaceholder, "/") .
        preg_quote($functionName, "/") .
        "\(([^,\)]+)(?:,\s*([^\)]+))?\)" .
        preg_quote($this->closeTagPlaceholder, "/") .
        "/";
    }

    /**
     * @return string
     */
    public function getOpenTagPlaceholder()
    {
        return $this->openTagPlaceholder;
    }

    /**
     * Registers a custom compiler
     *
     * @param callable $compiler The anonymous function to execute to compile custom functions inside tags
     *      The function must take in one parameter: the template contents
     *      The function must return the compile template's contents
     */
    public function registerCompiler(callable $compiler)
    {
        $this->customCompileFunctions[] = $compiler;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        return $this->compileTemplate();
    }

    /**
     * @param string $closeTagPlaceholder
     * @throws \RuntimeException Thrown if the tags overlap with reserved tag definitions
     */
    public function setCloseTagPlaceholder($closeTagPlaceholder)
    {
        if($closeTagPlaceholder === self::SAFE_CLOSE_TAG_PLACEHOLDER
            && $this->openTagPlaceholder == self::SAFE_OPEN_TAG_PLACEHOLDER
        )
        {
            throw new \RuntimeException("Cannot use " . self::SAFE_OPEN_TAG_PLACEHOLDER . self::SAFE_CLOSE_TAG_PLACEHOLDER .
                " as placeholders because they are reserved for safe tags");
        }

        $this->closeTagPlaceholder = $closeTagPlaceholder;
    }

    /**
     * @param string $openTagPlaceholder
     * @throws \RuntimeException Thrown if the tags overlap with reserved tag definitions
     */
    public function setOpenTagPlaceholder($openTagPlaceholder)
    {
        if($openTagPlaceholder === self::SAFE_OPEN_TAG_PLACEHOLDER
            && $this->closeTagPlaceholder == self::SAFE_CLOSE_TAG_PLACEHOLDER
        )
        {
            throw new \RuntimeException("Cannot use " . self::SAFE_OPEN_TAG_PLACEHOLDER . self::SAFE_CLOSE_TAG_PLACEHOLDER .
                " as placeholders because they are reserved for safe tags");
        }

        $this->openTagPlaceholder = $openTagPlaceholder;
    }

    /**
     * Sets the value for a tag in the template
     * If the value was previously set for this tag, it'll be overwritten
     *
     * @param string $name The name of the tag to replace
     * @param mixed $value The value with which to replace the tag name
     */
    public function setTag($name, $value)
    {
        $this->tags[$name] = $value;
    }

    /**
     * Sets multiple tags' values in the template
     *
     * @param array $namesToValues The mapping of tag names to their respective values
     */
    public function setTags(array $namesToValues)
    {
        foreach($namesToValues as $name => $value)
        {
            $this->setTag($name, $value);
        }
    }

    /**
     * @param string $path
     */
    public function setTemplatePath($path)
    {
        $this->templatePath = $path;
    }

    /**
     * Sets the value for a variable in the template
     *
     * @param string $name The name of the variable whose value we're setting
     *      For example, if we are setting the value of a variable named "$email" in the template, pass in "email"
     * @param mixed $value
     */
    public function setVar($name, $value)
    {
        $this->vars[$name] = $value;
    }

    /**
     * Sets multiple variables' values in the template
     *
     * @param array $namesToValues The mapping of variable names to their respective values
     */
    public function setVars(array $namesToValues)
    {
        foreach($namesToValues as $name => $value)
        {
            $this->setVar($name, $value);
        }
    }

    /**
     * Compiles the custom tags in a template
     *
     * @param string $template The template's contents
     * @return string The template with the compiled custom tags
     */
    private function compileCustomTags($template)
    {
        foreach($this->customCompileFunctions as $compileFunction)
        {
            $template = $compileFunction($template);
        }

        return $template;
    }

    /**
     * Compiles the PHP in a template
     *
     * @param string $template The template's contents
     * @return string The template with the results of the evaluated PHP code
     * @throws \RuntimeException Thrown if the template contained invalid PHP code
     */
    private function compilePHP($template)
    {
        // Create local variables for use in eval()
        foreach($this->vars as $name => $value)
        {
            ${$name} = $value;
        }

        ob_start();

        // Notice the little hack inside eval() to compile inline PHP
        if(@eval("?>" . $template) === false)
        {
            throw new \RuntimeException("Invalid PHP inside template");
        }

        return ob_get_clean();
    }

    /**
     * Compiles the regular tags in a template
     *
     * @param string $untaggedTemplate The untagged template's contents
     * @returns string The template with compiled regular tags
     */
    private function compileRegularTags($untaggedTemplate)
    {
        // Create the regexes to find regular tags with bookends
        $arrayMapCallback = function ($tagName)
        {
            return "/(?<!" . preg_quote("\\") . ")" .
            preg_quote($this->openTagPlaceholder .
                $tagName .
                $this->closeTagPlaceholder, "/") . "/U";
        };

        // Replace the tags with their values
        $regexes = array_map($arrayMapCallback, array_keys($this->tags));
        $taggedTemplate = preg_replace($regexes, array_values($this->tags), $untaggedTemplate);
        $taggedTemplate = $this->stripUnusedTags($taggedTemplate, $this->openTagPlaceholder, $this->closeTagPlaceholder);
        $taggedTemplate = $this->stripEscapeCharacters($taggedTemplate, $this->openTagPlaceholder, $this->closeTagPlaceholder);

        return $taggedTemplate;
    }

    /**
     * Compiles the safe tags in a template
     *
     * @param string $untaggedTemplate The untagged template's contents
     * @returns string The template with compiled safe tags
     */
    private function compileSafeTags($untaggedTemplate)
    {
        // Create the regexes to find safe tags with bookends
        $arrayMapCallback = function ($tagName)
        {
            return "/(?<!" . preg_quote("\\") . ")" .
            preg_quote(self::SAFE_OPEN_TAG_PLACEHOLDER, "/") .
            "(" . preg_quote($tagName, "/") . ")" .
            preg_quote(self::SAFE_CLOSE_TAG_PLACEHOLDER, "/") .
            "/U";
        };

        // Filter the values
        $regexCallback = function ($matches)
        {
            $tagName = $matches[1];

            // Check if the tag name is a string literal
            if(isset($tagName) && $tagName[0] == $tagName[strlen($tagName) - 1] && ($tagName[0] == "'" || $tagName[0] == '"'))
            {
                return Security\XSS::filter(trim($tagName, $tagName[0]));
            }

            return Security\XSS::filter($this->tags[$tagName]);
        };

        // Replace string literals
        $taggedTemplate = preg_replace_callback("/(?<!" . preg_quote("\\") . ")" .
            preg_quote(self::SAFE_OPEN_TAG_PLACEHOLDER, "/") .
            "(" .
            "(([\"'])[^\\3]*\\3)" .
            ")" .
            preg_quote(self::SAFE_CLOSE_TAG_PLACEHOLDER, "/") .
            "/U", $regexCallback, $untaggedTemplate);

        // Replace the tags with their safe values
        $regexes = array_map($arrayMapCallback, array_keys($this->tags));

        foreach($regexes as $regex)
        {
            $taggedTemplate = preg_replace_callback($regex, $regexCallback, $taggedTemplate);
        }

        $taggedTemplate = $this->stripUnusedTags($taggedTemplate, self::SAFE_OPEN_TAG_PLACEHOLDER,
            self::SAFE_CLOSE_TAG_PLACEHOLDER);
        $taggedTemplate = $this->stripEscapeCharacters($taggedTemplate, self::SAFE_OPEN_TAG_PLACEHOLDER,
            self::SAFE_CLOSE_TAG_PLACEHOLDER);

        return $taggedTemplate;
    }

    /**
     * Gets the compiled template
     *
     * @return string The compiled template
     * @throws \RuntimeException Thrown if there was an error compiling the template
     */
    private function compileTemplate()
    {
        // Order here matters
        $untaggedTemplate = file_get_contents($this->templatePath);
        $templateWithCompileCustomTags = $this->compileCustomTags($untaggedTemplate);
        $templateWithCompiledPHP = $this->compilePHP($templateWithCompileCustomTags);
        $safeTaggedTemplate = $this->compileSafeTags($templateWithCompiledPHP);
        $regularTaggedTemplate = $this->compileRegularTags($safeTaggedTemplate);

        return $regularTaggedTemplate;
    }

    /**
     * Removes the escape character from all the input tags
     *
     * @param string $template The template whose escape characters we want to remove
     * @param string $openTagPlaceholder The open tag placeholder
     * @param string $closeTagPlaceholder The close tag placeholder
     * @return string The template without the tag escape characters
     */
    private function stripEscapeCharacters($template, $openTagPlaceholder, $closeTagPlaceholder)
    {
        return preg_replace("/" .
            preg_quote("\\") .
            "(" .
            preg_quote($openTagPlaceholder, "/") .
            "((?!" . preg_quote($closeTagPlaceholder, "/") . ").)*" .
            preg_quote($closeTagPlaceholder, "/") .
            ")" .
            "/U", "$1", $template);
    }

    /**
     * Removes any unused tags from the template
     *
     * @param string $template The template whose empty tags we want to remove
     * @param string $openTagPlaceholder The open tag placeholder
     * @param string $closeTagPlaceholder The close tag placeholder
     * @return string The template without the unused tags
     */
    private function stripUnusedTags($template, $openTagPlaceholder, $closeTagPlaceholder)
    {
        $isStrippingRegularTags = $openTagPlaceholder == $this->openTagPlaceholder
            && $closeTagPlaceholder == $this->closeTagPlaceholder;
        $safeOpenTagFirstChar = substr(self::SAFE_OPEN_TAG_PLACEHOLDER, 0, 1);
        $safeCloseTagLastChar = substr(self::SAFE_CLOSE_TAG_PLACEHOLDER, -1);

        $callback = function ($matches) use ($isStrippingRegularTags, $safeOpenTagFirstChar, $safeCloseTagLastChar)
        {
            // If we are stripping regular tags, make sure to not strip safe tags
            if($isStrippingRegularTags && $matches[1] == self::SAFE_OPEN_TAG_PLACEHOLDER && $matches[4] == self::SAFE_CLOSE_TAG_PLACEHOLDER)
            {
                return $matches[0];
            }

            return "";
        };

        return preg_replace_callback("/" .
            "(?<!" . preg_quote("\\") . ")" .
            "(" .
            "(" . preg_quote($safeOpenTagFirstChar, "/") . ")?" .
            preg_quote($openTagPlaceholder, "/") .
            ")" .
            "((?!" . preg_quote($closeTagPlaceholder, "/") . ").)*" .
            "(" .
            preg_quote($closeTagPlaceholder, "/") .
            "(" . preg_quote($safeCloseTagLastChar, "/") . ")?" .
            ")" .
            "/", $callback, $template);
    }
} 