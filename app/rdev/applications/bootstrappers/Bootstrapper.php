<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the base bootstrapper
 */
namespace RDev\Applications\Bootstrappers;
use RDev\Applications;
use RDev\Applications\Environments;
use RDev\IoC;
use RDev\Sessions;

abstract class Bootstrapper
{
    /** @var Applications\Paths The paths to various directories used by RDev */
    protected $paths = null;
    /** @var Environments\Environment The current environment */
    protected $environment = null;
    /** @var Sessions\ISession The current session */
    protected $session = null;

    /**
     * @param Applications\Paths $paths The paths to various directories used by RDev
     * @param Environments\Environment $environment The current environment
     * @param Sessions\ISession $session The current session
     */
    public final function __construct(
        Applications\Paths $paths,
        Environments\Environment $environment,
        Sessions\ISession $session
    )
    {
        $this->paths = $paths;
        $this->environment = $environment;
        $this->session = $session;
    }

    /**
     * Attempts to call the "run" method on the bootstrapper
     *
     * @param string $name The name of the method to call
     * @param array $arguments The list of arguments to pass in
     * @throws \BadMethodCallException Thrown if a method other than "run" is called
     */
    public function __call($name, array $arguments)
    {
        if($name !== "run")
        {
            throw new \BadMethodCallException("Only Bootstrapper::run is supported");
        }

        // The user must have not specified a "run" function, so just return
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function registerBindings(IoC\IContainer $container)
    {
        // Let extending classes define this
    }

    /**
     * NOTE:  Because the following function accepts a variable number of parameters, we do not define it inside
     * this class.  However, bootstrappers MUST implement this method
     *
     * abstract public function run();
     */
}