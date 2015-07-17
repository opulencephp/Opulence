<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the argv parser
 */
namespace Opulence\Console\Requests\Parsers;
use InvalidArgumentException;

class ArgvParserTest extends \PHPUnit_Framework_TestCase
{
    /** @var ArgvParser The parser to use in tests */
    private $parser = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->parser = new ArgvParser();
    }

    /**
     * Tests parsing arguments and options
     */
    public function testParsingArgumentsAndOptions()
    {
        $request = $this->parser->parse(["opulence", "foo", "bar", "-r", "--name=dave"]);
        $this->assertEquals("foo", $request->getCommandName());
        $this->assertEquals(["bar"], $request->getArgumentValues());
        $this->assertNull($request->getOptionValue("r"));
        $this->assertEquals("dave", $request->getOptionValue("name"));
    }

    /**
     * Tests parsing a null string
     */
    public function testParsingNullString()
    {
        $_SERVER["argv"] = ["opulence", "foo", "bar", "-r", "--name=dave"];
        $request = $this->parser->parse(null);
        $this->assertEquals("foo", $request->getCommandName());
        $this->assertEquals(["bar"], $request->getArgumentValues());
        $this->assertNull($request->getOptionValue("r"));
        $this->assertEquals("dave", $request->getOptionValue("name"));
    }

    /**
     * Tests parsing option with no value
     */
    public function testParsingOptionWithNoValue()
    {
        $request = $this->parser->parse(["opulence", "foo", "--name"]);
        $this->assertNull($request->getOptionValue("name"));
    }

    /**
     * Tests passing in an invalid input type
     */
    public function testPassingInvalidInputType()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $this->parser->parse("foo");
    }
}