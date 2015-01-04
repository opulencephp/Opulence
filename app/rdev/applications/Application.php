<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines an application
 */
namespace RDev\Applications;
use Monolog;
use RDev\IoC;
use RDev\Sessions;

class Application
{
    /** @var Monolog\Logger The logger used by this application */
    private $logger = null;
    /** @var Environments\Environment The environment the application is running on */
    private $environment = null;
    /** @var IoC\IContainer The dependency injection container to use throughout the application */
    private $container = null;
    /** @var Sessions\ISession The current user's session */
    private $session = null;
    /** @var bool Whether or not the application is currently running */
    private $isRunning = false;
    /** @var callable[] The list of functions to execute before startup */
    private $preStartTasks = [];
    /** @var callable[] The list of functions to execute after startup */
    private $postStartTasks = [];
    /** @var callable[] The list of functions to execute before shutdown */
    private $preShutdownTasks = [];
    /** @var callable[] The list of functions to execute after shutdown */
    private $postShutdownTasks = [];

    /**
     * @param Monolog\Logger $logger The logger to use throughout the application
     * @param Environments\Environment $environment The current environment
     * @param IoC\IContainer $container The IoC container to use
     * @param Sessions\ISession $session The current user's session
     */
    public function __construct(
        Monolog\Logger $logger,
        Environments\Environment $environment,
        IoC\IContainer $container,
        Sessions\ISession $session
    )
    {
        // Order here is important
        $this->setLogger($logger);
        $this->setEnvironment($environment);
        $this->setIoCContainer($container);
        $this->setSession($session);
    }

    /**
     * @return Environments\Environment
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @return IoC\IContainer
     */
    public function getIoCContainer()
    {
        return $this->container;
    }

    /**
     * @return Monolog\Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @return Sessions\ISession
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
     * @throws \RuntimeException Thrown if the bootstrapper is of the incorrect class
     */
    public function registerBootstrappers(array $bootstrapperClasses)
    {
        $this->registerPreStartTask(function () use ($bootstrapperClasses)
        {
            foreach($bootstrapperClasses as $bootstrapperClass)
            {
                $bootstrapper = $this->container->makeNew($bootstrapperClass);

                if(!$bootstrapper instanceof Bootstrappers\IBootstrapper)
                {
                    throw new \RuntimeException("Bootstrapper does not implement IBootstrapper");
                }

                $bootstrapper->run();
            }
        });
    }

    /**
     * Registers a task to be run after the application shuts down
     *
     * @param callable $task The task to register
     */
    public function registerPostShutdownTask(callable $task)
    {
        $this->postShutdownTasks[] = $task;
    }

    /**
     * Registers a task to be run after the application starts
     *
     * @param callable $task The task to register
     */
    public function registerPostStartTask(callable $task)
    {
        $this->postStartTasks[] = $task;
    }

    /**
     * Registers a task to be run before the application shuts down
     *
     * @param callable $task The task to register
     */
    public function registerPreShutdownTask(callable $task)
    {
        $this->preShutdownTasks[] = $task;
    }

    /**
     * Registers a task to be run before the application starts
     *
     * @param callable $task The task to register
     */
    public function registerPreStartTask(callable $task)
    {
        $this->preStartTasks[] = $task;
    }

    /**
     * @param Environments\Environment $environment
     */
    public function setEnvironment(Environments\Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @param IoC\IContainer $container
     */
    public function setIoCContainer(IoC\IContainer $container)
    {
        $this->container = $container;
    }

    /**
     * @param Monolog\Logger $logger
     */
    public function setLogger(Monolog\Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param Sessions\ISession $session
     */
    public function setSession(Sessions\ISession $session)
    {
        $this->session = $session;
    }

    /**
     * Shuts down this application
     *
     * @throws \RuntimeException Thrown if there was an error shutting down the application
     */
    public function shutdown()
    {
        // Don't shutdown a shutdown application
        if($this->isRunning)
        {
            try
            {
                $this->doTasks($this->preShutdownTasks);
                $this->isRunning = false;
                $this->doTasks($this->postShutdownTasks);
            }
            catch(\Exception $ex)
            {
                $this->logger->addError("Failed to shut down properly: $ex");
                $this->isRunning = false;
            }
        }
    }

    /**
     * Starts this application
     */
    public function start()
    {
        // Don't start a running application
        if(!$this->isRunning)
        {
            try
            {
                $this->doTasks($this->preStartTasks);
                $this->isRunning = true;
                $this->doTasks($this->postStartTasks);
            }
            catch(\Exception $ex)
            {
                $this->logger->addError("Failed to start application: $ex");
                $this->shutdown();
            }
        }
    }

    /**
     * Runs a list of tasks
     *
     * @param callable[] $taskList The list of tasks to run
     * @throws \RuntimeException Thrown if any of the tasks error out
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
        catch(\Exception $ex)
        {
            throw new \RuntimeException("Failed to run tasks: " . $ex->getMessage());
        }
    }
} 