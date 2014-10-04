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
use RDev\Models\Routing;

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
    private $httpConnection = null;
    /** @var Routing\Router The router for requests */
    private $router = null;
    /** @var IoC\IContainer The dependency injection container to use throughout the application */
    private $iocContainer = null;
    /** @var Monolog\Logger The logger used by this application */
    private $logger = null;
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
     * @param Configs\ApplicationConfig|array $config The configuration to use to setup the application
     *      The following keys are optional:
     *          "environment" => see environment config for details on structure
     * @see Environment::getEnvironment()
     */
    public function __construct($config)
    {
        if(is_array($config))
        {
            $config = new Configs\ApplicationConfig($config);
        }

        $this->setLog($config["monolog"]["handlers"]);
        $this->iocContainer = $config["bindings"]["container"];
        $this->registerBindings($config["bindings"]);
        $environmentFetcher = new EnvironmentFetcher();
        $this->environment = $environmentFetcher->getEnvironment($config["environment"]);
        $this->httpConnection = new HTTP\Connection();
        $this->router = new Routing\Router($this->iocContainer, $this->httpConnection, $config["router"]);
    }

    /**
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @return HTTP\Connection
     */
    public function getHTTPConnection()
    {
        return $this->httpConnection;
    }

    /**
     * @return IoC\IContainer
     */
    public function getIoCContainer()
    {
        return $this->iocContainer;
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
     * @return boolean
     */
    public function isRunning()
    {
        return $this->isRunning;
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
                $this->httpConnection->getResponse()->setStatusCode(HTTP\Response::HTTP_INTERNAL_SERVER_ERROR);
                $this->httpConnection->getResponse()->send();
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
                $this->httpConnection->getResponse()->setStatusCode(HTTP\Response::HTTP_INTERNAL_SERVER_ERROR);
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
        $this->httpConnection->getResponse()->send();
    }

    /**
     * Actually performs the start
     *
     * @throws \RuntimeException Thrown if there was an error starting up the application
     */
    protected function doStart()
    {
        $response = $this->router->route($this->httpConnection->getRequest()->getServer()->get("REQUEST_URI"));

        if($response instanceof HTTP\Response)
        {
            $this->httpConnection->setResponse($response);
        }
        else
        {
            $this->httpConnection->getResponse()->setContent($response);
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

    /**
     * Registers the bindings from the config
     *
     * @param array $bindings The list of bindings from the config
     */
    private function registerBindings(array $bindings)
    {
        foreach($bindings["universal"] as $component => $concreteClassName)
        {
            $this->iocContainer->bind($component, $concreteClassName);
        }

        foreach($bindings["targeted"] as $targetClassName => $targetedBindings)
        {
            foreach($targetedBindings as $component => $concreteClassName)
            {
                $this->iocContainer->bind($component, $concreteClassName, $targetClassName);
            }
        }
    }

    /**
     * Sets the application log
     *
     * @param array $config The array of handlers
     */
    private function setLog(array $config)
    {
        $this->logger = new Monolog\Logger("application");

        foreach($config as $name => $handler)
        {
            $this->logger->pushHandler($handler);
        }
    }
} 