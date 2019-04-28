<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Redis\Types\Factories;

use Opulence\Redis\Types\TypeMapper;

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
    public function createTypeMapper(): TypeMapper
    {
        return new TypeMapper();
    }
}
