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

use Opulence\Http\HttpException;
use Opulence\Http\Responses\Response;
use Opulence\Routing\Controller as BaseController;

/**
 * Defines a mock controller for use in testing
 */
class Controller extends BaseController
{
    /**
     * Mocks a method that takes in multiple parameters with some default values
     *
     * @param mixed $foo The first parameter
     * @param mixed $bar The second parameter
     * @param mixed $blah The optional third parameter
     * @return Response The parameter names to their values
     */
    public function multipleParametersWithDefaultValues($foo, $bar, $blah = '724'): Response
    {
        return new Response("foo:$foo, bar:$bar, blah:$blah");
    }

    /**
     * Mocks a method that takes in no parameters
     *
     * @return Response An empty string
     */
    public function noParameters(): Response
    {
        return new Response('noParameters');
    }

    /**
     * Mocks a method that takes in a single parameter
     *
     * @param mixed $foo The parameter
     * @return Response The parameter name to its value
     */
    public function oneParameter($foo): Response
    {
        return new Response("foo:$foo");
    }

    /**
     * Mocks a method that does not return anything
     */
    public function returnsNothing(): void
    {
        // Don't do anything
    }

    /**
     * Mocks a method that returns text
     */
    public function returnsText(): string
    {
        return 'returnsText';
    }

    /**
     * Mocks a method that takes in several parameters
     *
     * @param mixed $foo The first parameter
     * @param mixed $bar The second parameter
     * @param mixed $baz The third parameter
     * @param mixed $blah The fourth parameter
     * @return Response The parameter names to their values
     */
    public function severalParameters($foo, $bar, $baz, $blah): Response
    {
        return new Response("foo:$foo, bar:$bar, baz:$baz, blah:$blah");
    }

    /**
     * @inheritdoc
     */
    public function showHttpError($statusCode): Response
    {
        return new Response('foo', $statusCode);
    }

    /**
     * Mocks a method that throws an HTTP exception
     */
    public function throwsHttpException(): void
    {
        throw new HttpException(400);
    }

    /**
     * Mocks a method that takes in two parameters
     *
     * @param mixed $foo The first parameter
     * @param mixed $bar The second parameter
     * @return Response The parameter names to their values
     */
    public function twoParameters($foo, $bar): Response
    {
        return new Response("foo:$foo, bar:$bar");
    }

    /**
     * Mocks a protected method for use in testing
     *
     * @return Response The name of the method
     */
    protected function protectedMethod(): Response
    {
        return new Response('protectedMethod');
    }

    /**
     * Mocks a private method for use in testing
     *
     * @return Response The name of the method
     */
    private function privateMethod(): Response
    {
        return new Response('privateMethod');
    }
}
