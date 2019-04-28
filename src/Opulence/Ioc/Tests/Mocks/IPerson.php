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
 * Defines an interface to implement
 */
interface IPerson
{
    /**
     * Gets the last name of the person
     *
     * @return string The last name
     */
    public function getLastName(): string;
}
