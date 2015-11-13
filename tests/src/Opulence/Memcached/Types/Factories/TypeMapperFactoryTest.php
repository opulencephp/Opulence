<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Memcached\Types\Factories;

use Opulence\Memcached\Types\TypeMapper;

/**
 * Tests the type mapper factory
 */
class TypeMapperFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that a type mapper is created
     */
    public function testTypeMapperIsCreated()
    {
        $this->assertInstanceOf(TypeMapper::class, (new TypeMapperFactory)->create());
    }
}