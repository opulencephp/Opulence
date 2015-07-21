<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the Composer bootstrapper
 */
namespace Opulence\Framework\Bootstrappers\Console\Composer;
use Opulence\Applications\Bootstrappers\Bootstrapper;
use Opulence\Applications\Bootstrappers\ILazyBootstrapper;
use Opulence\Framework\Composer\Composer as ComposerWrapper;
use Opulence\Framework\Composer\Executable;
use Opulence\IoC\IContainer;

class Composer extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @inheritdoc
     */
    public function getBindings()
    {
        return [ComposerWrapper::class, Executable::class];
    }

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        $composer = ComposerWrapper::createFromRawConfig($this->paths);
        $executable = new Executable($this->paths);
        $container->bind(ComposerWrapper::class, $composer);
        $container->bind(Executable::class, $executable);
    }
}