<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Ioc;

interface IBinding
{
    /**
     * Gets whether or not this binding should be resolved as a singleton
     *
     * @return bool True if the binding should be resolved as a singleton, otherwise false
     */
    public function resolveAsSingleton() : bool;
}
