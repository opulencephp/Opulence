<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Console\Tests\Responses;

use InvalidArgumentException;
use Opulence\Console\Responses\Compilers\Compiler;
use Opulence\Console\Responses\Compilers\Lexers\Lexer;
use Opulence\Console\Responses\Compilers\Parsers\Parser;
use Opulence\Console\Responses\StreamResponse;

/**
 * Tests the stream response
 */
class StreamResponseTest extends \PHPUnit\Framework\TestCase
{
    /** @var StreamResponse The response to use in tests */
    private $response;
    /** @var Compiler The compiler to use in tests */
    private $compiler;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->compiler = new Compiler(new Lexer(), new Parser());
        $this->response = new StreamResponse(fopen('php://memory', 'wb'), $this->compiler);
    }

    /**
     * Tests getting the stream
     */
    public function testGettingStream(): void
    {
        $this->assertTrue(is_resource($this->response->getStream()));
    }

    /**
     * Tests an invalid stream
     */
    public function testInvalidStream(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new StreamResponse('foo', $this->compiler);
    }

    /**
     * Test writing an array message
     */
    public function testWriteOnArray(): void
    {
        $this->response->write(['foo', 'bar']);
        rewind($this->response->getStream());
        $this->assertEquals('foobar', stream_get_contents($this->response->getStream()));
    }

    /**
     * Tests writing a string message
     */
    public function testWriteOnString(): void
    {
        $this->response->write('foo');
        rewind($this->response->getStream());
        $this->assertEquals('foo', stream_get_contents($this->response->getStream()));
    }

    /**
     * Test writing an array message to a line
     */
    public function testWritelnOnArray(): void
    {
        $this->response->writeln(['foo', 'bar']);
        rewind($this->response->getStream());
        $this->assertEquals('foo' . PHP_EOL . 'bar' . PHP_EOL, stream_get_contents($this->response->getStream()));
    }

    /**
     * Tests writing a string message to a line
     */
    public function testWritelnOnString(): void
    {
        $this->response->writeln('foo');
        rewind($this->response->getStream());
        $this->assertEquals('foo' . PHP_EOL, stream_get_contents($this->response->getStream()));
    }
}
