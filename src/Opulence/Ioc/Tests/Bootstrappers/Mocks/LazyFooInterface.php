<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Ioc\Tests\Bootstrappers\Mocks;

/**
 * Defines an interface for a class that's used by bootstrappers
 */
interface LazyFooInterface
{
    /**
     * Gets the name of the concrete class
     *
     * @return string The name of the class
     */
    public function getClass() : string;
}
