<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Views\Bootstrappers;

use Opulence\Framework\Http\CsrfTokenChecker;
use Opulence\Http\Requests\Request;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Routing\Urls\UrlGenerator;
use Opulence\Sessions\ISession;
use Opulence\Views\Compilers\Fortune\ITranspiler;

/**
 * Defines the view functions bootstrapper
 */
class ViewFunctionsBootstrapper extends Bootstrapper
{
    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container): void
    {
        $request = $container->resolve(Request::class);
        $transpiler = $container->resolve(ITranspiler::class);
        $urlGenerator = $container->resolve(UrlGenerator::class);
        $session = $container->resolve(ISession::class);

        // Add the ability to display a hidden input with the current CSRF token
        $transpiler->registerViewFunction('csrfInput', function () use ($session) {
            return sprintf(
                '<input type="hidden" name="%s" value="%s">',
                CsrfTokenChecker::TOKEN_INPUT_NAME,
                $session->get(CsrfTokenChecker::TOKEN_INPUT_NAME)
            );
        });
        // Add the ability to display the CSRF token
        $transpiler->registerViewFunction('csrfToken', function () use ($session) {
            return $session->get(CsrfTokenChecker::TOKEN_INPUT_NAME);
        });
        // Add the ability to tell if the current route is a particular route
        $transpiler->registerViewFunction('currentRouteIs', function ($routeName) use ($request, $urlGenerator) {
            $regex = $urlGenerator->createRegexFromName($routeName);
            // Strip the delimiters
            $regex = substr($regex, 1, -1);

            // Check if the returned regex is a path or full URL regex
            if (preg_match("#^\^http(s)?\\\\://#", $regex) === 1) {
                return $request->isUrl($regex, true);
            } else {
                return $request->isPath($regex, true);
            }
        });
        // Add the ability to generate URLs to named routes from views
        $transpiler->registerViewFunction('route', function ($routeName, ...$args) use ($urlGenerator) {
            return $urlGenerator->createFromName(...func_get_args());
        });
    }
}
