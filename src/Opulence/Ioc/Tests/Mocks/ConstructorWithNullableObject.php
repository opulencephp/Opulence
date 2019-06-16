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
 * Mocks a class with a nullable object parameter
 */
final class ConstructorWithNullableObject
{
    private $foo;

    public function __construct(?\DateTime $foo)
    {
        $this->foo = $foo ?? new \DateTime();
    }

    public function getFoo(): \DateTime
    {
        return $this->foo;
    }
}
