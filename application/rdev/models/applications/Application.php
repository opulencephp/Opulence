<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines an application
 */
namespace RDev\Models\Applications;
use Monolog;
use Monolog\Handler;
use RDev\Models\HTTP;
use RDev\Models\IoC;
use RDev\Models\IoC\Configs as IoCConfigs;
use RDev\Models\Routing;
use RDev\Models\Routing\Configs as RouterConfigs;
use RDev\Models\Sessions;

class Application
{
    /** The production environment */
    const ENV_PRODUCTION = "production";
    /** The staging environment */
    const ENV_STAGING = "staging";
    /** The testing environment */
    const ENV_TESTING = "testing";
    /** The development environment */
    const ENV_DEVELOPMENT = "development";

    /** @var string The environment the current server belongs to, eg "production" */
    private $environment = self::ENV_PRODUCTION;
    /** @var HTTP\Connection The HTTP connection */
    private $connection = null;
    /** @var Routing\Router The router for requests */
    private $router = null;
    /** @var IoC\IContainer The dependency injection container to use throughout the application */
    private $container = null;
    /** @var Monolog\Logger The logger used by this application */
    private $logger = null;
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
     * @param string $environment The current environment
     * @param HTTP\Connection $connection The current HTTP connection
     * @param IoC\IContainer $container The IoC container to use
     * @param Routing\Router $router The router to use
     * @param Sessions\ISession $session The current user's session
     */
    public function __construct(
        Monolog\Logger $logger,
        $environment,
        HTTP\Connection $connection,
        IoC\IContainer $container,
        Routing\Router $router,
        Sessions\ISession $session
    )
    {
        $this->logger = $logger;
        $this->environment = $environment;
        $this->connection = $connection;
        $this->container = $container;
        $this->router = $router;
        $this->session = $session;
    }

    /**
     * @return HTTP\Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return string
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
     * @return Routing\Router
     */
    public function getRouter()
    {
        return $this->router;
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
        $instantiatedBootstrappers = [];

        foreach($bootstrapperClasses as $bootstrapperClass)
        {
            $bootstrapper = $this->container->makeNew($bootstrapperClass);

            if(!$bootstrapper instanceof Bootstrappers\IBootstrapper)
            {
                throw new \RuntimeException("Bootstrapper does not implement IBootstrapper");
            }

            $bootstrapper->setApplication($this);
            $instantiatedBootstrappers[] = $bootstrapper;
        }

        $this->registerPreStartTask(function () use ($instantiatedBootstrappers)
        {
            foreach($instantiatedBootstrappers as $bootstrapper)
            {
                /** @var Bootstrappers\IBootstrapper $bootstrapper */
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
                $this->doShutdown();
                $this->isRunning = false;
                $this->doTasks($this->postShutdownTasks);
            }
            catch(\Exception $ex)
            {
                $this->connection->getResponse()->setStatusCode(HTTP\ResponseHeaders::HTTP_INTERNAL_SERVER_ERROR);
                $this->connection->getResponse()->send();
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
                $this->doStart();
                $this->isRunning = true;
                $this->doTasks($this->postStartTasks);
            }
            catch(\Exception $ex)
            {
                $this->logger->addError("Failed to start application: $ex");
                $this->connection->getResponse()->setStatusCode(HTTP\ResponseHeaders::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    /**
     * Actually performs the shutdown
     *
     * @throws \RuntimeException Thrown if there was an error shutting down the application
     */
    protected function doShutdown()
    {
        $this->connection->getResponse()->send();
    }

    /**
     * Actually performs the start
     *
     * @throws \RuntimeException Thrown if there was an error starting up the application
     */
    protected function doStart()
    {
        $response = $this->router->route($this->connection->getRequest());

        if($response instanceof HTTP\Response)
        {
            $this->connection->setResponse($response);
        }
        else
        {
            $this->connection->getResponse()->setContent($response);
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