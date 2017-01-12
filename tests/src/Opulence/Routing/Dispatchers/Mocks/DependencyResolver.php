<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Tests\Routing\Dispatchers\Mocks;

use Opulence\Routing\Dispatchers\IDependencyResolver;

/**
 * Mocks a dependency resolver
 */
class DependencyResolver implements IDependencyResolver
{
    /**
     * @inheritdoc
     */
    public function resolve(string $interface)
    {
        return (new \PHPUnit_Framework_MockObject_Generator)->getMock($interface, [], [], '', false);
    }
}
