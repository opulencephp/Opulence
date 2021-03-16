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
 * Mocks another class that implements a simple interface
 */
class Blah implements IFoo
{
    /**
     * @inheritdoc
     */
    public function getClassName()
    {
        return __CLASS__;
    }
}
