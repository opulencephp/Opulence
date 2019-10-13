<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Composer\Bootstrappers;

use Aphiria\DependencyInjection\Bootstrappers\Bootstrapper;
use Aphiria\DependencyInjection\IContainer;
use Opulence\Framework\Composer\Composer;
use Opulence\Framework\Composer\Executable;
use Opulence\Framework\Configuration\Config;
use RuntimeException;

/**
 * Defines the Composer bootstrapper
 */
final class ComposerBootstrapper extends Bootstrapper
{
    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container): void
    {
        $rootPath = Config::get('paths', 'root');
        $psr4RootPath = Config::get('paths', 'src');

        if (!is_string($rootPath) || !is_string($psr4RootPath)) {
            throw new RuntimeException('Paths not configured');
        }

        $composer = Composer::createFromRawConfig($rootPath, $psr4RootPath);
        $executable = new Executable($rootPath);
        $container->bindInstance(Composer::class, $composer);
        $container->bindInstance(Executable::class, $executable);
    }
}
