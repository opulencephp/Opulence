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
 * Defines a class with a default value object
 */
final class ConstructorWithDefaultValueObject
{
    private $foo;

    public function __construct(\DateTime $foo = null)
    {
        $this->foo = $foo ?? new \DateTime();
    }

    public function getFoo(): \DateTime
    {
        return $this->foo;
    }
}
