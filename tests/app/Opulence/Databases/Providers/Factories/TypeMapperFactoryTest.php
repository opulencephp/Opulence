<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Databases\Providers\Factories;

use Opulence\Databases\Providers\Provider;
use Opulence\Databases\Providers\TypeMapper;

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
        /** @var Provider|\PHPUnit_Framework_TestCase $provider */
        $provider = $this->getMockForAbstractClass(Provider::class);
        $this->assertInstanceOf(TypeMapper::class, (new TypeMapperFactory)->create($provider));
    }
}