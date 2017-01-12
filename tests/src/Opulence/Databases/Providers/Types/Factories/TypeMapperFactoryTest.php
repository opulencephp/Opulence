<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Databases\Providers\Types\Factories;

use Opulence\Databases\Providers\Provider;
use Opulence\Databases\Providers\Types\TypeMapper;

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
        /** @var Provider|\PHPUnit\Framework\TestCase $provider */
        $provider = $this->getMockForAbstractClass(Provider::class);
        $this->assertInstanceOf(TypeMapper::class, (new TypeMapperFactory)->createTypeMapper($provider));
    }
}
