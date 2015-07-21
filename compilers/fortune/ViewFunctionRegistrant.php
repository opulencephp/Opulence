<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the registrant that creates the built-in functions
 */
namespace Opulence\Views\Compilers\Fortune;

class ViewFunctionRegistrant
{
    /**
     * Registers the built-in view functions
     *
     * @param FortuneCompiler $compiler The compiler to register to
     */
    public function registerViewFunctions(FortuneCompiler &$compiler)
    {
        // Register the charset function
        $compiler->registerViewFunction("charset", function ($charset)
        {
            return '<meta charset="' . $charset . '">';
        });
        // Register the CSS function
        $compiler->registerViewFunction("css", function ($paths)
        {
            $callback = function ($path)
            {
                return '<link href="' . $path . '" rel="stylesheet">';
            };

            return implode("\n", array_map($callback, (array)$paths));
        });
        // Register the favicon function
        $compiler->registerViewFunction("favicon", function ($path)
        {
            return '<link href="' . $path . '" rel="shortcut icon">';
        });
        // Register the HTTP-equiv function
        $compiler->registerViewFunction("httpEquiv", function ($name, $value)
        {
            return '<meta http-equiv="' . htmlentities($name) . '" content="' . htmlentities($value) . '">';
        });
        // Register the meta description function
        $compiler->registerViewFunction("metaDescription", function ($metaDescription)
        {
            return '<meta name="description" content="' . htmlentities($metaDescription) . '">';
        });
        // Register the meta keywords function
        $compiler->registerViewFunction("metaKeywords", function (array $metaKeywords)
        {
            return '<meta name="keywords" content="' . implode(",", array_map("htmlentities", $metaKeywords)) . '">';
        });
        // Register the page title function
        $compiler->registerViewFunction("pageTitle", function ($title)
        {
            return '<title>' . htmlentities($title) . '</title>';
        });
        // Register the script function
        $compiler->registerViewFunction("script", function ($paths, $type = "text/javascript")
        {
            $callback = function ($path) use ($type)
            {
                return '<script type="' . $type . '" src="' . $path . '"></script>';
            };

            return implode("\n", array_map($callback, (array)$paths));
        });
    }
}