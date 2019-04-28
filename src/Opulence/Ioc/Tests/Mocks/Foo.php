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
 * Defines a class that implements an interface for use in IoC tests
 */
class Foo implements IFoo
{
    /** @var IPerson A dependency */
    private $person;

    public function __construct(IPerson $person)
    {
        $this->person = $person;
    }

    /**
     * @inheritdoc
     */
    public function getClassName(): string
    {
        return __CLASS__;
    }
}
