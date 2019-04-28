<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Tests\Http\Testing\PhpUnit\Assertions;

use DateTime;
use Opulence\Framework\Http\Testing\PhpUnit\Assertions\ResponseAssertions;
use Opulence\Http\Responses\Cookie;
use Opulence\Http\Responses\JsonResponse;
use Opulence\Http\Responses\RedirectResponse;
use Opulence\Http\Responses\Response;
use Opulence\Http\Responses\ResponseHeaders;

/**
 * Tests the response assertions
 */
class ResponseAssertionsTest extends \PHPUnit\Framework\TestCase
{
    /** @var ResponseAssertions The assertions to use in tests */
    private $assertions;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->assertions = new ResponseAssertions();
    }

    /**
     * Tests asserting that a path redirects to another
     */
    public function testAssertingRedirect(): void
    {
        $this->assertions->setResponse(new RedirectResponse('/redirectedPath'));
        $this->assertSame($this->assertions, $this->assertions->redirectsTo('/redirectedPath'));
    }

    /**
     * Tests asserting that a response has certain content
     */
    public function testAssertingResponseHasContent(): void
    {
        $this->assertions->setResponse(new Response('FooBar'));
        $this->assertSame($this->assertions, $this->assertions->contentEquals('FooBar'));
    }

    /**
     * Tests asserting that a response has a certain cookie
     */
    public function testAssertingResponseHasCookie(): void
    {
        $response = new Response();
        $response->getHeaders()->setCookie(
            new Cookie('foo', 'bar', new DateTime('+1 week'))
        );
        $this->assertions->setResponse($response);
        $this->assertSame($this->assertions, $this->assertions->hasCookie('foo'));
        $this->assertSame($this->assertions, $this->assertions->cookieValueEquals('foo', 'bar'));
    }

    /**
     * Tests asserting that a response has a certain header
     */
    public function testAssertingResponseHasHeader(): void
    {
        $response = new Response();
        $response->getHeaders()->set('foo', 'bar');
        $this->assertions->setResponse($response);
        $this->assertSame($this->assertions, $this->assertions->hasHeader('foo'));
        $this->assertSame($this->assertions, $this->assertions->headerEquals('foo', 'bar'));
    }

    /**
     * Tests asserting that a response has status code
     */
    public function testAssertingResponseHasStatusCode(): void
    {
        $response = new Response('', ResponseHeaders::HTTP_BAD_GATEWAY);
        $this->assertions->setResponse($response);
        $this->assertSame(
            $this->assertions,
            $this->assertions->statusCodeEquals(ResponseHeaders::HTTP_BAD_GATEWAY)
        );
    }

    /**
     * Tests asserting that a response is an internal server error
     */
    public function testAssertingResponseIsInternalServerError(): void
    {
        $response = new Response('', ResponseHeaders::HTTP_INTERNAL_SERVER_ERROR);
        $this->assertions->setResponse($response);
        $this->assertSame($this->assertions, $this->assertions->isInternalServerError());
    }

    /**
     * Tests asserting that a response is not found
     */
    public function testAssertingResponseIsNotFound(): void
    {
        $response = new Response('', ResponseHeaders::HTTP_NOT_FOUND);
        $this->assertions->setResponse($response);
        $this->assertSame($this->assertions, $this->assertions->isNotFound());
    }

    /**
     * Tests asserting that a response is OK
     */
    public function testAssertingResponseIsOK(): void
    {
        $response = new Response('', ResponseHeaders::HTTP_OK);
        $this->assertions->setResponse($response);
        $this->assertSame($this->assertions, $this->assertions->isOK());
    }

    /**
     * Tests asserting that a response is unauthorized
     */
    public function testAssertingResponseIsUnauthorized(): void
    {
        $response = new Response('', ResponseHeaders::HTTP_UNAUTHORIZED);
        $this->assertions->setResponse($response);
        $this->assertSame($this->assertions, $this->assertions->isUnauthorized());
    }

    /**
     * Tests asserting response JSON contains
     */
    public function testAssertingResponseJsonContains(): void
    {
        $response = new JsonResponse(['foo' => 'bar', 'baz' => ['subkey' => 'subvalue']]);
        $this->assertions->setResponse($response);
        $this->assertSame($this->assertions, $this->assertions->jsonContains(['foo' => 'bar']));
        $this->assertSame(
            $this->assertions,
            $this->assertions->jsonContains(['baz' => ['subkey' => 'subvalue']])
        );
        $this->assertSame(
            $this->assertions,
            $this->assertions->jsonContains(['subkey' => 'subvalue'])
        );
    }

    /**
     * Tests asserting response JSON contains key
     */
    public function testAssertingResponseJsonContainsKey(): void
    {
        $response = new JsonResponse(['foo' => 'bar', 'baz' => ['subkey' => 'subvalue']]);
        $this->assertions->setResponse($response);
        $this->assertSame($this->assertions, $this->assertions->jsonContainsKey('foo'));
        $this->assertSame($this->assertions, $this->assertions->jsonContainsKey('subkey'));
    }

    /**
     * Tests asserting response JSON equals
     */
    public function testAssertingResponseJsonEquals(): void
    {
        $response = new JsonResponse(['foo' => 'bar', 'baz' => ['subkey' => 'subvalue']]);
        $this->assertions->setResponse($response);
        $this->assertSame(
            $this->assertions,
            $this->assertions->jsonEquals(['foo' => 'bar', 'baz' => ['subkey' => 'subvalue']])
        );
    }
}
