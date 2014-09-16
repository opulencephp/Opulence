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
    /** @var Web\HTTPConnection The HTTP connection */
    private $httpConnection = null;
    /** @var Web\Router The router for requests */
    private $router = null;

    /**
     * @param Configs\ApplicationConfig|array $config The configuration to use to setup the application
     */
    public function __construct($config)
    {
        if(is_array($config))
        {
            $config = new Configs\ApplicationConfig($config);
        }

        $this->httpConnection = new Web\HTTPConnection();
        $this->router = new Web\Router();
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