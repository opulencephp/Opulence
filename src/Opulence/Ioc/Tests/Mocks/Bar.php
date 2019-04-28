<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Ioc\Tests\Mocks;

/**
 * Mocks a class that implements a simple interface
 */
class Bar implements IFoo
{
    /**
     * @inheritdoc
     */
    public function getClassName() : string
    {
        return __CLASS__;
    }
}
