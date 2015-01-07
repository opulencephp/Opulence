<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the argv parser
 */
namespace RDev\Console\Requests\Parsers;

class ArgvTest extends \PHPUnit_Framework_TestCase 
{
    /** @var Argv The parser to use in tests */
    private $parser = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->parser = new Argv();
    }

    /**
     * Tests parsing arguments and options
     */
    public function testParsingArgumentsAndOptions()
    {
        $request = $this->parser->parse("rdev foo bar -r --name=dave");
        $this->assertEquals("foo", $request->getCommandName());
        $this->assertEquals(["bar"], $request->getArgumentValues());
        $this->assertNull($request->getOptionValue("r"));
        $this->assertEquals("dave", $request->getOptionValue("name"));
    }
}