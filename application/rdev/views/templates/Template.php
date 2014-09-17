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
    /** @var array The mapping of function names to their callbacks */
    protected $functions = [];
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
        $this->registerBuiltInFunctions();
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
     * @param string $closeTagPlaceholder
     * @throws \RuntimeException Thrown if the tags overlap with reserved tag definitions
     */
    public function setCloseTagPlaceholder($closeTagPlaceholder)
    {
        if($closeTagPlaceholder === self::SAFE_CLOSE_TAG_PLACEHOLDER
            && $this->openTagPlaceholder == self::SAFE_OPEN_TAG_PLACEHOLDER
        )
        {
            throw new \RuntimeException("Cannot use " . self::SAFE_OPEN_TAG_PLACEHOLDER
                . self::SAFE_CLOSE_TAG_PLACEHOLDER . " as placeholders because they are reserved for safe tags");
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
            throw new \RuntimeException("Cannot use " . self::SAFE_OPEN_TAG_PLACEHOLDER
                . self::SAFE_CLOSE_TAG_PLACEHOLDER . " as placeholders because they are reserved for safe tags");
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
                $content = preg_replace("/"
                    . preg_quote($this->openTagPlaceholder, "/")
                    . "\s*"
                    . preg_quote($functionName, "/")
                    . "\("
                    . "((?:(?!"
                    . "\)"
                    . "\s*"
                    . preg_quote($this->closeTagPlaceholder, "/")
                    . ").)*)"
                    . "\)"
                    . "\s*"
                    . preg_quote($this->closeTagPlaceholder, "/")
                    . "/",
                    '<?php echo call_user_func_array($this->functions["' . $functionName . '"], [\1]); ?>',
                    $content);
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
                if(isset($tagName) && $tagName[0] == $tagName[strlen($tagName) - 1]
                    && ($tagName[0] == "'" || $tagName[0] == '"')
                )
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
            if($isStrippingRegularTags && $matches[1] == self::SAFE_OPEN_TAG_PLACEHOLDER
                && $matches[4] == self::SAFE_CLOSE_TAG_PLACEHOLDER
            )
            {
                return $matches[0];
            }

            return "";
        };

        return preg_replace_callback("/"
            . "(?<!" . preg_quote("\\", "/") . ")"
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
} 