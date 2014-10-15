<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the JSON response
 */
namespace RDev\Models\HTTP;

class JSONResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests getting the content type
     */
    public function testGettingContentType()
    {
        $response = new JSONResponse();
        $this->assertEquals(ResponseHeaders::CONTENT_TYPE_JSON, $response->getHeaders()->get("Content-Type"));
    }

    /**
     * Tests getting the status code after setting it in the constructor
     */
    public function testGettingStatusCodeAfterSettingInConstructor()
    {
        $response = new JSONResponse([], ResponseHeaders::HTTP_ACCEPTED);
        $this->assertEquals(ResponseHeaders::HTTP_ACCEPTED, $response->getStatusCode());
    }

    /**
     * Tests setting the content to an invalid type in the constructor
     */
    public function testSettingContentOfIncorrectTypeInConstructor()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        new JSONResponse("\xB1\x31");
    }

    /**
     * Tests setting the content to an invalid type in the setter
     */
    public function testSettingContentOfIncorrectTypeInSetter()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $response = new JSONResponse();
        $response->setContent("\xB1\x31");
    }

    /**
     * Tests setting the content to an array in the constructor
     */
    public function testSettingContentToArrayInConstructor()
    {
        $content = ["foo" => "bar"];
        $response = new JSONResponse($content);
        $this->assertSame(json_encode($content), $response->getContent());
    }

    /**
     * Tests setting the content to an array in the setter
     */
    public function testSettingContentToArrayInSetter()
    {
        $content = ["foo" => "bar"];
        $response = new JSONResponse();
        $response->setContent($content);
        $this->assertEquals(json_encode($content), $response->getContent());
    }

    /**
     * Tests setting the content to an ArrayObject in the constructor
     */
    public function testSettingContentToArrayObjectInConstructor()
    {
        $content = new \ArrayObject(["foo" => "bar"]);
        $response = new JSONResponse($content);
        $this->assertEquals(json_encode($content->getArrayCopy()), $response->getContent());
    }

    /**
     * Tests setting the content to an ArrayObject in the setter
     */
    public function testSettingContentToArrayObjectInSetter()
    {
        $content = new \ArrayObject(["foo" => "bar"]);
        $response = new JSONResponse();
        $response->setContent($content);
        $this->assertEquals(json_encode($content->getArrayCopy()), $response->getContent());
    }

    /**
     * Tests setting the headers in the constructor
     */
    public function testSettingHeadersInConstructor()
    {
        $response = new JSONResponse([], ResponseHeaders::HTTP_OK, ["HTTP_FOO" => "bar"]);
        $this->assertEquals("bar", $response->getHeaders()->get("FOO"));
    }
} 