<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Bootstrappers;

use BadMethodCallException;
use Opulence\Applications\Environments\Environment;
use Opulence\Applications\Paths;
use Opulence\Ioc\IContainer;

/**
 * Defines the base bootstrapper
 * Note:  This class also accepts a run() method with a variable number of parameters
 */
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
     * Handles the case that the bootstrapper did not implement the initialize(), run(), or shutdown() methods
     *
     * @param string $name The name of the method to call
     * @param array $arguments The list of arguments to pass in
     * @throws BadMethodCallException Thrown if a method other than "run" is called
     */
    public function __call($name, array $arguments)
    {
        if ($name !== "initialize" && $name !== "run" && $name !== "shutdown") {
            throw new BadMethodCallException(
                sprintf(
                    "Only %s, %s, and %s are supported",
                    "Bootstrapper::initialize()",
                    "Bootstrapper::run()",
                    "Bootstrapper::shutdown()"
                )
            );
        }

        // The user must have not specified a "run" or "shutdown" function, so just return
        return;
    }

    /**
     * Registers any bindings to the IoC container
     *
     * @param IContainer $container The IoC container to bind to
     */
    public function registerBindings(IContainer $container)
    {
        // Let extending classes define this
    }
}