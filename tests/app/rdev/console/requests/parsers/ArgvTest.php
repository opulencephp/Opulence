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
     * This is just a placeholder
     */
    public function testNothing()
    {
        // TODO:  Remove
        $this->assertTrue(true);
    }
}