<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Databases\Tests\Providers\Types\Factories;

use Opulence\Databases\Providers\Provider;
use Opulence\Databases\Providers\Types\Factories\TypeMapperFactory;
use Opulence\Databases\Providers\Types\TypeMapper;

/**
 * Tests the type mapper factory
 */
class TypeMapperFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testTypeMapperIsCreated(): void
    {
        /** @var Provider|\PHPUnit\Framework\TestCase $provider */
        $provider = $this->getMockForAbstractClass(Provider::class);
        $this->assertInstanceOf(TypeMapper::class, (new TypeMapperFactory)->createTypeMapper($provider));
    }
}
