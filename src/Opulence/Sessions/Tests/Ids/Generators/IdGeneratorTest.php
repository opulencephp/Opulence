<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Sessions\Tests\Ids\Generators;

use Opulence\Sessions\Ids\Generators\IdGenerator;

/**
 * Tests the Id generator
 */
class IdGeneratorTest extends \PHPUnit\Framework\TestCase
{
    /** @var IdGenerator The Id generator to use in tests */
    private $generator = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->generator = new IdGenerator();
    }

    /**
     * Tests generating an Id with a length specified
     */
    public function testGeneratingWithLength() : void
    {
        $id = $this->generator->generate(28);
        $this->assertTrue(is_string($id));
        $this->assertEquals(28, strlen($id));
    }

    /**
     * Tests generating an Id without a length specified
     */
    public function testGeneratingWithoutLength() : void
    {
        $id = $this->generator->generate();
        $this->assertTrue(is_string($id));
        $this->assertEquals(IdGenerator::DEFAULT_LENGTH, strlen($id));
    }

    /**
     * Tests validating an invalid Id
     */
    public function testValidatingInvalidId() : void
    {
        // Invalid characters
        $id = str_repeat('#', IdGenerator::DEFAULT_LENGTH);
        $this->assertFalse($this->generator->idIsValid($id));
        // Too short
        $id = str_repeat(1, IdGenerator::MIN_LENGTH - 1);
        $this->assertFalse($this->generator->idIsValid($id));
        // Incorrect type
        $id = ['foo'];
        $this->assertFalse($this->generator->idIsValid($id));
        // Longer than max length
        $id = str_repeat(1, IdGenerator::MAX_LENGTH + 1);
        $this->assertFalse($this->generator->idIsValid($id));
    }

    /**
     * Tests validating a valid Id
     */
    public function testValidatingValidId() : void
    {
        // Default length
        $id = str_repeat('1', IdGenerator::DEFAULT_LENGTH);
        $this->assertTrue($this->generator->idIsValid($id));
        // The min length
        $id = str_repeat(1, IdGenerator::MIN_LENGTH);
        $this->assertTrue($this->generator->idIsValid($id));
        // The max length
        $id = str_repeat(1, IdGenerator::MAX_LENGTH);
        $this->assertTrue($this->generator->idIsValid($id));
        // Mix of characters
        $id = 'aA1' . str_repeat(2, IdGenerator::DEFAULT_LENGTH - 3);
        $this->assertTrue($this->generator->idIsValid($id));
    }
}
