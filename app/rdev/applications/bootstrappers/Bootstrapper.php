<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the base bootstrapper
 * Note:  This class also accepts a run() method with a variable number of parameters
 */
namespace RDev\Applications\Bootstrappers;
use BadMethodCallException;
use RDev\Applications\Environments\Environment;
use RDev\Applications\Paths;
use RDev\IoC\IContainer;

abstract class Bootstrapper
{
    /** @var Paths The paths to various directories used by RDev */
    protected $paths = null;
    /** @var Environment The current environment */
    protected $environment = null;

    /**
     * @param Paths $paths The paths to various directories used by RDev
     * @param Environment $environment The current environment
     */
    public final function __construct(Paths $paths, Environment $environment)
    {
        $this->paths = $paths;
        $this->environment = $environment;
    }

    /**
     * Attempts to call the "run" method on the bootstrapper
     *
     * @param string $name The name of the method to call
     * @param array $arguments The list of arguments to pass in
     * @throws BadMethodCallException Thrown if a method other than "run" is called
     */
    public function __call($name, array $arguments)
    {
        if($name !== "run")
        {
            throw new BadMethodCallException("Only Bootstrapper::run is supported");
        }

        // The user must have not specified a "run" function, so just return
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function registerBindings(IContainer $container)
    {
        // Let extending classes define this
    }
}