<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the Id generator
 */
namespace RDev\Sessions\Ids;
use RDev\Cryptography\Utilities\Strings;

class IdGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /** @var IdGenerator The Id generator to use in tests */
    private $generator = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->generator = new IdGenerator(new Strings());
    }

    /**
     * Tests generating an Id with a length specified
     */
    public function testGeneratingWithLength()
    {
        $id = $this->generator->generate(28);
        $this->assertTrue(is_string($id));
        $this->assertEquals(28, strlen($id));
    }

    /**
     * Tests generating an Id without a length specified
     */
    public function testGeneratingWithoutLength()
    {
        $id = $this->generator->generate();
        $this->assertTrue(is_string($id));
        $this->assertEquals(IdGenerator::DEFAULT_LENGTH, strlen($id));
    }
}