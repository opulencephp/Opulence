<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Tests\Console\Testing\PhpUnit\Assertions;

use Opulence\Console\Responses\StreamResponse;
use Opulence\Console\StatusCodes;
use Opulence\Framework\Console\Testing\PhpUnit\Assertions\ResponseAssertions;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Tests the response assertions
 */
class ResponseAssertionsTest extends \PHPUnit\Framework\TestCase
{
    private ResponseAssertions $assertions;
    /** @var StreamResponse|MockObject The response to use in tests */
    private StreamResponse $mockResponse;

    protected function setUp(): void
    {
        $this->assertions = new ResponseAssertions();
        $this->mockResponse = $this->getMockBuilder(StreamResponse::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testAssertStatusCodeIsError(): void
    {
        $this->assertions->setResponse($this->mockResponse, StatusCodes::ERROR);
        $this->assertSame($this->assertions, $this->assertions->isError());
    }

    public function testAssertStatusCodeIsFatal(): void
    {
        $this->assertions->setResponse($this->mockResponse, StatusCodes::FATAL);
        $this->assertSame($this->assertions, $this->assertions->isFatal());
    }

    public function testAssertStatusCodeIsOK(): void
    {
        $this->assertions->setResponse($this->mockResponse, StatusCodes::OK);
        $this->assertSame($this->assertions, $this->assertions->isOK());
    }

    public function testAssertStatusCodeIsWarning(): void
    {
        $this->assertions->setResponse($this->mockResponse, StatusCodes::WARNING);
        $this->assertSame($this->assertions, $this->assertions->isWarning());
    }

    public function testAssertingStatusCodeEquals(): void
    {
        $this->assertions->setResponse($this->mockResponse, StatusCodes::OK);
        $this->assertSame($this->assertions, $this->assertions->statusCodeEquals(StatusCodes::OK));
    }
}
