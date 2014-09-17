<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines an application
 */
namespace RDev\Models\Applications;
use RDev\Models\Web;

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
    /** @var Web\HTTPConnection The HTTP connection */
    private $httpConnection = null;
    /** @var Web\Router The router for requests */
    private $router = null;

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

        $environmentFetcher = new EnvironmentFetcher();
        $this->environment = $environmentFetcher->getEnvironment($config["environment"]);
        $this->httpConnection = new Web\HTTPConnection();
        $this->router = new Web\Router();
    }

    /**
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @return Web\HTTPConnection
     */
    public function getHTTPConnection()
    {
        return $this->httpConnection;
    }

    /**
     * @return Web\Router
     */
    public function getRouter()
    {
        return $this->router;
    }
} 