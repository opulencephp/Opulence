<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the long option parser
 */
namespace RDev\Console\Requests\Parsers;

class LongOptionTest extends \PHPUnit_Framework_TestCase 
{
    /** @var LongOption The parser to use in tests */
    private $parser = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->parser = new LongOption();
    }

    /**
     * Tests parsing an invalid long option
     */
    public function testParsingInvalidLongOption()
    {
        $this->setExpectedException("\\RuntimeException");
        $tokens = ["foo"];
        $this->parser->parse($tokens[0], $tokens);
    }

    /**
     * Tests parsing option with an equals sign
     */
    public function testParsingOptionWithEqualsSign()
    {
        $tokens = [];
        $option = $this->parser->parse("--foo=bar", $tokens);
        $this->assertEquals(["foo", "bar"], $option);
    }

    /**
     * Tests parsing option without equals sign
     */
    public function testParsingOptionWithoutEqualsSign()
    {
        $tokens = ["bar"];
        $option = $this->parser->parse("--foo", $tokens);
        $this->assertEquals(["foo", "bar"], $option);
    }
}