<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Routing\Tests\Mocks;

/**
 * Mocks a controller that doesn't extend the base controller
 */
class InvalidController
{
    /**
     * A dummy method that does nothing
     *
     * @return string A dummy string
     */
    public function foo(): string
    {
        return 'fooWasCalled';
    }
}
