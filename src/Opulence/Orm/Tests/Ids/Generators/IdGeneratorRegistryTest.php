<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Orm\Tests\Ids\Generators;

use Opulence\Orm\Ids\Generators\IdGeneratorRegistry;
use Opulence\Orm\Ids\Generators\IIdGenerator;
use PHPUnit\Framework\MockObject\MockObject;

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
    protected function setUp() : void
    {
        $this->registry = new IdGeneratorRegistry();
    }

    /**
     * Tests that the correct instance is returned after registering a generator
     */
    public function testCorrectInstanceReturnedAfterRegisteringGenerator() : void
    {
        /** @var IIdGenerator|MockObject $generator */
        $generator = $this->createMock(IIdGenerator::class);
        $this->registry->registerIdGenerator('foo', $generator);
        $this->assertSame($generator, $this->registry->getIdGenerator('foo'));
    }

    /**
     * Tests null is returned for non-existent generator
     */
    public function testNullReturnedForNonExistentGenerator() : void
    {
        $this->assertNull($this->registry->getIdGenerator('foo'));
    }
}
