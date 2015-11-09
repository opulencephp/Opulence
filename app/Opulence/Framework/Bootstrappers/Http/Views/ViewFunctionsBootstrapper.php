<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Bootstrappers\Http\Views;

use Opulence\Bootstrappers\Bootstrapper;
use Opulence\Framework\Http\CsrfTokenChecker;
use Opulence\Routing\Url\UrlGenerator;
use Opulence\Sessions\ISession;
use Opulence\Views\Compilers\Fortune\ITranspiler;

/**
 * Defines the view functions bootstrapper
 */
class ViewFunctionsBootstrapper extends Bootstrapper
{
    /**
     * Registers view functions
     *
     * @param ITranspiler $transpiler The transpiler to register to
     * @param UrlGenerator $urlGenerator What generates URLs from routes
     * @param ISession $session The current session
     */
    public function run(ITranspiler $transpiler, UrlGenerator $urlGenerator, ISession $session)
    {
        // Add the ability to display a hidden input with the current CSRF token
        $transpiler->registerViewFunction("csrfInput", function () use ($session) {
            return sprintf(
                '<input type="hidden" name="%s" value="%s">',
                CsrfTokenChecker::TOKEN_INPUT_NAME,
                $session->get(CsrfTokenChecker::TOKEN_INPUT_NAME)
            );
        });
        // Add the ability to display the CSRF token
        $transpiler->registerViewFunction("csrfToken", function () use ($session) {
            return $session->get(CsrfTokenChecker::TOKEN_INPUT_NAME);
        });
        // Add the ability to generate URLs to named routes from views
        $transpiler->registerViewFunction("route", function ($routeName) use ($urlGenerator) {
            return call_user_func_array([$urlGenerator, "createFromName"], func_get_args());
        });
    }
}