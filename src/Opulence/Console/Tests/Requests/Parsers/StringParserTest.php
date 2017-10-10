<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Console\Tests\Requests\Parsers;

use Opulence\Console\Requests\Parsers\StringParser;
use Opulence\Console\Requests\Tokenizers\StringTokenizer;

/**
 * Tests the string parser
 */
class StringParserTest extends \PHPUnit\Framework\TestCase
{
    /** @var StringParser The parser to use in tests */
    private $parser = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->parser = new StringParser(new StringTokenizer());
    }

    /**
     * Tests that backslashes are respected
     */
    public function testBackslashesAreRespected()
    {
        $request = $this->parser->parse('foo bar\\baz');
        $this->assertEquals(['bar\\baz'], $request->getArgumentValues());
    }

    /**
     * Tests parsing argument and short option and long option
     */
    public function testParsingArgumentShortOptionLongOption()
    {
        $request = $this->parser->parse('foo bar -r --name=dave');
        $this->assertEquals('foo', $request->getCommandName());
        $this->assertEquals(['bar'], $request->getArgumentValues());
        $this->assertNull($request->getOptionValue('r'));
        $this->assertEquals('dave', $request->getOptionValue('name'));
    }

    /**
     * Tests parsing an array long option with an equals sign
     */
    public function testParsingArrayLongOptionWithEqualsSign()
    {
        $request = $this->parser->parse('foo --name=dave --name=young');
        $this->assertEquals('foo', $request->getCommandName());
        $this->assertEquals([], $request->getArgumentValues());
        $this->assertEquals(['dave', 'young'], $request->getOptionValue('name'));
    }

    /**
     * Tests parsing an array long option without an equals sign
     */
    public function testParsingArrayLongOptionWithoutEqualsSign()
    {
        $request = $this->parser->parse('foo --name dave --name young');
        $this->assertEquals('foo', $request->getCommandName());
        $this->assertEquals([], $request->getArgumentValues());
        $this->assertEquals(['dave', 'young'], $request->getOptionValue('name'));
    }

    /**
     * Tests parsing just a command name
     */
    public function testParsingCommandName()
    {
        $request = $this->parser->parse('foo');
        $this->assertEquals('foo', $request->getCommandName());
        $this->assertEquals([], $request->getArgumentValues());
        $this->assertEquals([], $request->getOptionValues());
    }

    /**
     * Tests parsing long option with equals sign
     */
    public function testParsingLongOptionWithEqualsSign()
    {
        $request = $this->parser->parse('foo --name=dave');
        $this->assertEquals('foo', $request->getCommandName());
        $this->assertEquals([], $request->getArgumentValues());
        $this->assertEquals('dave', $request->getOptionValue('name'));
    }

    /**
     * Tests parsing long option without an equals sign
     */
    public function testParsingLongOptionWithoutEqualsSign()
    {
        $request = $this->parser->parse('foo --name dave');
        $this->assertEquals('foo', $request->getCommandName());
        $this->assertEquals([], $request->getArgumentValues());
        $this->assertEquals('dave', $request->getOptionValue('name'));
    }

    /**
     * Tests parsing long option without an equals sign with an argument after
     */
    public function testParsingLongOptionWithoutEqualsSignWithArgumentAfter()
    {
        $request = $this->parser->parse('foo --name dave bar');
        $this->assertEquals('foo', $request->getCommandName());
        $this->assertEquals(['bar'], $request->getArgumentValues());
        $this->assertEquals('dave', $request->getOptionValue('name'));
    }

    /**
     * Tests parsing long option without an equals sign with quoted value
     */
    public function testParsingLongOptionWithoutEqualsSignWithQuotedValue()
    {
        $request = $this->parser->parse("foo --first 'dave' --last=\"young\"");
        $this->assertEquals('foo', $request->getCommandName());
        $this->assertEquals([], $request->getArgumentValues());
        $this->assertEquals('dave', $request->getOptionValue('first'));
        $this->assertEquals('young', $request->getOptionValue('last'));
    }

    /**
     * Tests parsing multiple arguments
     */
    public function testParsingMultipleArgument()
    {
        $request = $this->parser->parse('foo bar baz blah');
        $this->assertEquals('foo', $request->getCommandName());
        $this->assertEquals(['bar', 'baz', 'blah'], $request->getArgumentValues());
        $this->assertEquals([], $request->getOptionValues());
    }

    /**
     * Tests parsing multiple separate short options
     */
    public function testParsingMultipleSeparateShortOptions()
    {
        $request = $this->parser->parse('foo -r -f -d');
        $this->assertEquals('foo', $request->getCommandName());
        $this->assertNull($request->getOptionValue('r'));
        $this->assertNull($request->getOptionValue('f'));
        $this->assertNull($request->getOptionValue('d'));
        $this->assertEquals([], $request->getArgumentValues());
    }

    /**
     * Tests parsing multiple short options
     */
    public function testParsingMultipleShortOptions()
    {
        $request = $this->parser->parse('foo -rfd');
        $this->assertEquals('foo', $request->getCommandName());
        $this->assertNull($request->getOptionValue('r'));
        $this->assertNull($request->getOptionValue('f'));
        $this->assertNull($request->getOptionValue('d'));
        $this->assertEquals([], $request->getArgumentValues());
    }

    /**
     * Tests parsing a single argument
     */
    public function testParsingSingleArgument()
    {
        $request = $this->parser->parse('foo bar');
        $this->assertEquals('foo', $request->getCommandName());
        $this->assertEquals(['bar'], $request->getArgumentValues());
        $this->assertEquals([], $request->getOptionValues());
    }

    /**
     * Tests parsing a single short option
     */
    public function testParsingSingleShortOption()
    {
        $request = $this->parser->parse('foo -r');
        $this->assertEquals('foo', $request->getCommandName());
        $this->assertNull($request->getOptionValue('r'));
        $this->assertEquals([], $request->getArgumentValues());
    }

    /**
     * Tests parsing two consecutive long options
     */
    public function testParsingTwoConsecutiveLongOptions()
    {
        $request = $this->parser->parse('foo --bar --baz');
        $this->assertEquals('foo', $request->getCommandName());
        $this->assertEquals([], $request->getArgumentValues());
        $this->assertEquals(null, $request->getOptionValue('bar'));
        $this->assertEquals(null, $request->getOptionValue('baz'));
    }
}
