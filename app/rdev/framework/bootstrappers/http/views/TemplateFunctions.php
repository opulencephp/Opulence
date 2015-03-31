<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the template functions bootstrapper
 */
namespace RDev\Framework\Bootstrappers\HTTP\Views;
use RDev\Applications\Bootstrappers\Bootstrapper;
use RDev\HTTP\Routing\URL\URLGenerator;
use RDev\Views\Compilers\ICompiler;

class TemplateFunctions extends Bootstrapper
{
    /**
     * Registers template functions
     *
     * @param ICompiler $compiler The compiler to use
     * @param URLGenerator $urlGenerator What generates URLs from routes
     */
    public function run(ICompiler $compiler, URLGenerator $urlGenerator)
    {
        // Add the ability to generate URLs to named routes from templates
        $compiler->registerTemplateFunction("route", function($routeName, $arguments = []) use ($urlGenerator)
        {
            return $urlGenerator->createFromName($routeName, $arguments);
        });
    }
}