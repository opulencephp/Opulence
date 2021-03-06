<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Ioc\Tests\Mocks;

/**
 * Mocks a class that uses a __call magic method
 */
class MagicCallMethod
{
    /**
     * Handles non-existent methods
     *
     * @param string $name The name of the method called
     * @param array $arguments The arguments
     */
    public function __call($name, array $arguments)
    {
        return;
    }
}
