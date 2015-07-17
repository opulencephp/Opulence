<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the argv tokenizer
 */
namespace Opulence\Console\Requests\Tokenizers;

class ArgvTokenizerTest extends \PHPUnit_Framework_TestCase
{
    /** @var ArgvTokenizer The tokenizer to use in tests */
    private $tokenizer = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->tokenizer = new ArgvTokenizer();
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