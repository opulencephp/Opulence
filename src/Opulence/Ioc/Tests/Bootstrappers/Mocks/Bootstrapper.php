<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Ioc\Tests\Bootstrappers\Mocks;

use Opulence\Ioc\Bootstrappers\Bootstrapper as BaseBootstrapper;
use Opulence\Ioc\IContainer;

/**
 * Defines a mocked bootstrapper
 */
class Bootstrapper extends BaseBootstrapper
{
    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container): void
    {
        // Don't do anything
    }
}
