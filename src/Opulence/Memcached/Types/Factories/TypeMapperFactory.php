<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Memcached\Types\Factories;

use Opulence\Memcached\Types\TypeMapper;

/**
 * Defines the type mapper factory
 */
class TypeMapperFactory
{
    /**
     * Creates a type mapper
     *
     * @return TypeMapper The type mapper
     */
    public function createTypeMapper() : TypeMapper
    {
        return new TypeMapper();
    }
}
