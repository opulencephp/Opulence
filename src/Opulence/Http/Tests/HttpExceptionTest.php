<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Http\Tests;

use Exception;
use Opulence\Http\HttpException;

/**
 * Tests the HTTP exception
 */
class HttpExceptionTest extends \PHPUnit\Framework\TestCase
{
    /** @var HttpException The exception to use in tests */
    private $exception = null;
    /** @var Exception The previous exception */
    private $previousException = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->previousException = new Exception();
        $this->exception = new HttpException(
            404,
            'foo',
            ['bar' => 'baz'],
            4,
            $this->previousException
        );
    }

    /**
     * Tests getting the code
     */
    public function testGettingCode()
    {
        $this->assertEquals(4, $this->exception->getCode());
    }

    /**
     * Tests getting the headers
     */
    public function testGettingHeaders()
    {
        $this->assertEquals(['bar' => 'baz'], $this->exception->getHeaders());
    }

    /**
     * Tests getting the message
     */
    public function testGettingMessage()
    {
        $this->assertEquals('foo', $this->exception->getMessage());
    }

    /**
     * Tests getting the previous exception
     */
    public function testGettingPreviousException()
    {
        $this->assertSame($this->previousException, $this->exception->getPrevious());
    }

    /**
     * Tests getting the status code
     */
    public function testGettingStatusCode()
    {
        $this->assertEquals(404, $this->exception->getStatusCode());
    }
}
