<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the template functions bootstrapper
 */
namespace RDev\Framework\HTTP\Views\Bootstrappers;
use RDev\Applications\Bootstrappers;
use RDev\HTTP\Routing\URL;
use RDev\Views;
use RDev\Views\Compilers;

class TemplateFunctions implements Bootstrappers\IBootstrapper
{
    /** @var Compilers\ICompiler The template compiler */
    private $compiler = null;
    /** @var URL\URLGenerator What generates URLs from routes */
    private $urlGenerator = null;

    /**
     * @param Compilers\ICompiler $compiler The compiler to use
     * @param URL\URLGenerator $urlGenerator What generates URLs from routes
     */
    public function __construct(Compilers\ICompiler $compiler, URL\URLGenerator $urlGenerator)
    {
        $this->compiler = $compiler;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        // Add the ability to generate URLs to named routes from templates
        $this->compiler->registerTemplateFunction("route", function($routeName, $arguments = [])
        {
            return $this->urlGenerator->createFromName($routeName, $arguments);
        });
    }
}