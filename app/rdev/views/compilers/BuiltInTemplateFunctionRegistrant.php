<?php
/**
 * Copyright (C) 2014 David Young
 * 
 * Defines the registrant that creates the built-in functions
 */
namespace RDev\Views\Compilers;

class BuiltInTemplateFunctionRegistrant
{
    /**
     * Registers the built-in template functions
     * 
     * @param ICompiler $compiler The compiler to register to
     */
    public function registerTemplateFunctions(ICompiler &$compiler)
    {
        // Register the absolute value function
        $compiler->registerTemplateFunction("abs", function ($number)
        {
            return abs($number);
        });
        // Register the ceiling function
        $compiler->registerTemplateFunction("ceil", function ($number)
        {
            return ceil($number);
        });
        // Register the charset function
        $compiler->registerTemplateFunction("charset", function ($charset)
        {
            return '<meta charset="' . $charset . '">';
        });
        // Register the CSS function
        $compiler->registerTemplateFunction("css", function ($paths)
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
        $compiler->registerTemplateFunction("count", function (array $array)
        {
            return count($array);
        });
        // Register the date function
        $compiler->registerTemplateFunction("date", function ($format, $timestamp = null)
        {
            if($timestamp === null)
            {
                $timestamp = time();
            }

            return date($format, $timestamp);
        });
        // Register the favicon function
        $compiler->registerTemplateFunction("favicon", function ($path)
        {
            return '<link href="' . $path . '" rel="shortcut icon">';
        });
        // Register the floor function
        $compiler->registerTemplateFunction("floor", function ($number)
        {
            return floor($number);
        });
        // Register the format DateTime function
        $compiler->registerTemplateFunction('formatDateTime',
            function (\DateTime $date, $format = "m/d/Y", $timeZone = null)
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
        $compiler->registerTemplateFunction("httpEquiv", function ($name, $value)
        {
            return '<meta http-equiv="' . htmlentities($name) . '" content="' . htmlentities($value) . '">';
        });
        // Register the implode function
        $compiler->registerTemplateFunction("implode", function ($glue, array $pieces)
        {
            return implode($glue, $pieces);
        });
        // Register the JSON encode function
        $compiler->registerTemplateFunction("json_encode", function ($value, $options = 0, $depth = 512)
        {
            return json_encode($value, $options, $depth);
        });
        // Register the lowercase first function
        $compiler->registerTemplateFunction("lcfirst", function ($string)
        {
            return lcfirst($string);
        });
        // Register the meta description function
        $compiler->registerTemplateFunction("metaDescription", function ($metaDescription)
        {
            return '<meta name="description" content="' . htmlentities($metaDescription) . '">';
        });
        // Register the meta keywords function
        $compiler->registerTemplateFunction("metaKeywords", function (array $metaKeywords)
        {
            return '<meta name="keywords" content="' . implode(",", array_map("htmlentities", $metaKeywords)) . '">';
        });
        // Register the page title function
        $compiler->registerTemplateFunction("pageTitle", function ($title)
        {
            return '<title>' . htmlentities($title) . '</title>';
        });
        // Register the round function
        $compiler->registerTemplateFunction("round", function ($number, $precision = 0, $mode = PHP_ROUND_HALF_UP)
        {
            return round($number, $precision, $mode);
        });
        // Register the script function
        $compiler->registerTemplateFunction("script", function ($paths, $type = "text/javascript")
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
        });
        // Register the lowercase function
        $compiler->registerTemplateFunction("strtolower", function ($string)
        {
            return strtolower($string);
        });
        // Register the lowercase function
        $compiler->registerTemplateFunction("strtoupper", function ($string)
        {
            return strtoupper($string);
        });
        // Register the substring function
        $compiler->registerTemplateFunction("substr", function ($string, $start, $length = null)
        {
            if($length === null)
            {
                return substr($string, $start);
            }

            return substr($string, $start, $length);
        });
        // Register the trim function
        $compiler->registerTemplateFunction("trim", function ($string, $characterMask = " \t\n\r\0\x0B")
        {
            return trim($string, $characterMask);
        });
        // Register the uppercase first function
        $compiler->registerTemplateFunction("ucfirst", function ($string)
        {
            return ucfirst($string);
        });
        // Register the uppercase words function
        $compiler->registerTemplateFunction("ucwords", function ($string)
        {
            return ucwords($string);
        });
        // Register the URL decode function
        $compiler->registerTemplateFunction("urldecode", function ($string)
        {
            return urldecode($string);
        });
        // Register the URL encode function
        $compiler->registerTemplateFunction("urlencode", function ($string)
        {
            return urlencode($string);
        });
    }
}