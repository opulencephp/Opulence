<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the view functions bootstrapper
 */
namespace Opulence\Framework\Bootstrappers\HTTP\Views;

use Opulence\Applications\Bootstrappers\Bootstrapper;
use Opulence\Framework\HTTP\CSRFTokenChecker;
use Opulence\Routing\URL\URLGenerator;
use Opulence\Sessions\ISession;
use Opulence\Views\Compilers\Fortune\ITranspiler;

class ViewFunctionsBootstrapper extends Bootstrapper
{
    /**
     * Registers view functions
     *
     * @param ITranspiler $transpiler The transpiler to register to
     * @param URLGenerator $urlGenerator What generates URLs from routes
     * @param ISession $session The current session
     */
    public function run(ITranspiler $transpiler, URLGenerator $urlGenerator, ISession $session)
    {
        // Add the ability to display a hidden input with the current CSRF token
        $transpiler->registerViewFunction("csrfInput", function () use ($session)
        {
            return sprintf(
                '<input type="hidden" name="%s" value="%s">',
                CSRFTokenChecker::TOKEN_INPUT_NAME,
                $session->get(CSRFTokenChecker::TOKEN_INPUT_NAME)
            );
        });
        // Add the ability to display the CSRF token
        $transpiler->registerViewFunction("csrfToken", function () use ($session)
        {
            return $session->get(CSRFTokenChecker::TOKEN_INPUT_NAME);
        });
        // Add the ability to generate URLs to named routes from views
        $transpiler->registerViewFunction("route", function ($routeName, $arguments = []) use ($urlGenerator)
        {
            return $urlGenerator->createFromName($routeName, $arguments);
        });
    }
}