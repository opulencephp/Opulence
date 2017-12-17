<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Ioc\Tests\Mocks;

/**
 * Mocks a simple interface for use in testing
 */
interface IFoo
{
    /**
     * Gets the name of the concrete class
     *
     * @return string The name of the concrete class
     */
    public function getClassName() : string;
}
