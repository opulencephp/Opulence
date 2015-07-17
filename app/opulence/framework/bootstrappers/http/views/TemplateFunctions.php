<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the template functions bootstrapper
 */
namespace Opulence\Framework\Bootstrappers\HTTP\Views;
use Opulence\Applications\Bootstrappers\Bootstrapper;
use Opulence\Framework\HTTP\CSRFTokenChecker;
use Opulence\Routing\URL\URLGenerator;
use Opulence\Sessions\ISession;
use Opulence\Views\Compilers\ICompiler;

class TemplateFunctions extends Bootstrapper
{
    /**
     * Registers template functions
     *
     * @param ICompiler $compiler The compiler to use
     * @param URLGenerator $urlGenerator What generates URLs from routes
     * @param ISession $session The current session
     */
    public function run(ICompiler $compiler, URLGenerator $urlGenerator, ISession $session)
    {
        // Add the ability to display a hidden input with the current CSRF token
        $compiler->registerTemplateFunction("csrfInput", function () use ($session)
        {
            return sprintf(
                '<input type="hidden" name="%s" value="%s">',
                CSRFTokenChecker::TOKEN_INPUT_NAME,
                $session->get(CSRFTokenChecker::TOKEN_INPUT_NAME)
            );
        });
        // Add the ability to display the CSRF token
        $compiler->registerTemplateFunction("csrfToken", function () use ($session)
        {
            return $session->get(CSRFTokenChecker::TOKEN_INPUT_NAME);
        });
        // Add the ability to generate URLs to named routes from templates
        $compiler->registerTemplateFunction("route", function ($routeName, $arguments = []) use ($urlGenerator)
        {
            return $urlGenerator->createFromName($routeName, $arguments);
        });
    }
}