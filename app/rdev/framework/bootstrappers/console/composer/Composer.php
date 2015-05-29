<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the Composer bootstrapper
 */
namespace RDev\Framework\Bootstrappers\Console\Composer;
use RDev\Applications\Bootstrappers\Bootstrapper;
use RDev\Applications\Bootstrappers\ILazyBootstrapper;
use RDev\Framework\Composer\Composer as ComposerWrapper;
use RDev\Framework\Composer\Executable;
use RDev\IoC\IContainer;

class Composer extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * {@inheritdoc}
     */
    public function getBoundClasses()
    {
        return [ComposerWrapper::class, Executable::class];
    }

    /**
     * {@inheritdoc}
     */
    public function registerBindings(IContainer $container)
    {
        $composer = ComposerWrapper::createFromRawConfig($this->paths);
        $executable = new Executable($this->paths);
        $container->bind(ComposerWrapper::class, $composer);
        $container->bind(Executable::class, $executable);
    }
}