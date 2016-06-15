<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Http\Testing\PhpUnit\Assertions;

use Opulence\Http\Responses\RedirectResponse;
use Opulence\Http\Responses\Response;
use Opulence\Http\Responses\ResponseHeaders;
use PHPUnit_Framework_TestCase;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

/**
 * Defines the HTTP response assertions
 */
class ResponseAssertions extends PHPUnit_Framework_TestCase
{
    /** @var Response The HTTP response */
    protected $response = null;

    /**
     * Asserts that the response's contents match the input
     *
     * @param mixed $expected The expected value
     * @return self For method chaining
     */
    public function contentEquals($expected) : self
    {
        $this->checkResponseIsSet();
        $this->assertEquals($expected, $this->response->getContent());

        return $this;
    }

    /**
     * Asserts that the response's cookie's value equals the input
     *
     * @param string $name The name of the cookie to search for
     * @param mixed $expected The expected value
     * @return self For method chaining
     */
    public function cookieValueEquals(string $name, $expected) : self
    {
        $this->checkResponseIsSet();
        $cookies = $this->response->getHeaders()->getCookies();
        $cookieValue = null;

        foreach ($cookies as $cookie) {
            if ($cookie->getName() == $name) {
                $cookieValue = $cookie->getValue();

                break;
            }
        }

        $this->assertEquals($expected, $cookieValue);

        return $this;
    }

    /**
     * Asserts that the response has a cookie
     *
     * @param string $name The name of the cookie to search for
     * @return self For method chaining
     */
    public function hasCookie(string $name) : self
    {
        $this->checkResponseIsSet();
        $cookies = $this->response->getHeaders()->getCookies();
        $wasFound = false;

        foreach ($cookies as $cookie) {
            if ($cookie->getName() == $name) {
                $wasFound = true;

                break;
            }
        }

        $this->assertTrue($wasFound, "Failed asserting that the response has cookie \"$name\"");

        return $this;
    }

    /**
     * Asserts that the response has a header
     *
     * @param string $name The name of the header to search for
     * @return self For method chaining
     */
    public function hasHeader(string $name) : self
    {
        $this->checkResponseIsSet();
        $this->assertTrue(
            $this->response->getHeaders()->has($name),
            "Failed asserting that the response has header \"$name\""
        );

        return $this;
    }

    /**
     * Asserts that the response's header's value equals the input
     *
     * @param string $name The name of the header to search for
     * @param mixed $expected The expected value
     * @return self For method chaining
     */
    public function headerEquals(string $name, $expected) : self
    {
        $this->checkResponseIsSet();
        $this->assertEquals($expected, $this->response->getHeaders()->get($name));

        return $this;
    }

    /**
     * Asserts that the response is an internal server error
     *
     * @return self For method chaining
     */
    public function isInternalServerError() : self
    {
        $this->checkResponseIsSet();
        $this->assertEquals(ResponseHeaders::HTTP_INTERNAL_SERVER_ERROR, $this->response->getStatusCode());

        return $this;
    }

    /**
     * Asserts that the response is not found
     *
     * @return self For method chaining
     */
    public function isNotFound() : self
    {
        $this->checkResponseIsSet();
        $this->assertEquals(ResponseHeaders::HTTP_NOT_FOUND, $this->response->getStatusCode());

        return $this;
    }

    /**
     * Asserts that the response is OK
     *
     * @return self For method chaining
     */
    public function isOK() : self
    {
        $this->checkResponseIsSet();
        $this->assertEquals(ResponseHeaders::HTTP_OK, $this->response->getStatusCode());

        return $this;
    }

    /**
     * Asserts that the response is unauthorized
     *
     * @return self For method chaining
     */
    public function isUnauthorized() : self
    {
        $this->checkResponseIsSet();
        $this->assertEquals(ResponseHeaders::HTTP_UNAUTHORIZED, $this->response->getStatusCode());

        return $this;
    }

    /**
     * Asserts that the response's JSON contains the input
     *
     * @param array $expected The expected value
     * @return self For method chaining
     */
    public function jsonContains(array $expected) : self
    {
        $this->checkResponseIsSet();
        $this->assertJson($this->response->getContent());
        $actual = json_decode($this->response->getContent(), true);
        $iter = new RecursiveIteratorIterator(
            new RecursiveArrayIterator($actual),
            RecursiveIteratorIterator::SELF_FIRST
        );

        /**
         * The logic here is loop through all the keys in the response and,
         * on finding a match, unset that key from expected
         * If there's anything left in expected, then the response did not
         * contain it
         */
        foreach ($iter as $key => $value) {
            if (array_key_exists($key, $expected) && $expected[$key] === $value) {
                unset($expected[$key]);
            }
        }

        $this->assertTrue(count($expected) === 0, "Failed asserting JSON contains " . json_encode($expected));

        return $this;
    }

    /**
     * Asserts that the response's JSON contains a key
     *
     * @param string $expected The expected key
     * @return self For method chaining
     */
    public function jsonContainsKey(string $expected) : self
    {
        $this->checkResponseIsSet();
        $this->assertJson($this->response->getContent());
        $actual = json_decode($this->response->getContent(), true);
        $found = false;
        $iter = new RecursiveIteratorIterator(
            new RecursiveArrayIterator($actual),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iter as $key => $value) {
            if ($key === $expected) {
                $found = true;
                break;
            }
        }

        $this->assertTrue($found, "Failed asserting JSON contains key \"$expected\"");

        return $this;
    }

    /**
     * Asserts that the response's JSON matches the input
     *
     * @param array $expected The expected value
     * @return self For method chaining
     */
    public function jsonEquals(array $expected) : self
    {
        $this->checkResponseIsSet();
        $this->assertJson($this->response->getContent());
        $this->assertEquals($expected, json_decode($this->response->getContent(), true));

        return $this;
    }

    /**
     * Asserts that the response redirects to a URL
     *
     * @param string $url The expected URL
     * @return self For method chaining
     */
    public function redirectsTo(string $url) : self
    {
        $this->checkResponseIsSet();
        $this->assertTrue(
            $this->response instanceof RedirectResponse && $this->response->getTargetUrl() == $url,
            "Failed asserting that the response redirects to \"$url\""
        );

        return $this;
    }

    /**
     * @param Response $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * Asserts that the response status code equals a particular value
     *
     * @param int $statusCode The expected status code
     * @return self For method chaining
     */
    public function statusCodeEquals(int $statusCode) : self
    {
        $this->checkResponseIsSet();
        $this->assertEquals($statusCode, $this->response->getStatusCode());

        return $this;
    }

    /**
     * Checks if the response was set
     * Useful for making sure the response was set before making any assertions on it
     */
    private function checkResponseIsSet()
    {
        if ($this->response === null) {
            $this->fail("Must call route() before assertions");
        }
    }
}