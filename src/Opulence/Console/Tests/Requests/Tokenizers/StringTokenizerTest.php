<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Console\Tests\Requests\Tokenizers;

use Opulence\Console\Requests\Tokenizers\StringTokenizer;
use RuntimeException;

/**
 * Tests the string tokenizer
 */
class StringTokenizerTest extends \PHPUnit\Framework\TestCase
{
    /** @var StringTokenizer The tokenizer to use in tests */
    private $tokenizer;

    protected function setUp(): void
    {
        $this->tokenizer = new StringTokenizer();
    }

    public function testTokenizingArgumentAndOptionWithSpaceAroundIt(): void
    {
        $tokens = $this->tokenizer->tokenize("foo ' dave ' --last=' young '");
        $this->assertEquals([
            'foo',
            "' dave '",
            "--last=' young '"
        ], $tokens);
    }

    public function testTokenizingDoubleQuoteInsideSingleQuotes(): void
    {
        $tokens = $this->tokenizer->tokenize("foo '\"foo bar\"' --quote '\"Dave is cool\"'");
        $this->assertEquals([
            'foo',
            '\'"foo bar"\'',
            '--quote',
            '\'"Dave is cool"\'',
        ], $tokens);
    }

    public function testTokenizingOptionValueWithSpace(): void
    {
        $tokens = $this->tokenizer->tokenize("foo --name 'dave young'");
        $this->assertEquals([
            'foo',
            '--name',
            "'dave young'"
        ], $tokens);
    }

    public function testTokenizingSingleQuoteInsideDoubleQuotes(): void
    {
        $tokens = $this->tokenizer->tokenize("foo \"'foo bar'\" --quote \"'Dave is cool'\"");
        $this->assertEquals([
            'foo',
            "\"'foo bar'\"",
            '--quote',
            "\"'Dave is cool'\""
        ], $tokens);
    }

    public function testTokenizingUnclosedDoubleQuote(): void
    {
        $this->expectException(RuntimeException::class);
        $this->tokenizer->tokenize('foo "blah');
    }

    public function testTokenizingUnclosedSingleQuote(): void
    {
        $this->expectException(RuntimeException::class);
        $this->tokenizer->tokenize("foo 'blah");
    }

    public function testTokenizingWithExtraSpacesBetweenTokens(): void
    {
        $tokens = $this->tokenizer->tokenize(" foo   bar  --name='dave   young'  -r ");
        $this->assertEquals([
            'foo',
            'bar',
            "--name='dave   young'",
            '-r'
        ], $tokens);
    }
}
