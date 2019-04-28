<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Ioc\Tests\Bootstrappers\Inspection\Caching\Mocks;

use Opulence\Ioc\IContainer;
use Opulence\Ioc\Tests\Bootstrappers\Mocks\Bootstrapper;

/**
 * Mocks a bootstrapper for use in testing
 */
final class MockBootstrapper extends Bootstrapper
{
    public function registerBindings(IContainer $container): void
    {
        // Don't do anything
    }
}
