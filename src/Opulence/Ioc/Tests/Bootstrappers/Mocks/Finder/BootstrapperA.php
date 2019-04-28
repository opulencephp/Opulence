<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Ioc\Tests\Bootstrappers\Mocks\Finder;

use Opulence\Ioc\IContainer;
use Opulence\Ioc\Bootstrappers\Bootstrapper;

/**
 * Defines a mock bootstrapper
 */
class BootstrapperA extends Bootstrapper
{
    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container): void
    {
        // Don't do anything
    }
}
