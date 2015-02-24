<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the Composer bootstrapper
 */
namespace RDev\Framework\Bootstrappers\Console\Composer;
use RDev\Applications\Bootstrappers;
use RDev\Framework\Composer as ComposerWrapper;
use RDev\IoC;

class Composer extends Bootstrappers\Bootstrapper
{
    /**
     * {@inheritdoc}
     */
    public function registerBindings(IoC\IContainer $container)
    {
        $composer = ComposerWrapper\Composer::createFromRawConfig($this->paths);
        $executable = new ComposerWrapper\Executable($this->paths);
        $container->bind("RDev\\Framework\\Composer\\Composer", $composer);
        $container->bind("RDev\\Framework\\Composer\\Executable", $executable);
    }
}