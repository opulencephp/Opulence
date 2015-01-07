<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the argv tokenizer
 */
namespace RDev\Console\Requests\Parsers\Tokenizers;

class ArgvTest extends \PHPUnit_Framework_TestCase 
{
    /** @var Argv The tokenizer to use in tests */
    private $tokenizer = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->tokenizer = new Argv();
    }

    /**
     * Tests tokenizing an escaped double quote
     */
    public function testTokenizingEscapedDoubleQuote()
    {
        $tokens = $this->tokenizer->tokenize(['foo', 'Dave\"s']);
        $this->assertEquals(['Dave"s'], $tokens);
    }

    /**
     * Tests tokenizing an escaped single quote
     */
    public function testTokenizingEscapedSingleQuote()
    {
        $tokens = $this->tokenizer->tokenize(["foo", "Dave\'s"]);
        $this->assertEquals(["Dave's"], $tokens);
    }
}