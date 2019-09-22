<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Console\Tests\Requests\Parsers;

use Opulence\Console\Requests\Parsers\StringParser;
use Opulence\Console\Requests\Tokenizers\StringTokenizer;

/**
 * Tests the string parser
 */
class StringParserTest extends \PHPUnit\Framework\TestCase
{
    /** @var StringParser The parser to use in tests */
    private $parser;

    protected function setUp(): void
    {
        $this->parser = new StringParser(new StringTokenizer());
    }

    public function testBackslashesAreRespected(): void
    {
        $request = $this->parser->parse('foo bar\\baz');
        $this->assertEquals(['bar\\baz'], $request->getArgumentValues());
    }

    public function testParsingArgumentShortOptionLongOption(): void
    {
        $request = $this->parser->parse('foo bar -r --name=dave');
        $this->assertEquals('foo', $request->getCommandName());
        $this->assertEquals(['bar'], $request->getArgumentValues());
        $this->assertNull($request->getOptionValue('r'));
        $this->assertEquals('dave', $request->getOptionValue('name'));
    }

    public function testParsingArrayLongOptionWithEqualsSign(): void
    {
        $request = $this->parser->parse('foo --name=dave --name=young');
        $this->assertEquals('foo', $request->getCommandName());
        $this->assertEquals([], $request->getArgumentValues());
        $this->assertEquals(['dave', 'young'], $request->getOptionValue('name'));
    }

    public function testParsingArrayLongOptionWithoutEqualsSign(): void
    {
        $request = $this->parser->parse('foo --name dave --name young');
        $this->assertEquals('foo', $request->getCommandName());
        $this->assertEquals([], $request->getArgumentValues());
        $this->assertEquals(['dave', 'young'], $request->getOptionValue('name'));
    }

    public function testParsingCommandName(): void
    {
        $request = $this->parser->parse('foo');
        $this->assertEquals('foo', $request->getCommandName());
        $this->assertEquals([], $request->getArgumentValues());
        $this->assertEquals([], $request->getOptionValues());
    }

    public function testParsingLongOptionWithEqualsSign(): void
    {
        $request = $this->parser->parse('foo --name=dave');
        $this->assertEquals('foo', $request->getCommandName());
        $this->assertEquals([], $request->getArgumentValues());
        $this->assertEquals('dave', $request->getOptionValue('name'));
    }

    public function testParsingLongOptionWithoutEqualsSign(): void
    {
        $request = $this->parser->parse('foo --name dave');
        $this->assertEquals('foo', $request->getCommandName());
        $this->assertEquals([], $request->getArgumentValues());
        $this->assertEquals('dave', $request->getOptionValue('name'));
    }

    public function testParsingLongOptionWithoutEqualsSignWithArgumentAfter(): void
    {
        $request = $this->parser->parse('foo --name dave bar');
        $this->assertEquals('foo', $request->getCommandName());
        $this->assertEquals(['bar'], $request->getArgumentValues());
        $this->assertEquals('dave', $request->getOptionValue('name'));
    }

    public function testParsingLongOptionWithoutEqualsSignWithQuotedValue(): void
    {
        $request = $this->parser->parse("foo --first 'dave' --last=\"young\"");
        $this->assertEquals('foo', $request->getCommandName());
        $this->assertEquals([], $request->getArgumentValues());
        $this->assertEquals('dave', $request->getOptionValue('first'));
        $this->assertEquals('young', $request->getOptionValue('last'));
    }

    public function testParsingMultipleArgument(): void
    {
        $request = $this->parser->parse('foo bar baz blah');
        $this->assertEquals('foo', $request->getCommandName());
        $this->assertEquals(['bar', 'baz', 'blah'], $request->getArgumentValues());
        $this->assertEquals([], $request->getOptionValues());
    }

    public function testParsingMultipleSeparateShortOptions(): void
    {
        $request = $this->parser->parse('foo -r -f -d');
        $this->assertEquals('foo', $request->getCommandName());
        $this->assertNull($request->getOptionValue('r'));
        $this->assertNull($request->getOptionValue('f'));
        $this->assertNull($request->getOptionValue('d'));
        $this->assertEquals([], $request->getArgumentValues());
    }

    public function testParsingMultipleShortOptions(): void
    {
        $request = $this->parser->parse('foo -rfd');
        $this->assertEquals('foo', $request->getCommandName());
        $this->assertNull($request->getOptionValue('r'));
        $this->assertNull($request->getOptionValue('f'));
        $this->assertNull($request->getOptionValue('d'));
        $this->assertEquals([], $request->getArgumentValues());
    }

    public function testParsingSingleArgument(): void
    {
        $request = $this->parser->parse('foo bar');
        $this->assertEquals('foo', $request->getCommandName());
        $this->assertEquals(['bar'], $request->getArgumentValues());
        $this->assertEquals([], $request->getOptionValues());
    }

    public function testParsingSingleShortOption(): void
    {
        $request = $this->parser->parse('foo -r');
        $this->assertEquals('foo', $request->getCommandName());
        $this->assertNull($request->getOptionValue('r'));
        $this->assertEquals([], $request->getArgumentValues());
    }

    public function testParsingTwoConsecutiveLongOptions(): void
    {
        $request = $this->parser->parse('foo --bar --baz');
        $this->assertEquals('foo', $request->getCommandName());
        $this->assertEquals([], $request->getArgumentValues());
        $this->assertEquals(null, $request->getOptionValue('bar'));
        $this->assertEquals(null, $request->getOptionValue('baz'));
    }
}
