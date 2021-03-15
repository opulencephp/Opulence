<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Memcached\Tests\Types\Factories;

use Opulence\Memcached\Types\Factories\TypeMapperFactory;
use Opulence\Memcached\Types\TypeMapper;

/**
 * Tests the type mapper factory
 */
class TypeMapperFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests that a type mapper is created
     */
    public function testTypeMapperIsCreated()
    {
        $this->assertInstanceOf(TypeMapper::class, (new TypeMapperFactory)->createTypeMapper());
    }
}
