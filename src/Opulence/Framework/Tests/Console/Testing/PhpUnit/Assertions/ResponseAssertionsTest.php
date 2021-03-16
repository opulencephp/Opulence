<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Framework\Tests\Console\Testing\PhpUnit\Assertions;

use Opulence\Console\Responses\StreamResponse;
use Opulence\Console\StatusCodes;
use Opulence\Framework\Console\Testing\PhpUnit\Assertions\ResponseAssertions;

/**
 * Tests the response assertions
 */
class ResponseAssertionsTest extends \PHPUnit\Framework\TestCase
{
    /** @var ResponseAssertions The response assertions to use in tests */
    private $assertions = null;
    /** @var StreamResponse|\PHPUnit_Framework_MockObject_MockObject The response to use in tests */
    private $mockResponse = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->assertions = new ResponseAssertions();
        $this->mockResponse = $this->getMockBuilder(StreamResponse::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Tests asserting that the status code is an error
     */
    public function testAssertStatusCodeIsError()
    {
        $this->assertions->setResponse($this->mockResponse, StatusCodes::ERROR);
        $this->assertSame($this->assertions, $this->assertions->isError());
    }

    /**
     * Tests asserting that the status code is fatal
     */
    public function testAssertStatusCodeIsFatal()
    {
        $this->assertions->setResponse($this->mockResponse, StatusCodes::FATAL);
        $this->assertSame($this->assertions, $this->assertions->isFatal());
    }

    /**
     * Tests asserting that the status code is OK
     */
    public function testAssertStatusCodeIsOK()
    {
        $this->assertions->setResponse($this->mockResponse, StatusCodes::OK);
        $this->assertSame($this->assertions, $this->assertions->isOK());
    }

    /**
     * Tests asserting that the status code is a warning
     */
    public function testAssertStatusCodeIsWarning()
    {
        $this->assertions->setResponse($this->mockResponse, StatusCodes::WARNING);
        $this->assertSame($this->assertions, $this->assertions->isWarning());
    }

    /**
     * Tests asserting that the status code equals the right value
     */
    public function testAssertingStatusCodeEquals()
    {
        $this->assertions->setResponse($this->mockResponse, StatusCodes::OK);
        $this->assertSame($this->assertions, $this->assertions->statusCodeEquals(StatusCodes::OK));
    }
}
