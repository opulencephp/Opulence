<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

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
    public static function setStaticSetterDependency(IPerson $setterDependency): void
    {
        self::$staticDependency = $setterDependency;
    }
}
