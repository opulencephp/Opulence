<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Http\Tests\Responses;

use DateTime;
use Opulence\Http\Responses\Response;
use Opulence\Http\Responses\ResponseHeaders;

/**
 * Tests the HTTP response
 */
class ResponseTest extends \PHPUnit\Framework\TestCase
{
    /** @var Response The response to use in tests */
    private $response = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->response = new Response();
    }

    /**
     * Tests getting the content
     */
    public function testGettingContent()
    {
        $response = new Response('foo');
        $this->assertEquals('foo', $response->getContent());
    }

    /**
     * Tests getting the default HTTP version
     */
    public function testGettingDefaultHttpVersion()
    {
        $this->assertEquals('1.1', $this->response->getHttpVersion());
    }

    /**
     * Tests getting the default status code
     */
    public function testGettingDefaultStatusCode()
    {
        $this->assertEquals(ResponseHeaders::HTTP_OK, $this->response->getStatusCode());
    }

    /**
     * Tests sending the content
     *
     * @runInSeparateProcess
     */
    public function testSendingContent()
    {
        $this->response->setContent('foo');
        ob_start();
        $this->response->sendContent();
        $this->assertEquals('foo', ob_get_clean());
    }

    /**
     * Tests setting the content
     */
    public function testSettingContent()
    {
        $this->response->setContent('foo');
        $this->assertEquals('foo', $this->response->getContent());
    }

    /**
     * Tests setting an expiration
     */
    public function testSettingExpiration()
    {
        $expiration = new DateTime('now');
        $this->response->setExpiration($expiration);
        $this->assertEquals($expiration->format('r'), $this->response->getHeaders()->get('Expires'));
    }

    /**
     * Tests setting the HTTP version
     */
    public function testSettingHttpVersion()
    {
        $this->response->setHttpVersion('2.0');
        $this->assertEquals('2.0', $this->response->getHttpVersion());
    }

    /**
     * Tests setting the status code
     */
    public function testSettingStatusCode()
    {
        $this->response->setStatusCode(ResponseHeaders::HTTP_ACCEPTED);
        $this->assertEquals(ResponseHeaders::HTTP_ACCEPTED, $this->response->getStatusCode());
    }

    /**
     * Tests setting the status code with text
     */
    public function testSettingStatusCodeWithText()
    {
        $this->response->setStatusCode(ResponseHeaders::HTTP_ACCEPTED,
            ResponseHeaders::$statusTexts[ResponseHeaders::HTTP_ACCEPTED]);
        $this->assertEquals(ResponseHeaders::HTTP_ACCEPTED, $this->response->getStatusCode());
    }
}
