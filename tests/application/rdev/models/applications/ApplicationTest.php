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

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->application = new Application(new Configs\ApplicationConfig());
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