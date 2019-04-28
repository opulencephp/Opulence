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

use Opulence\Console\Responses\Compilers\Compiler;
use Opulence\Console\Responses\Compilers\Lexers\Lexer;
use Opulence\Console\Responses\Compilers\Parsers\Parser;
use Opulence\Console\Tests\Responses\Mocks\Response;

/**
 * Tests the response class
 */
class ResponseTest extends \PHPUnit\Framework\TestCase
{
    /** @var Response The response to use in tests */
    private $response;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->response = new Response(new Compiler(new Lexer(), new Parser()));
    }

    /**
     * Tests clearing the response
     */
    public function testClearingResponse(): void
    {
        ob_start();
        $this->response->clear();
        $this->assertEquals(chr(27) . '[2J' . chr(27) . '[;H', ob_get_clean());
    }

    /**
     * Tests writing multiple messages with new lines
     */
    public function testWritingMultipleMessagesWithNewLines(): void
    {
        ob_start();
        $this->response->writeln(['foo', 'bar']);
        $this->assertEquals('foo' . PHP_EOL . 'bar' . PHP_EOL, ob_get_clean());
    }

    /**
     * Tests writing multiple messages with no new lines
     */
    public function testWritingMultipleMessagesWithNoNewLines(): void
    {
        ob_start();
        $this->response->write(['foo', 'bar']);
        $this->assertEquals('foobar', ob_get_clean());
    }

    /**
     * Tests writing a single message with a new line
     */
    public function testWritingSingleMessageWithNewLine(): void
    {
        ob_start();
        $this->response->writeln('foo');
        $this->assertEquals('foo' . PHP_EOL, ob_get_clean());
    }

    /**
     * Tests writing a single message with no new line
     */
    public function testWritingSingleMessageWithNoNewLine(): void
    {
        ob_start();
        $this->response->write('foo');
        $this->assertEquals('foo', ob_get_clean());
    }

    /**
     * Tests writing a styled message with styling disabled
     */
    public function testWritingStyledMessageWithStylingDisabled(): void
    {
        ob_start();
        $this->response->setStyled(false);
        $this->response->write('<b>foo</b>');
        $this->assertEquals('foo', ob_get_clean());
    }
}
