<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Http\Tests\Responses;

use LogicException;
use Opulence\Http\Responses\StreamResponse;

/**
 * Tests the stream response
 */
class StreamResponseTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests that the contents are output once
     */
    public function testContentsAreOutputOnce()
    {
        $response = new StreamResponse(function () {
            echo 'foo';
        });
        ob_start();
        $response->sendContent();
        $this->assertEquals('foo', ob_get_clean());
        ob_start();
        $response->sendContent();
        $this->assertEquals('', ob_get_clean());
    }

    /**
     * Tests not setting the callback and then sending the content
     */
    public function testNotSettingCallbackThenSendingContent()
    {
        $response = new StreamResponse();
        ob_start();
        $response->sendContent();
        $this->assertEquals('', ob_get_clean());
    }

    /**
     * Tests setting the content
     */
    public function testSettingContent()
    {
        $this->expectException(LogicException::class);
        $response = new StreamResponse();
        $response->setContent('foo');
    }

    /**
     * Tests setting the stream callback
     */
    public function testSettingStreamCallback()
    {
        $response = new StreamResponse();
        $response->setStreamCallback(function () {
            echo 'foo';
        });
        ob_start();
        $response->sendContent();
        $this->assertEquals('foo', ob_get_clean());
    }
}
