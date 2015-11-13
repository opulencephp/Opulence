<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Bootstrappers\Console\Composer;

use Opulence\Bootstrappers\Bootstrapper;
use Opulence\Bootstrappers\ILazyBootstrapper;
use Opulence\Framework\Composer\Composer;
use Opulence\Framework\Composer\Executable;
use Opulence\Ioc\IContainer;

/**
 * Defines the Composer bootstrapper
 */
class ComposerBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @inheritdoc
     */
    public function getBindings()
    {
        return [Composer::class, Executable::class];
    }

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        $composer = Composer::createFromRawConfig($this->paths);
        $executable = new Executable($this->paths);
        $container->bind(Composer::class, $composer);
        $container->bind(Executable::class, $executable);
    }
}