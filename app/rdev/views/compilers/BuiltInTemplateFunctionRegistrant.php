<?php
/**
 * Copyright (C) 2014 David Young
 * 
 * Defines the registrant that creates the built-in functions
 */
namespace RDev\Views\Compilers;
use RDev\Views;

class BuiltInTemplateFunctionRegistrant
{
    private $parts = [];

    /**
     * Registers the built-in template functions
     * 
     * @param ICompiler $compiler The compiler to register to
     */
    public function registerTemplateFunctions(ICompiler &$compiler)
    {
        // Register the absolute value function
        $compiler->registerTemplateFunction("abs", function (Views\ITemplate $template, $number)
        {
            return abs($number);
        });
        // Register the ceiling function
        $compiler->registerTemplateFunction("ceil", function (Views\ITemplate $template, $number)
        {
            return ceil($number);
        });
        // Register the charset function
        $compiler->registerTemplateFunction("charset", function (Views\ITemplate $template, $charset)
        {
            return '<meta charset="' . $charset . '">';
        });
        // Register the CSS function
        $compiler->registerTemplateFunction("css", function (Views\ITemplate $template, $paths)
        {
            if(!is_array($paths))
            {
                $paths = [$paths];
            }

            $callback = function($path)
            {
                return '<link href="' . $path . '" rel="stylesheet">';
            };

            return implode("\n", array_map($callback, $paths));
        });
        // Register the count function
        $compiler->registerTemplateFunction("count", function (Views\ITemplate $template, array $array)
        {
            return count($array);
        });
        // Register the date function
        $compiler->registerTemplateFunction("date", function (Views\ITemplate $template, $format, $timestamp = null)
        {
            if($timestamp === null)
            {
                $timestamp = time();
            }

            return date($format, $timestamp);
        });
        // Register the favicon function
        $compiler->registerTemplateFunction("favicon", function (Views\ITemplate $template, $path)
        {
            return '<link href="' . $path . '" rel="shortcut icon">';
        });
        // Register the floor function
        $compiler->registerTemplateFunction("floor", function (Views\ITemplate $template, $number)
        {
            return floor($number);
        });
        // Register the format DateTime function
        $compiler->registerTemplateFunction('formatDateTime',
            function (Views\ITemplate $template, \DateTime $date, $format = "m/d/Y", $timeZone = null)
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
            }
        );
        // Register the HTTP-equiv function
        $compiler->registerTemplateFunction("httpEquiv", function (Views\ITemplate $template, $name, $value)
        {
            return '<meta http-equiv="' . htmlentities($name) . '" content="' . htmlentities($value) . '">';
        });
        // Register the implode function
        $compiler->registerTemplateFunction("implode", function (Views\ITemplate $template, $glue, array $pieces)
        {
            return implode($glue, $pieces);
        });
        // Register the JSON encode function
        $compiler->registerTemplateFunction("json_encode",
            function (Views\ITemplate $template, $value, $options = 0, $depth = 512)
            {
                return json_encode($value, $options, $depth);
            }
        );
        // Register the lowercase first function
        $compiler->registerTemplateFunction("lcfirst", function (Views\ITemplate $template, $string)
        {
            return lcfirst($string);
        });
        // Register the meta description function
        $compiler->registerTemplateFunction("metaDescription", function (Views\ITemplate $template, $metaDescription)
        {
            return '<meta name="description" content="' . htmlentities($metaDescription) . '">';
        });
        // Register the meta keywords function
        $compiler->registerTemplateFunction("metaKeywords", function (Views\ITemplate $template, array $metaKeywords)
        {
            return '<meta name="keywords" content="' . implode(",", array_map("htmlentities", $metaKeywords)) . '">';
        });
        // Register the page title function
        $compiler->registerTemplateFunction("pageTitle", function (Views\ITemplate $template, $title)
        {
            return '<title>' . htmlentities($title) . '</title>';
        });
        // Register the round function
        $compiler->registerTemplateFunction("round",
            function (Views\ITemplate $template, $number, $precision = 0, $mode = PHP_ROUND_HALF_UP)
            {
                return round($number, $precision, $mode);
            }
        );
        // Register the script function
        $compiler->registerTemplateFunction("script",
            function (Views\ITemplate $template, $paths, $type = "text/javascript")
            {
                if(!is_array($paths))
                {
                    $paths = [$paths];
                }

                $callback = function($path) use ($type)
                {
                    return '<script type="' . $type . '" src="' . $path . '"></script>';
                };

                return implode("\n", array_map($callback, $paths));
            }
        );
        // Register the lowercase function
        $compiler->registerTemplateFunction("strtolower", function (Views\ITemplate $template, $string)
        {
            return strtolower($string);
        });
        // Register the lowercase function
        $compiler->registerTemplateFunction("strtoupper", function (Views\ITemplate $template, $string)
        {
            return strtoupper($string);
        });
        // Register the substring function
        $compiler->registerTemplateFunction("substr", function (Views\ITemplate $template, $string, $start, $length = null)
        {
            if($length === null)
            {
                return substr($string, $start);
            }

            return substr($string, $start, $length);
        });
        // Register the trim function
        $compiler->registerTemplateFunction("trim",
            function (Views\ITemplate $template, $string, $characterMask = " \t\n\r\0\x0B")
            {
                return trim($string, $characterMask);
            }
        );
        // Register the uppercase first function
        $compiler->registerTemplateFunction("ucfirst", function (Views\ITemplate $template, $string)
        {
            return ucfirst($string);
        });
        // Register the uppercase words function
        $compiler->registerTemplateFunction("ucwords", function (Views\ITemplate $template, $string)
        {
            return ucwords($string);
        });
        // Register the URL decode function
        $compiler->registerTemplateFunction("urldecode", function (Views\ITemplate $template, $string)
        {
            return urldecode($string);
        });
        // Register the URL encode function
        $compiler->registerTemplateFunction("urlencode", function (Views\ITemplate $template, $string)
        {
            return urlencode($string);
        });
    }
}