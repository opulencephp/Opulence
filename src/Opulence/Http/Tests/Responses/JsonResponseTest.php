<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Http\Tests\Responses;

use ArrayObject;
use InvalidArgumentException;
use Opulence\Http\Responses\JsonResponse;
use Opulence\Http\Responses\ResponseHeaders;

/**
 * Tests the JSON response
 */
class JsonResponseTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests getting the content type
     */
    public function testGettingContentType()
    {
        $response = new JsonResponse();
        $this->assertEquals(ResponseHeaders::CONTENT_TYPE_JSON, $response->getHeaders()->get('Content-Type'));
    }

    /**
     * Tests getting the status code after setting it in the constructor
     */
    public function testGettingStatusCodeAfterSettingInConstructor()
    {
        $response = new JsonResponse([], ResponseHeaders::HTTP_ACCEPTED);
        $this->assertEquals(ResponseHeaders::HTTP_ACCEPTED, $response->getStatusCode());
    }

    /**
     * Tests setting the content to an invalid type in the constructor
     */
    public function testSettingContentOfIncorrectTypeInConstructor()
    {
        $this->expectException(InvalidArgumentException::class);
        new JsonResponse("\xB1\x31");
    }

    /**
     * Tests setting the content to an invalid type in the setter
     */
    public function testSettingContentOfIncorrectTypeInSetter()
    {
        $this->expectException(InvalidArgumentException::class);
        $response = new JsonResponse();
        $response->setContent("\xB1\x31");
    }

    /**
     * Tests setting the content to an array in the constructor
     */
    public function testSettingContentToArrayInConstructor()
    {
        $content = ['foo' => 'bar'];
        $response = new JsonResponse($content);
        $this->assertSame(json_encode($content), $response->getContent());
    }

    /**
     * Tests setting the content to an array in the setter
     */
    public function testSettingContentToArrayInSetter()
    {
        $content = ['foo' => 'bar'];
        $response = new JsonResponse();
        $response->setContent($content);
        $this->assertEquals(json_encode($content), $response->getContent());
    }

    /**
     * Tests setting the content to an ArrayObject in the constructor
     */
    public function testSettingContentToArrayObjectInConstructor()
    {
        $content = new ArrayObject(['foo' => 'bar']);
        $response = new JsonResponse($content);
        $this->assertEquals(json_encode($content->getArrayCopy()), $response->getContent());
    }

    /**
     * Tests setting the content to an ArrayObject in the setter
     */
    public function testSettingContentToArrayObjectInSetter()
    {
        $content = new ArrayObject(['foo' => 'bar']);
        $response = new JsonResponse();
        $response->setContent($content);
        $this->assertEquals(json_encode($content->getArrayCopy()), $response->getContent());
    }

    /**
     * Tests setting the headers in the constructor
     */
    public function testSettingHeadersInConstructor()
    {
        $response = new JsonResponse([], ResponseHeaders::HTTP_OK, ['FOO' => 'bar']);
        $this->assertEquals('bar', $response->getHeaders()->get('FOO'));
    }
}
