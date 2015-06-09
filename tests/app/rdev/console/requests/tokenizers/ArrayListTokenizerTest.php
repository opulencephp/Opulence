<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the array list tokenizer
 */
namespace RDev\Console\Requests\Tokenizers;
use RuntimeException;

class ArrayListTokenizerTest extends \PHPUnit_Framework_TestCase
{
    /** @var ArrayListTokenizer The tokenizer to use in tests */
    private $tokenizer = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->tokenizer = new ArrayListTokenizer();
    }

    /**
     * Test not passing the command name
     */
    public function testNotPassingCommandName()
    {
        $this->setExpectedException(RuntimeException::class);
        $this->tokenizer->tokenize([
            "foo" => "bar"
        ]);
    }

    /**
     * Tests tokenizing arguments and options
     */
    public function testTokenizingArgumentsAndOptions()
    {
        $tokens = $this->tokenizer->tokenize([
            "name" => "foo",
            "arguments" => ["bar"],
            "options" => ["--name=dave", "-r"]
        ]);
        $this->assertEquals(["foo", "bar", "--name=dave", "-r"], $tokens);
    }
}