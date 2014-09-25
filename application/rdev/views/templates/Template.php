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
    /** The default open tag for unescaped tags  */
    const DEFAULT_UNESCAPED_OPEN_TAG = "{{!";
    /** The default close tag for unescaped tags  */
    const DEFAULT_UNESCAPED_CLOSE_TAG = "!}}";
    /** The default open tag for escaped tags  */
    const DEFAULT_ESCAPED_OPEN_TAG = "{{";
    /** The default close tag for escaped tags */
    const DEFAULT_ESCAPED_CLOSE_TAG = "}}";

    /** @var string The unrendered contents of the template */
    protected $unrenderedTemplate = "";
    /** @var ICompiler The compiler to use to compile the template */
    protected $compiler = null;
    /** @var array The mapping of tag names to their values */
    protected $tags = [];
    /** @var array The mapping of PHP variable names to their values */
    protected $vars = [];
    /** @var array The mapping of function names to their callbacks */
    protected $functions = [];
    /** @var string The unescaped open tag */
    private $unescapedOpenTag = self::DEFAULT_UNESCAPED_OPEN_TAG;
    /** @var string The unescaped close tag */
    private $unescapedCloseTag = self::DEFAULT_UNESCAPED_CLOSE_TAG;
    /** @var string The escaped open tag */
    private $escapedOpenTag = self::DEFAULT_ESCAPED_OPEN_TAG;
    /** @var string The escaped close tag */
    private $escapedCloseTag = self::DEFAULT_ESCAPED_CLOSE_TAG;
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
    public function getEscapedCloseTag()
    {
        return $this->escapedCloseTag;
    }

    /**
     * @return string
     */
    public function getEscapedOpenTag()
    {
        return $this->escapedOpenTag;
    }

    /**
     * @return string
     */
    public function getUnescapedCloseTag()
    {
        return $this->unescapedCloseTag;
    }

    /**
     * @return string
     */
    public function getUnescapedOpenTag()
    {
        return $this->unescapedOpenTag;
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
     * @param string $escapedCloseTag
     */
    public function setEscapedCloseTag($escapedCloseTag)
    {
        $this->escapedCloseTag = $escapedCloseTag;
    }

    /**
     * @param string $escapedOpenTag
     */
    public function setEscapedOpenTag($escapedOpenTag)
    {
        $this->escapedOpenTag = $escapedOpenTag;
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
     * @param string $unescapedCloseTag
     */
    public function setUnescapedCloseTag($unescapedCloseTag)
    {
        $this->unescapedCloseTag = $unescapedCloseTag;
    }

    /**
     * @param string $unescapedOpenTag
     */
    public function setUnescapedOpenTag($unescapedOpenTag)
    {
        $this->unescapedOpenTag = $unescapedOpenTag;
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
        $this->registerFunction("date", function ($format, $timestamp = null)
        {
            if($timestamp === null)
            {
                $timestamp = time();
            }

            return date($format, $timestamp);
        });
        // Register the floor function
        $this->registerFunction("floor", function ($number)
        {
            return floor($number);
        });
        // Register the format DateTime function
        $this->registerFunction('formatDateTime', function (\DateTime $date, $format = "m/d/Y", $timeZone = null)
        {
            if(is_string($timeZone) && in_array($timeZone, \DateTimeZone::listIdentifiers()))
            {
                $timeZone = new \DateTimeZone($timeZone);
            }

            if($timeZone instanceof \DateTimeZone)
            {
                $date->setTimezone($timeZone);
            }

            return $date->format($format);
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
            extract($this->vars);

            $startOBLevel = ob_get_level();
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
                        preg_quote($this->escapedOpenTag, "/"),
                        preg_quote($functionName, "/"),
                        preg_quote($this->escapedCloseTag, "/"),
                        preg_quote($this->escapedCloseTag, "/")),
                    $replacementString,
                    $content
                );
                // Replace function calls in unescaped tags
                $content = preg_replace(
                    sprintf(
                        $regex,
                        preg_quote($this->unescapedOpenTag, "/"),
                        preg_quote($functionName, "/"),
                        preg_quote($this->unescapedCloseTag, "/"),
                        preg_quote($this->unescapedCloseTag, "/")),
                    $replacementString,
                    $content
                );
            }

            // Notice the little hack inside eval() to compile inline PHP
            if(@eval("?>" . $content) === false)
            {
                // Prevent an invalid template from displaying
                while(ob_get_level() > $startOBLevel)
                {
                    ob_end_clean();
                }

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
            if(strlen($this->escapedOpenTag) > strlen($this->unescapedOpenTag))
            {
                $tags[] = [$this->escapedOpenTag, $this->escapedCloseTag];
                $tags[] = [$this->unescapedOpenTag, $this->unescapedCloseTag];
            }
            else
            {
                $tags[] = [$this->unescapedOpenTag, $this->unescapedCloseTag];
                $tags[] = [$this->escapedOpenTag, $this->escapedCloseTag];
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
                    "tags" => [$this->escapedOpenTag, $this->escapedCloseTag],
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
                    "tags" => [$this->unescapedOpenTag, $this->unescapedCloseTag],
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