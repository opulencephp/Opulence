<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines an application
 */
namespace RDev\Applications;
use Closure;
use Exception;
use Monolog\Logger;
use RuntimeException;
use RDev\Applications\Bootstrappers\Bootstrapper;
use RDev\Applications\Environments\Environment;
use RDev\IoC\IContainer;
use RDev\Sessions\ISession;

class Application
{
    /** The current RDev version */
    private static $version = "0.5.0";
    /** @var Paths The paths to various directories used by RDev */
    private $paths = null;
    /** @var Logger The logger used by this application */
    private $logger = null;
    /** @var Environment The environment the application is running on */
    private $environment = null;
    /** @var IContainer The dependency injection container to use throughout the application */
    private $container = null;
    /** @var ISession The current user's session */
    private $session = null;
    /** @var bool Whether or not the application is currently running */
    private $isRunning = false;
    /** @var array The list of task callbacks */
    private $tasks = [
        "preStart" => [],
        "postStart" => [],
        "preShutdown" => [],
        "postShutdown" => []
    ];
    /** @var array The list of bootstrapper classes registered to the application */
    private $bootstrapperClasses = [];

    /**
     * @param Paths $paths The paths to various directories used by RDev
     * @param Logger $logger The logger to use throughout the application
     * @param Environment $environment The current environment
     * @param IContainer $container The IoC container to use
     * @param ISession $session The current user's session
     */
    public function __construct(
        Paths $paths,
        Logger $logger,
        Environment $environment,
        IContainer $container,
        ISession $session
    )
    {
        // Order here is important
        $this->setPaths($paths);
        $this->setLogger($logger);
        $this->setEnvironment($environment);
        $this->setIoCContainer($container);
        $this->setSession($session);
        $this->registerBootstrappersTask();
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
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @return Paths
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * @return ISession
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @return boolean
     */
    public function isRunning()
    {
        return $this->isRunning;
    }

    /**
     * Registers bootstrapper classes to run
     * This should be called before the application is started
     *
     * @param array $bootstrapperClasses The list of class names of bootstrappers
     * @throws RuntimeException Thrown if the bootstrapper is of the incorrect class
     */
    public function registerBootstrappers(array $bootstrapperClasses)
    {
        $this->bootstrapperClasses = array_merge($this->bootstrapperClasses, $bootstrapperClasses);
    }

    /**
     * Registers a task to be run after the application shuts down
     *
     * @param callable $task The task to register
     */
    public function registerPostShutdownTask(callable $task)
    {
        $this->tasks["postShutdown"][] = $task;
    }

    /**
     * Registers a task to be run after the application starts
     *
     * @param callable $task The task to register
     */
    public function registerPostStartTask(callable $task)
    {
        $this->tasks["postStart"][] = $task;
    }

    /**
     * Registers a task to be run before the application shuts down
     *
     * @param callable $task The task to register
     */
    public function registerPreShutdownTask(callable $task)
    {
        $this->tasks["preShutdown"][] = $task;
    }

    /**
     * Registers a task to be run before the application starts
     *
     * @param callable $task The task to register
     */
    public function registerPreStartTask(callable $task)
    {
        $this->tasks["preStart"][] = $task;
    }

    /**
     * @param Environment $environment
     */
    public function setEnvironment(Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @param IContainer $container
     */
    public function setIoCContainer(IContainer $container)
    {
        $this->container = $container;
    }

    /**
     * @param Logger $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param Paths $paths
     */
    public function setPaths($paths)
    {
        $this->paths = $paths;
    }

    /**
     * @param ISession $session
     */
    public function setSession(ISession $session)
    {
        $this->session = $session;
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
        if($this->isRunning)
        {
            try
            {
                $this->doTasks($this->tasks["preShutdown"]);
                $this->isRunning = false;

                if($shutdownTask !== null)
                {
                    $taskReturnValue = call_user_func($shutdownTask);
                }

                $this->doTasks($this->tasks["postShutdown"]);
            }
            catch(\Exception $ex)
            {
                $this->logger->addError("Failed to shut down properly: $ex");
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
        if(!$this->isRunning)
        {
            try
            {
                $this->doTasks($this->tasks["preStart"]);
                $this->isRunning = true;

                if($startTask !== null)
                {
                    $taskReturnValue = call_user_func($startTask);
                }

                $this->doTasks($this->tasks["postStart"]);
            }
            catch(\Exception $ex)
            {
                $this->logger->addError("Failed to start application: $ex");
                $this->shutdown();
            }
        }

        return $taskReturnValue;
    }

    /**
     * Runs a list of tasks
     *
     * @param callable[] $taskList The list of tasks to run
     * @throws RuntimeException Thrown if any of the tasks error out
     */
    protected function doTasks(array $taskList)
    {
        try
        {
            foreach($taskList as $task)
            {
                call_user_func($task);
            }
        }
        catch(Exception $ex)
        {
            throw new RuntimeException("Failed to run tasks: " . $ex->getMessage());
        }
    }

    /**
     * Registers the task that will run the bootstrappers
     */
    private function registerBootstrappersTask()
    {
        $this->registerPreStartTask(function ()
        {
            $bootstrapperObjects = [];

            foreach($this->bootstrapperClasses as $bootstrapperClass)
            {
                $bootstrapper = new $bootstrapperClass($this->paths, $this->environment, $this->session);

                if(!$bootstrapper instanceof Bootstrapper)
                {
                    throw new RuntimeException("\"$bootstrapperClass\" does not extend Bootstrapper");
                }

                $bootstrapper->registerBindings($this->container);
                $bootstrapperObjects[] = $bootstrapper;
            }

            /** @var Bootstrapper $bootstrapper */
            foreach($bootstrapperObjects as $bootstrapper)
            {
                $this->container->call($bootstrapper, "run", [], true);
            }
        });
    }
} 