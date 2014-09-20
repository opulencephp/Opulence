<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines methods common to all website page files
 */
namespace RDev\Views\Templates;
use RDev\Models\Files;
use RDev\Views;
use RDev\Views\Security;

class Template implements Views\IView
{
    /** The default string used to denote the beginning of a tag name in a template */
    const DEFAULT_UNESCAPED_OPEN_TAG_PLACEHOLDER = "{{!";
    /** The default string used to denote the end of a tag name in a template */
    const DEFAULT_UNESCAPED_CLOSE_TAG_PLACEHOLDER = "!}}";
    /** The string used to denote the beginning of an escaped tag name in a template */
    const DEFAULT_ESCAPED_OPEN_TAG_PLACEHOLDER = "{{";
    /** The string used to denote the end of an escaped tag name in a template */
    const DEFAULT_ESCAPED_CLOSE_TAG_PLACEHOLDER = "}}";

    /** @var string The unrendered contents of the template */
    protected $unrenderedTemplate = "";
    /** @var ICompiler The compiler to use to compile the template */
    protected $compiler = null;
    /** @var array The mapping of tag (placeholder) names to their values */
    protected $tags = [];
    /** @var array The mapping of PHP variable names to their values */
    protected $vars = [];
    /** @var array The mapping of function names to their callbacks */
    protected $functions = [];
    /** @var string The unescaped open tag placeholder */
    private $unescapedOpenTagPlaceholder = self::DEFAULT_UNESCAPED_OPEN_TAG_PLACEHOLDER;
    /** @var string The unescaped close tag placeholder */
    private $unescapedCloseTagPlaceholder = self::DEFAULT_UNESCAPED_CLOSE_TAG_PLACEHOLDER;
    /** @var string The escaped open tag placeholder */
    private $escapedOpenTagPlaceholder = self::DEFAULT_ESCAPED_OPEN_TAG_PLACEHOLDER;
    /** @var string The escaped close tag placeholder */
    private $escapedCloseTagPlaceholder = self::DEFAULT_ESCAPED_CLOSE_TAG_PLACEHOLDER;
    /** @var Files\FileSystem The file system to use to read/write to files */
    private $fileSystem = null;

    /**
     * @param ICompiler $compiler The compiler to use in this template
     */
    public function __construct(ICompiler $compiler = null)
    {
        if($compiler === null)
        {
            $compiler = new Compiler();
        }

        $this->setCompiler($compiler);
        $this->fileSystem = new Files\FileSystem();

        // Order here matters
        $this->registerPHPCompiler();
        $this->registerTagCompiler();
        $this->registerTagCleanupCompiler();
        $this->registerBuiltInFunctions();
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
    public function getEscapedCloseTagPlaceholder()
    {
        return $this->escapedCloseTagPlaceholder;
    }

    /**
     * @return string
     */
    public function getEscapedOpenTagPlaceholder()
    {
        return $this->escapedOpenTagPlaceholder;
    }

    /**
     * @return string
     */
    public function getUnescapedCloseTagPlaceholder()
    {
        return $this->unescapedCloseTagPlaceholder;
    }

    /**
     * @return string
     */
    public function getUnescapedOpenTagPlaceholder()
    {
        return $this->unescapedOpenTagPlaceholder;
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
        $this->unrenderedTemplate = $this->fileSystem->read($path);
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
     * @param string $functionName The name of the function as it'll appear in the template
     * @param callable $function The function that returns the replacement string for the function in the template
     *      It must accept one parameter (the template's contents) and return a printable value
     */
    public function registerFunction($functionName, callable $function)
    {
        $this->functions[$functionName] = $function;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        return $this->compiler->compile($this->unrenderedTemplate);
    }

    /**
     * @param ICompiler $compiler
     */
    public function setCompiler(ICompiler $compiler)
    {
        $this->compiler = $compiler;
    }

    /**
     * @param string $escapedCloseTagPlaceholder
     */
    public function setEscapedCloseTagPlaceholder($escapedCloseTagPlaceholder)
    {
        $this->escapedCloseTagPlaceholder = $escapedCloseTagPlaceholder;
    }

    /**
     * @param string $escapedOpenTagPlaceholder
     */
    public function setEscapedOpenTagPlaceholder($escapedOpenTagPlaceholder)
    {
        $this->escapedOpenTagPlaceholder = $escapedOpenTagPlaceholder;
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
     * @param string $unescapedCloseTagPlaceholder
     */
    public function setUnescapedCloseTagPlaceholder($unescapedCloseTagPlaceholder)
    {
        $this->unescapedCloseTagPlaceholder = $unescapedCloseTagPlaceholder;
    }

    /**
     * @param string $unescapedOpenTagPlaceholder
     */
    public function setUnescapedOpenTagPlaceholder($unescapedOpenTagPlaceholder)
    {
        $this->unescapedOpenTagPlaceholder = $unescapedOpenTagPlaceholder;
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
     * Registers the built-in function compilers
     */
    private function registerBuiltInFunctions()
    {
        // Register the absolute value function
        $this->registerFunction("abs", function ($number)
        {
            return abs($number);
        });
        // Register the ceiling function
        $this->registerFunction("ceil", function ($number)
        {
            return ceil($number);
        });
        // Register the count function
        $this->registerFunction("count", function (array $array)
        {
            return count($array);
        });
        // Register the date function
        $this->registerFunction('date', function (\DateTime $date, $format = "m/d/Y", $timeZone = null)
        {
            if($timeZone instanceof \DateTimeZone)
            {
                $date->setTimezone($timeZone);
            }

            return $date->format($format);
        });
        // Register the floor function
        $this->registerFunction("floor", function ($number)
        {
            return floor($number);
        });
        // Register the implode function
        $this->registerFunction("implode", function ($glue, array $pieces)
        {
            return implode($glue, $pieces);
        });
        // Register the JSON encode function
        $this->registerFunction("json_encode", function ($value, $options = 0, $depth = 512)
        {
            return json_encode($value, $options, $depth);
        });
        // Register the lowercase first function
        $this->registerFunction("lcfirst", function ($string)
        {
            return lcfirst($string);
        });
        // Register the round function
        $this->registerFunction("round", function ($number, $precision = 0, $mode = PHP_ROUND_HALF_UP)
        {
            return round($number, $precision, $mode);
        });
        // Register the lowercase function
        $this->registerFunction("strtolower", function ($string)
        {
            return strtolower($string);
        });
        // Register the lowercase function
        $this->registerFunction("strtoupper", function ($string)
        {
            return strtoupper($string);
        });
        // Register the substring function
        $this->registerFunction("substr", function ($string, $start, $length = null)
        {
            if($length === null)
            {
                return substr($string, $start);
            }

            return substr($string, $start, $length);
        });
        // Register the trim function
        $this->registerFunction("trim", function ($string, $characterMask = " \t\n\r\0\x0B")
        {
            return trim($string, $characterMask);
        });
        // Register the uppercase first function
        $this->registerFunction("ucfirst", function ($string)
        {
            return ucfirst($string);
        });
        // Register the uppercase words function
        $this->registerFunction("ucwords", function ($string)
        {
            return ucwords($string);
        });
        // Register the URL decode function
        $this->registerFunction("urldecode", function ($string)
        {
            return urldecode($string);
        });
        // Register the URL encode function
        $this->registerFunction("urlencode", function ($string)
        {
            return urlencode($string);
        });
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

            // Compile the functions
            foreach($this->functions as $functionName => $callback)
            {
                $regex = "/%s\s*%s\(((?:(?!\)\s*%s).)*)\)\s*%s/";
                $replacementString = '<?php echo call_user_func_array($this->functions["' . $functionName . '"], [\1]); ?>';
                // Replace function calls in escaped tags
                $content = preg_replace(
                    sprintf(
                        $regex,
                        preg_quote($this->escapedOpenTagPlaceholder, "/"),
                        preg_quote($functionName, "/"),
                        preg_quote($this->escapedCloseTagPlaceholder, "/"),
                        preg_quote($this->escapedCloseTagPlaceholder, "/")),
                    $replacementString,
                    $content
                );
                // Replace function calls in unescaped tags
                $content = preg_replace(
                    sprintf(
                        $regex,
                        preg_quote($this->unescapedOpenTagPlaceholder, "/"),
                        preg_quote($functionName, "/"),
                        preg_quote($this->unescapedCloseTagPlaceholder, "/"),
                        preg_quote($this->unescapedCloseTagPlaceholder, "/")),
                    $replacementString,
                    $content
                );
            }

            // Notice the little hack inside eval() to compile inline PHP
            if(@eval("?>" . $content) === false)
            {
                ob_end_clean();
                throw new \RuntimeException("Invalid PHP inside template");
            }

            return ob_get_clean();
        });
    }

    /**
     * Registers the compiler that cleans up unused tags and escape characters before tags in a template
     * Cannot just use this method as the compiler and register it because it is private
     */
    private function registerTagCleanupCompiler()
    {
        $this->compiler->registerCompiler(function ($content)
        {
            // Holds the tags, with the longest-length opening tag first
            $tags = [];

            // In the case that one open tag is a substring of another (eg "{{" and "{{{"), handle the longer one first
            // If they're the same length, they cannot be substrings of one another unless they're equal
            if(strlen($this->escapedOpenTagPlaceholder) > strlen($this->unescapedOpenTagPlaceholder))
            {
                $tags[] = [$this->escapedOpenTagPlaceholder, $this->escapedCloseTagPlaceholder];
                $tags[] = [$this->unescapedOpenTagPlaceholder, $this->unescapedCloseTagPlaceholder];
            }
            else
            {
                $tags[] = [$this->unescapedOpenTagPlaceholder, $this->unescapedCloseTagPlaceholder];
                $tags[] = [$this->escapedOpenTagPlaceholder, $this->escapedCloseTagPlaceholder];
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
        });
    }

    /**
     * Registers the compiler of tags in a template
     * Cannot just use this method as the compiler and register it because it is private
     */
    private function registerTagCompiler()
    {
        $this->compiler->registerCompiler(function ($content)
        {
            // Holds the tags as well as the callbacks to callbacks to execute in the case of string literals or tag names
            $tagData = [
                [
                    "tags" => [$this->escapedOpenTagPlaceholder, $this->escapedCloseTagPlaceholder],
                    "stringLiteralCallback" => function ($stringLiteral)
                    {
                        return Security\XSS::filter(trim($stringLiteral, $stringLiteral[0]));
                    },
                    "tagNameCallback" => function ($tagName)
                    {
                        return Security\XSS::filter($this->tags[$tagName]);
                    }
                ],
                [
                    "tags" => [$this->unescapedOpenTagPlaceholder, $this->unescapedCloseTagPlaceholder],
                    "stringLiteralCallback" => function ($stringLiteral)
                    {
                        return trim($stringLiteral, $stringLiteral[0]);
                    },
                    "tagNameCallback" => function ($tagName)
                    {
                        return $this->tags[$tagName];
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
                $stringLiteralRegex = "/(?<!%s)%s\s*((([\"'])[^\\3]*\\3))\s*%s/U";
                $content = preg_replace_callback(
                    sprintf(
                        $stringLiteralRegex,
                        preg_quote("\\", "/"),
                        preg_quote($tagDataByType["tags"][0], "/"),
                        preg_quote($tagDataByType["tags"][1], "/")
                    ),
                    $regexCallback,
                    $content
                );

                // Replace the tags with their values
                $regexes = array_map($arrayMapCallback, array_keys($this->tags));
                $content = preg_replace_callback($regexes, $regexCallback, $content);
            }

            return $content;
        });
    }
} 