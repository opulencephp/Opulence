<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the template functions bootstrapper
 */
namespace RDev\Framework\HTTP\Bootstrappers\Views;
use RDev\Applications\Bootstrappers;
use RDev\HTTP\Routing\URL;
use RDev\Views\Compilers;

class TemplateFunctions extends Bootstrappers\Bootstrapper
{
    /**
     * Registers template functions
     *
     * @param Compilers\ICompiler $compiler The compiler to use
     * @param URL\URLGenerator $urlGenerator What generates URLs from routes
     */
    public function run(Compilers\ICompiler $compiler, URL\URLGenerator $urlGenerator)
    {
        // Add the ability to generate URLs to named routes from templates
        $compiler->registerTemplateFunction("route", function($routeName, $arguments = []) use ($urlGenerator)
        {
            return $urlGenerator->createFromName($routeName, $arguments);
        });
    }
}