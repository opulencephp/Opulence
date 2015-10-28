<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines an application
 */
namespace Opulence\Applications;

use Closure;
use Exception;
use Opulence\Applications\Environments\Environment;
use Opulence\Applications\Tasks\Dispatchers\IDispatcher;
use Opulence\Applications\Tasks\TaskTypes;
use Opulence\IoC\IContainer;
use RuntimeException;

class Application
{
    /** @var string The current Opulence version */
    private static $version = "1.0.0-alpha6";
    /** @var Paths The paths to various directories used by Opulence */
    private $paths = null;
    /** @var IDispatcher The task dispatcher */
    private $taskDispatcher = null;
    /** @var Environment The environment the application is running on */
    private $environment = null;
    /** @var IContainer The IoC container */
    private $container = null;
    /** @var bool Whether or not the application is currently running */
    private $isRunning = false;

    /**
     * @param Paths $paths The paths to various directories used by Opulence
     * @param IDispatcher $taskDispatcher The task dispatcher
     * @param Environment $environment The current environment
     * @param IContainer $container The IoC container
     */
    public function __construct(
        Paths $paths,
        IDispatcher $taskDispatcher,
        Environment $environment,
        IContainer $container
    ) {
        // Order here is important
        $this->setPaths($paths);
        $this->taskDispatcher = $taskDispatcher;
        $this->setEnvironment($environment);
        $this->container = $container;
    }

    /**
     * @return string
     */
    public static function getVersion()
    {
        return self::$version;
    }

    /**
     * @return Environment
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @return IContainer
     */
    public function getIoCContainer()
    {
        return $this->container;
    }

    /**
     * @return Paths
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * @return bool
     */
    public function isRunning()
    {
        return $this->isRunning;
    }

    /**
     * @param Environment $environment
     */
    public function setEnvironment(Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @param Paths $paths
     */
    public function setPaths($paths)
    {
        $this->paths = $paths;
    }

    /**
     * Shuts down this application
     *
     * @param Closure $shutdownTask The task to perform on shutdown
     * @return mixed|null The return value of the task if there was one, otherwise null
     * @throws RuntimeException Thrown if there was an error shutting down the application
     */
    public function shutdown(Closure $shutdownTask = null)
    {
        $taskReturnValue = null;

        // Don't shutdown a shutdown application
        if ($this->isRunning) {
            try {
                $this->taskDispatcher->dispatch(TaskTypes::PRE_SHUTDOWN);
                $this->isRunning = false;

                if ($shutdownTask !== null) {
                    $taskReturnValue = call_user_func($shutdownTask);
                }

                $this->taskDispatcher->dispatch(TaskTypes::POST_SHUTDOWN);
            } catch (Exception $ex) {
                $this->isRunning = false;
            }
        }

        return $taskReturnValue;
    }

    /**
     * Starts this application
     *
     * @param Closure $startTask The task to perform on startup
     * @return mixed|null The return value of the task if there was one, otherwise null
     */
    public function start(Closure $startTask = null)
    {
        $taskReturnValue = null;

        // Don't start a running application
        if (!$this->isRunning) {
            try {
                $this->taskDispatcher->dispatch(TaskTypes::PRE_START);
                $this->isRunning = true;

                if ($startTask !== null) {
                    $taskReturnValue = call_user_func($startTask);
                }

                $this->taskDispatcher->dispatch(TaskTypes::POST_START);
            } catch (Exception $ex) {
                $this->shutdown();
            }
        }

        return $taskReturnValue;
    }
} 