<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Databases\Providers\Types\Factories;

use Opulence\Databases\Providers\Provider;
use Opulence\Databases\Providers\Types\TypeMapper;

/**
 * Defines the type mapper factory
 */
class TypeMapperFactory
{
    /**
     * Creates a type mapper from a provider
     *
     * @param Provider $provider The provider whose type mapper we're creating
     * @return TypeMapper The type mapper
     */
    public function createTypeMapper(Provider $provider): TypeMapper
    {
        return new TypeMapper($provider);
    }
}
