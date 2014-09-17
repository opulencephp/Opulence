<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the application class
 */
namespace RDev\Models\Applications;
use RDev\Models\Web;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /** @var Application The application to use in the tests */
    private $application = null;
    /** @var array The config array the application uses */
    private $config = [];

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->config = [
            "environment" => [
                "staging" => gethostname()
            ]
        ];
        $this->application = new Application($this->config);
    }

    /**
     * Tests getting the environment
     */
    public function testGettingEnvironment()
    {
        $this->assertEquals("staging", $this->application->getEnvironment());
    }

    /**
     * Tests getting the HTTP connection
     */
    public function testGettingHTTPConnection()
    {
        $this->assertEquals(new Web\HTTPConnection, $this->application->getHTTPConnection());
    }

    /**
     * Tests getting the router
     */
    public function testGettingRouter()
    {
        $this->assertEquals(new Web\Router(), $this->application->getRouter());
    }
} 