<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines methods common to all website page files
 */
namespace RDev\Views\Templates;
use RDev\Views;
use RDev\Views\Security;

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

    /** @var string The unrendered contents of the template */
    protected $unrenderedTemplate = "";
    /** @var ICompiler The compiler to use to compile the template */
    protected $compiler = null;
    /** @var array The mapping of tag (placeholder) names to their values */
    protected $tags = [];
    /** @var array The mapping of PHP variable names to their values */
    protected $vars = [];
    /** @var string The open tag placeholder */
    private $openTagPlaceholder = self::DEFAULT_OPEN_TAG_PLACEHOLDER;
    /** @var string The close tag placeholder */
    private $closeTagPlaceholder = self::DEFAULT_CLOSE_TAG_PLACEHOLDER;

    /**
     * @param ICompiler $compiler The compiler to use in this template
     */
    public function __construct(ICompiler $compiler = null)
    {
        if($compiler === null)
        {
            $compiler = new Compiler();
        }

        $this->compiler = $compiler;
        // Order here matters
        $this->registerPHPCompiler();
        $this->registerSafeTagCompiler();
        $this->registerRegularTagCompiler();
    }

    /**
     * @return string
     */
    public function getCloseTagPlaceholder()
    {
        return $this->closeTagPlaceholder;
    }

    /**
     * @return ICompiler
     */
    public function getCompiler()
    {
        return $this->compiler;
    }

    /**
     * @return string
     */
    public function getOpenTagPlaceholder()
    {
        return $this->openTagPlaceholder;
    }

    /**
     * @return string
     */
    public function getUnrenderedTemplate()
    {
        return $this->unrenderedTemplate;
    }

    /**
     * Reads the contents of a template file
     * Note that this will overwrite any template set in any readFrom* method
     *
     * @param string $path The path to the template to read from
     * @throws \InvalidArgumentException Thrown if the path is not a string
     * @throws \RuntimeException Thrown if the path does not exist or is not readable
     */
    public function readFromFile($path)
    {
        if(!is_string($path))
        {
            throw new \InvalidArgumentException("Path is not a string");
        }

        if(!file_exists($path) || !is_readable($path))
        {
            throw new \RuntimeException("Couldn't read from path \"$path\"");
        }

        $contents = file_get_contents($path);

        if($contents === false)
        {
            throw new \RuntimeException("Couldn't read from path \"$path\"");
        }

        $this->unrenderedTemplate = $contents;
    }

    /**
     * Uses the contents of the input as the template
     * Note that this will overwrite any template set in any readFrom* method
     *
     * @param string $input The template's contents
     * @throws \InvalidArgumentException Thrown if the input is not a string
     */
    public function readFromInput($input)
    {
        if(!is_string($input))
        {
            throw new \InvalidArgumentException("Input is not a string");
        }

        $this->unrenderedTemplate = $input;
    }

    /**
     * Registers a custom function to our compiler
     * Useful for defining functions for consistent formatting in the template
     *
     * @param string $functionName The name of the function to call
     * @param callable $compiler The function that returns the string that will replace calls to the function in the template
     *      If the replacement string executes some PHP, it should be surrounded by PHP open/close tags
     */
    public function registerFunction($functionName, callable $compiler)
    {
        // Notice that the compiler will have priority over other compilers
        $this->compiler->registerCompiler(function ($content) use ($functionName, $compiler)
        {
            $callback = function ($matches) use ($compiler)
            {
                // Get rid of the subject
                array_shift($matches);
                ob_start();

                if(count($matches) == 1)
                {
                    // Grab any of the parameters from the function and convert them to their actual values
                    $parameters = $this->tokenizeFunctionParameters($matches[0]);
                    array_walk($parameters, [$this, "getVarValue"]);
                    call_user_func_array($compiler, $parameters);
                }
                else
                {
                    call_user_func($compiler);
                }

                return ob_get_clean();
            };

            return preg_replace_callback("/"
                . preg_quote($this->openTagPlaceholder, "/")
                . "\s*"
                . preg_quote($functionName, "/")
                . "\(([^\)]*)\)"
                . "\s*"
                . preg_quote($this->closeTagPlaceholder, "/")
                . "/",
                $callback,
                $content
            );
        }, true);
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        return $this->compiler->compile($this->unrenderedTemplate);
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
     * @param ICompiler $compiler
     */
    public function setCompiler(ICompiler $compiler)
    {
        $this->compiler = $compiler;
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
     * Gets the value for a variable set in this template
     *
     * @param string $var The variable whose value we want
     *      This variable should be wrapped in quotes, and it will be evaluated
     *      For example, if the string "[1, 2]" is passed in, it'll be evaluated to an actual array: [1, 2]
     *      Likewise, if a string '"m/d/Y"' is passed in, it'll be evaluated to a string: "m/d/Y"
     *      If the string looks like "$today", this will evaluate it as a variable set in this template named "today"
     */
    private function getVarValue(&$var)
    {
        // If it's pointing to a variable
        if(substr($var, 0, 1) == "$")
        {
            $var = trim($var, "$");

            if(isset($this->vars[$var]))
            {
                $var = $this->vars[$var];
            }
        }
        else
        {
            // It's pointing to a data type
            $var = eval("return " . $var . ";");
        }
    }

    /**
     * Registers the PHP compiler
     * Cannot just use this method as the compiler and register it because it is private
     */
    private function registerPHPCompiler()
    {
        $this->compiler->registerCompiler(function ($content)
        {
            // Create local variables for use in eval()
            foreach($this->vars as $name => $value)
            {
                ${$name} = $value;
            }

            ob_start();

            // Notice the little hack inside eval() to compile inline PHP
            if(@eval("?>" . $content) === false)
            {
                throw new \RuntimeException("Invalid PHP inside template");
            }

            return ob_get_clean();
        });
    }

    /**
     * Registers the compiler of the regular tags in a template
     * Cannot just use this method as the compiler and register it because it is private
     */
    private function registerRegularTagCompiler()
    {
        $this->compiler->registerCompiler(function ($content)
        {
            // Create the regexes to find regular tags with bookends
            $arrayMapCallback = function ($tagName)
            {
                return "/(?<!" . preg_quote("\\") . ")"
                . preg_quote($this->openTagPlaceholder, "/")
                . "\s*"
                . preg_quote($tagName, "/")
                . "\s*"
                . preg_quote($this->closeTagPlaceholder, "/") . "/U";
            };

            // Replace the tags with their values
            $regexes = array_map($arrayMapCallback, array_keys($this->tags));
            $content = preg_replace($regexes, array_values($this->tags), $content);
            $content = $this->stripUnusedTags($content, $this->openTagPlaceholder, $this->closeTagPlaceholder);
            $content = $this->stripEscapeCharacters($content, $this->openTagPlaceholder, $this->closeTagPlaceholder);

            return $content;
        });
    }

    /**
     * Registers the compiler of safe tags in a template
     * Cannot just use this method as the compiler and register it because it is private
     */
    private function registerSafeTagCompiler()
    {
        $this->compiler->registerCompiler(function ($content)
        {
            // Create the regexes to find safe tags with bookends
            $arrayMapCallback = function ($tagName)
            {
                return "/(?<!" . preg_quote("\\") . ")"
                . preg_quote(self::SAFE_OPEN_TAG_PLACEHOLDER, "/")
                . "\s*"
                . "(" . preg_quote($tagName, "/") . ")"
                . "\s*"
                . preg_quote(self::SAFE_CLOSE_TAG_PLACEHOLDER, "/")
                . "/U";
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
            $taggedTemplate = preg_replace_callback("/(?<!" . preg_quote("\\") . ")"
                . preg_quote(self::SAFE_OPEN_TAG_PLACEHOLDER, "/")
                . "\s*"
                . "("
                . "(([\"'])[^\\3]*\\3)"
                . ")"
                . "\s*"
                . preg_quote(self::SAFE_CLOSE_TAG_PLACEHOLDER, "/")
                . "/U",
                $regexCallback,
                $content);

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
        });
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
        return preg_replace("/"
            . preg_quote("\\")
            . "("
            . preg_quote($openTagPlaceholder, "/")
            . "\s*"
            . "((?!" . preg_quote($closeTagPlaceholder, "/") . ").)*"
            . "\s*"
            . preg_quote($closeTagPlaceholder, "/")
            . ")"
            . "/U",
            "$1",
            $template);
    }

    /**
     * Removes any unused tags from the template
     *
     * @param string $content The template's contents whose empty tags we want to remove
     * @param string $openTagPlaceholder The open tag placeholder
     * @param string $closeTagPlaceholder The close tag placeholder
     * @return string The template without the unused tags
     */
    private function stripUnusedTags($content, $openTagPlaceholder, $closeTagPlaceholder)
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

        return preg_replace_callback("/"
            . "(?<!" . preg_quote("\\") . ")"
            . "("
            . "(" . preg_quote($safeOpenTagFirstChar, "/") . ")?"
            . preg_quote($openTagPlaceholder, "/")
            . ")"
            . "((?!" . preg_quote($closeTagPlaceholder, "/") . ").)*"
            . "("
            . preg_quote($closeTagPlaceholder, "/")
            . "(" . preg_quote($safeCloseTagLastChar, "/") . ")?"
            . ")"
            . "/",
            $callback,
            $content);
    }

    /**
     * Tokenizes a list of parameters for a function in a template
     *
     * @param string $rawParameters The string of comma-separated parameters to tokenize
     * @return array The list of function parameters
     */
    private function tokenizeFunctionParameters($rawParameters)
    {
        $bracketDepth = 0;
        $commaDepth = 0;
        $parameters = [];
        $buffer = "";
        $parametersLen = strlen($rawParameters);

        for($charIter = 0;$charIter < $parametersLen;$charIter++)
        {
            $char = $rawParameters[$charIter];

            switch($char)
            {
                case '[':
                    $bracketDepth++;
                    break;
                case '(':
                    $commaDepth++;
                    break;
                case ',':
                    if($commaDepth == 0 && $bracketDepth == 0)
                    {
                        if($buffer != '')
                        {
                            $parameters[] = $buffer;
                            $buffer = '';
                        }

                        continue 2;
                    }

                    break;
                case ' ':
                    if($commaDepth == 0 && $bracketDepth == 0)
                    {
                        continue 2;
                    }

                    break;
                case ']':
                    if($bracketDepth == 0)
                    {
                        $parameters[] = $buffer . $char;
                        $buffer = '';

                        continue 2;
                    }
                    else
                    {
                        $bracketDepth--;
                    }

                    break;
                case ')':
                    if($commaDepth == 0)
                    {
                        $parameters[] = $buffer . $char;
                        $buffer = '';

                        continue 2;
                    }
                    else
                    {
                        $commaDepth--;
                    }

                    break;
            }

            $buffer .= $char;
        }

        if(!empty($buffer))
        {
            $parameters[] = $buffer;
        }

        return $parameters;
    }
} 