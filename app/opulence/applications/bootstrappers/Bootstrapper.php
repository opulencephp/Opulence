<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the base bootstrapper
 * Note:  This class also accepts a run() method with a variable number of parameters
 */
namespace Opulence\Applications\Bootstrappers;
use BadMethodCallException;
use Opulence\Applications\Environments\Environment;
use Opulence\Applications\Paths;
use Opulence\IoC\IContainer;

abstract class Bootstrapper
{
    /** @var Paths The paths to various directories used by Opulence */
    protected $paths = null;
    /** @var Environment The current environment */
    protected $environment = null;

    /**
     * @param Paths $paths The paths to various directories used by Opulence
     * @param Environment $environment The current environment
     */
    public final function __construct(Paths $paths, Environment $environment)
    {
        $this->paths = $paths;
        $this->environment = $environment;
    }

    /**
     * Handles the case that the bootstrapper did not implement the run() or shutdown() methods
     *
     * @param string $name The name of the method to call
     * @param array $arguments The list of arguments to pass in
     * @throws BadMethodCallException Thrown if a method other than "run" is called
     */
    public function __call($name, array $arguments)
    {
        if($name !== "run" && $name !== "shutdown")
        {
            throw new BadMethodCallException("Only Bootstrapper::run() and Bootstrapper::shutdown() are supported");
        }

        // The user must have not specified a "run" or "shutdown" function, so just return
        return;
    }

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        // Let extending classes define this
    }
}