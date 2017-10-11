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
 * Mocks a class with a static setter method
 */
class StaticSetters
{
    /** @var IPerson A static dependency */
    public static $staticDependency = null;

    /**
     * @param IPerson $setterDependency
     */
    public static function setStaticSetterDependency(IPerson $setterDependency)
    {
        self::$staticDependency = $setterDependency;
    }
}
