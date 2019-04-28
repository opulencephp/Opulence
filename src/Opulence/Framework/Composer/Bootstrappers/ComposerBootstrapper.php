<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Framework\Composer\Bootstrappers;

use Opulence\Framework\Composer\Composer;
use Opulence\Framework\Composer\Executable;
use Opulence\Framework\Configuration\Config;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\IContainer;

/**
 * Defines the Composer bootstrapper
 */
class ComposerBootstrapper extends Bootstrapper
{
    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container) : void
    {
        $rootPath = Config::get('paths', 'root');
        $psr4RootPath = Config::get('paths', 'src');
        $composer = Composer::createFromRawConfig($rootPath, $psr4RootPath);
        $executable = new Executable($rootPath);
        $container->bindInstance(Composer::class, $composer);
        $container->bindInstance(Executable::class, $executable);
    }
}
