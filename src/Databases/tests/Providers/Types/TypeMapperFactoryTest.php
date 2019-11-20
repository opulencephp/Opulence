<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Databases\Tests\Providers\Types;

use Opulence\Databases\Providers\Provider;
use Opulence\Databases\Providers\Types\TypeMapperFactory;
use Opulence\Databases\Providers\Types\TypeMapper;
use PHPUnit\Framework\TestCase;

/**
 * Tests the type mapper factory
 */
class TypeMapperFactoryTest extends TestCase
{
    public function testTypeMapperIsCreated(): void
    {
        /** @var Provider|TestCase $provider */
        $provider = $this->getMockForAbstractClass(Provider::class);
        $this->assertInstanceOf(TypeMapper::class, (new TypeMapperFactory)->createTypeMapper($provider));
    }
}
