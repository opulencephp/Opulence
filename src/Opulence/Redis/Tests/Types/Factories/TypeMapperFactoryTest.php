<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Redis\Tests\Types\Factories;

use Opulence\Redis\Types\Factories\TypeMapperFactory;
use Opulence\Redis\Types\TypeMapper;

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
