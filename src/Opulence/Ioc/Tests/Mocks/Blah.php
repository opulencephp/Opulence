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
 * Mocks another class that implements a simple interface
 */
class Blah implements IFoo
{
    /**
     * @inheritdoc
     */
    public function getClassName() : string
    {
        return __CLASS__;
    }
}
