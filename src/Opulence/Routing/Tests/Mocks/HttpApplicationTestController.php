<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Routing\Tests\Mocks;

use DateTime;
use Opulence\Http\Responses\Cookie;
use Opulence\Http\Responses\JsonResponse;
use Opulence\Http\Responses\RedirectResponse;
use Opulence\Http\Responses\Response;
use Opulence\Http\Responses\ResponseHeaders;
use Opulence\Routing\Controller as BaseController;
use Opulence\Views\View;

/**
 * Defines the controller used by the HTTP application test
 */
class HttpApplicationTestController extends BaseController
{
    /**
     * Creates a redirect response
     *
     * @return RedirectResponse The response
     */
    public function redirect(): RedirectResponse
    {
        return new RedirectResponse('/redirectedPath');
    }

    /**
     * Sets a bad gateway status code in the response
     *
     * @return Response The response
     */
    public function setBadGateway(): Response
    {
        return new Response('FooBar', ResponseHeaders::HTTP_BAD_GATEWAY);
    }

    /**
     * Sets a cookie in the response
     *
     * @return Response The response
     */
    public function setCookie(): Response
    {
        $response = new Response('FooBar');
        $response->getHeaders()->setCookie(new Cookie('foo', 'bar', new DateTime()));

        return $response;
    }

    /**
     * Sets a header in the response
     *
     * @return Response The response
     */
    public function setHeader(): Response
    {
        $response = new Response('FooBar');
        $response->getHeaders()->set('foo', 'bar');

        return $response;
    }

    /**
     * Sets an internal server error in the response
     *
     * @return Response The response
     */
    public function setISE(): Response
    {
        return new Response('FooBar', ResponseHeaders::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Sets an OK response
     *
     * @return Response The response
     */
    public function setOK(): Response
    {
        return new Response('FooBar', ResponseHeaders::HTTP_OK);
    }

    /**
     * Sets an unauthorized response
     *
     * @return Response The response
     */
    public function setUnauthorized(): Response
    {
        return new Response('FooBar', ResponseHeaders::HTTP_UNAUTHORIZED);
    }

    /**
     * Sets a variable in the view
     *
     * @return Response The response
     */
    public function setVar(): Response
    {
        $this->view = new View('thecontent');
        $this->view->setVar('foo', 'bar');

        return new Response('FooBar');
    }

    /**
     * Shows "FooBar" in the response content
     *
     * @return Response The response
     */
    public function showFooBar(): Response
    {
        return new Response('FooBar');
    }

    /**
     * Shows a JSON response
     *
     * @return JsonResponse The response
     */
    public function showJson(): JsonResponse
    {
        return new JsonResponse(['foo' => 'bar', 'baz' => ['subkey' => 'subvalue']]);
    }
}
