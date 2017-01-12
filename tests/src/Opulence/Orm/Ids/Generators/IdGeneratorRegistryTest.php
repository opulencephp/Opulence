<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Orm\Ids\Generators;

/**
 * Tests the Id generator registry
 */
class IdGeneratorRegistryTest extends \PHPUnit\Framework\TestCase
{
    /** @var IdGeneratorRegistry The registry to use in tests */
    private $registry = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->registry = new IdGeneratorRegistry();
    }

    /**
     * Tests that the correct instance is returned after registering a generator
     */
    public function testCorrectInstanceReturnedAfterRegisteringGenerator()
    {
        /** @var IIdGenerator|\PHPUnit_Framework_MockObject_MockObject $generator */
        $generator = $this->createMock(IIdGenerator::class);
        $this->registry->registerIdGenerator('foo', $generator);
        $this->assertSame($generator, $this->registry->getIdGenerator('foo'));
    }

    /**
     * Tests null is returned for non-existent generator
     */
    public function testNullReturnedForNonExistentGenerator()
    {
        $this->assertNull($this->registry->getIdGenerator('foo'));
    }
}
