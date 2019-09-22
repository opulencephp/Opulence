<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Redis\Tests\Types\Factories;

use Opulence\Redis\Types\Factories\TypeMapperFactory;
use Opulence\Redis\Types\TypeMapper;

/**
 * Tests the type mapper factory
 */
class TypeMapperFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testTypeMapperIsCreated(): void
    {
        $this->assertInstanceOf(TypeMapper::class, (new TypeMapperFactory)->createTypeMapper());
    }
}
