<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the base bootstrapper
 */
namespace RDev\Applications\Bootstrappers;
use RDev\IoC;

abstract class Bootstrapper implements IBootstrapper
{
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
     * the interface.  However, bootstrappers MUST implement this method
     *
     * abstract public function run();
     */
}