<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the string parser
 */
namespace RDev\Console\Requests\Parsers;

class StringTest extends \PHPUnit_Framework_TestCase 
{
    /** @var \RDev\Console\Requests\Parsers\String The parser to use in tests */
    private $parser = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
       $this->parser = new String();
    }

    /**
     * Tests a double quote inside single quotes
     */
    public function testDoubleQuoteInsideSingleQuotes()
    {
        $request = $this->parser->parse("foo --quote '\"Dave is cool\"'");
        $this->assertEquals("foo", $request->getCommandName());
        $this->assertEquals([], $request->getArgumentValues());
        $this->assertEquals('"Dave is cool"', $request->getOptionValue("quote"));
    }

    /**
     * Tests parsing argument and short option and long option
     */
    public function testParsingArgumentShortOptionLongOption()
    {
        $request = $this->parser->parse("foo bar -r --name=dave");
        $this->assertEquals("foo", $request->getCommandName());
        $this->assertEquals(["bar"], $request->getArgumentValues());
        $this->assertNull($request->getOptionValue("r"));
        $this->assertEquals("dave", $request->getOptionValue("name"));
    }

    /**
     * Tests parsing an array long option with an equals sign
     */
    public function testParsingArrayLongOptionWithEqualsSign()
    {
        $request = $this->parser->parse("foo --name=dave --name=young");
        $this->assertEquals("foo", $request->getCommandName());
        $this->assertEquals([], $request->getArgumentValues());
        $this->assertEquals(["dave", "young"], $request->getOptionValue("name"));
    }

    /**
     * Tests parsing an array long option without an equals sign
     */
    public function testParsingArrayLongOptionWithoutEqualsSign()
    {
        $request = $this->parser->parse("foo --name dave --name young");
        $this->assertEquals("foo", $request->getCommandName());
        $this->assertEquals([], $request->getArgumentValues());
        $this->assertEquals(["dave", "young"], $request->getOptionValue("name"));
    }

    /**
     * Tests parsing just a command name
     */
    public function testParsingCommandName()
    {
        $request = $this->parser->parse("foo");
        $this->assertEquals("foo", $request->getCommandName());
        $this->assertEquals([], $request->getArgumentValues());
        $this->assertEquals([], $request->getOptionValues());
    }

    /**
     * Tests parsing long option with equals sign
     */
    public function testParsingLongOptionWithEqualsSign()
    {
        $request = $this->parser->parse("foo --name=dave");
        $this->assertEquals("foo", $request->getCommandName());
        $this->assertEquals([], $request->getArgumentValues());
        $this->assertEquals("dave", $request->getOptionValue("name"));
    }

    /**
     * Tests parsing long option without an equals sign
     */
    public function testParsingLongOptionWithoutEqualsSign()
    {
        $request = $this->parser->parse("foo --name dave");
        $this->assertEquals("foo", $request->getCommandName());
        $this->assertEquals([], $request->getArgumentValues());
        $this->assertEquals("dave", $request->getOptionValue("name"));
    }

    /**
     * Tests parsing long option without an equals sign with an argument after
     */
    public function testParsingLongOptionWithoutEqualsSignWithArgumentAfter()
    {
        $request = $this->parser->parse("foo --name dave bar");
        $this->assertEquals("foo", $request->getCommandName());
        $this->assertEquals(["bar"], $request->getArgumentValues());
        $this->assertEquals("dave", $request->getOptionValue("name"));
    }

    /**
     * Tests parsing long option without an equals sign with quoted value
     */
    public function testParsingLongOptionWithoutEqualsSignWithQuotedValue()
    {
        $request = $this->parser->parse("foo --first 'dave' --last=\"young\"");
        $this->assertEquals("foo", $request->getCommandName());
        $this->assertEquals([], $request->getArgumentValues());
        $this->assertEquals("dave", $request->getOptionValue("first"));
        $this->assertEquals("young", $request->getOptionValue("last"));
    }

    /**
     * Tests parsing multiple arguments
     */
    public function testParsingMultipleArgument()
    {
        $request = $this->parser->parse("foo bar baz blah");
        $this->assertEquals("foo", $request->getCommandName());
        $this->assertEquals(["bar", "baz", "blah"], $request->getArgumentValues());
        $this->assertEquals([], $request->getOptionValues());
    }

    /**
     * Tests parsing multiple separate short options
     */
    public function testParsingMultipleSeparateShortOptions()
    {
        $request = $this->parser->parse("foo -r -f -d");
        $this->assertEquals("foo", $request->getCommandName());
        $this->assertNull($request->getOptionValue("r"));
        $this->assertNull($request->getOptionValue("f"));
        $this->assertNull($request->getOptionValue("d"));
        $this->assertEquals([], $request->getArgumentValues());
    }

    /**
     * Tests parsing multiple short options
     */
    public function testParsingMultipleShortOptions()
    {
        $request = $this->parser->parse("foo -rfd");
        $this->assertEquals("foo", $request->getCommandName());
        $this->assertNull($request->getOptionValue("r"));
        $this->assertNull($request->getOptionValue("f"));
        $this->assertNull($request->getOptionValue("d"));
        $this->assertEquals([], $request->getArgumentValues());
    }

    /**
     * Tests parsing option value with space in it
     */
    public function testParsingOptionValueWithSpace()
    {
        $request = $this->parser->parse("foo --name 'dave young'");
        $this->assertEquals("foo", $request->getCommandName());
        $this->assertEquals([], $request->getArgumentValues());
        $this->assertEquals("dave young", $request->getOptionValue("name"));
    }

    /**
     * Tests parsing a single argument
     */
    public function testParsingSingleArgument()
    {
        $request = $this->parser->parse("foo bar");
        $this->assertEquals("foo", $request->getCommandName());
        $this->assertEquals(["bar"], $request->getArgumentValues());
        $this->assertEquals([], $request->getOptionValues());
    }

    /**
     * Tests parsing a single short option
     */
    public function testParsingSingleShortOption()
    {
        $request = $this->parser->parse("foo -r");
        $this->assertEquals("foo", $request->getCommandName());
        $this->assertNull($request->getOptionValue("r"));
        $this->assertEquals([], $request->getArgumentValues());
    }

    /**
     * Tests parsing with extra spaces between tokens
     */
    public function testParsingWithExtraSpacesBetweenTokens()
    {
        $request = $this->parser->parse(" foo   bar  --name='dave   young'  -r ");
        $this->assertEquals("foo", $request->getCommandName());
        $this->assertEquals(["bar"], $request->getArgumentValues());
        $this->assertEquals("dave   young", $request->getOptionValue("name"));
        $this->assertNull($request->getOptionValue("r"));
    }

    /**
     * Tests passing an unclosed double quote
     */
    public function testPassingUnclosedDoubleQuote()
    {
        $this->setExpectedException("\\RuntimeException");
        $this->parser->parse('foo "blah');
    }

    /**
     * Tests passing an unclosed single quote
     */
    public function testPassingUnclosedSingleQuote()
    {
        $this->setExpectedException("\\RuntimeException");
        $this->parser->parse("foo 'blah");
    }

    /**
     * Tests a single quote inside double quotes
     */
    public function testSingleQuoteInsideDoubleQuotes()
    {
        $request = $this->parser->parse("foo --quote \"'Dave is cool'\"");
        $this->assertEquals("foo", $request->getCommandName());
        $this->assertEquals([], $request->getArgumentValues());
        $this->assertEquals("'Dave is cool'", $request->getOptionValue("quote"));
    }
}