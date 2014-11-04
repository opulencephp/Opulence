<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the application class
 */
namespace RDev\Applications;
use Monolog;
use RDev\Applications\Configs;
use RDev\HTTP;
use RDev\IoC;
use RDev\Tests\Mocks as ModelMocks;
use RDev\Routing;
use RDev\Sessions;
use RDev\Tests\Applications\Mocks;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /** @var Mocks\Application The application to use in the tests */
    private $application = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $logger = new Monolog\Logger("application");
        $logger->pushHandler(new Mocks\MonologHandler());
        $container = new IoC\Container();
        $this->application = new Mocks\Application(
            $logger,
            "staging",
            new HTTP\Connection(),
            $container,
            new Routing\Router($container, new Routing\Dispatcher($container), new Routing\RouteCompiler()),
            new Sessions\Session()
        );
    }

    /**
     * Tests that our application only attempts to shutdown once when it's already shutdown
     */
    public function testApplicationIsNotShutdownTwice()
    {
        $shutdownIter = 0;
        $this->application->registerPreShutdownTask(function () use (&$shutdownIter)
        {
            $shutdownIter++;
        });
        $this->application->start();
        $this->application->shutdown();
        $this->assertEquals(1, $shutdownIter);
        $this->application->shutdown();
        $this->assertEquals(1, $shutdownIter);
    }

    /**
     * Tests that our application only attempts to start up once when it's already running
     */
    public function testApplicationIsNotStartedTwice()
    {
        $startIter = 0;
        $this->application->registerPreStartTask(function () use (&$startIter)
        {
            $startIter++;
        });
        $this->application->start();
        $this->assertEquals(1, $startIter);
        $this->application->start();
        $this->assertEquals(1, $startIter);
    }

    /**
     * Tests registering a bad post-shutdown task
     */
    public function testBadPostShutdownTask()
    {
        $this->application->registerPostShutdownTask(function ()
        {
            // Throw anything other than a runtime exception
            throw new \InvalidArgumentException("foobar");
        });
        $this->application->start();
        $this->application->shutdown();
        $this->assertEquals(
            HTTP\ResponseHeaders::HTTP_INTERNAL_SERVER_ERROR,
            $this->application->getConnection()->getResponse()->getStatusCode()
        );
    }

    /**
     * Tests registering a bad post-start task
     */
    public function testBadPostStartTask()
    {
        $this->application->registerPostStartTask(function ()
        {
            // Throw anything other than a runtime exception
            throw new \InvalidArgumentException("foobar");
        });
        $this->application->start();
        $this->application->shutdown();
        $this->assertEquals(
            HTTP\ResponseHeaders::HTTP_INTERNAL_SERVER_ERROR,
            $this->application->getConnection()->getResponse()->getStatusCode()
        );
    }

    /**
     * Tests registering a bad pre-shutdown task
     */
    public function testBadPreShutdownTask()
    {
        $this->application->registerPreShutdownTask(function ()
        {
            // Throw anything other than a runtime exception
            throw new \InvalidArgumentException("foobar");
        });
        $this->application->start();
        $this->application->shutdown();
        $this->assertEquals(
            HTTP\ResponseHeaders::HTTP_INTERNAL_SERVER_ERROR,
            $this->application->getConnection()->getResponse()->getStatusCode()
        );
    }

    /**
     * Tests checking if an application that wasn't ever started is running
     */
    public function testCheckingIfUnstartedApplicationIsRunning()
    {
        $this->assertFalse($this->application->isRunning());
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
        $this->assertEquals(new HTTP\Connection, $this->application->getConnection());
    }

    /**
     * Tests getting the dependency injection container
     */
    public function testGettingIoCContainer()
    {
        $this->assertInstanceOf("RDev\\IoC\\IContainer", $this->application->getIoCContainer());
    }

    /**
     * Tests getting the log
     */
    public function testGettingLog()
    {
        $this->assertInstanceOf("Monolog\\Logger", $this->application->getLogger());
    }

    /**
     * Tests getting the router
     */
    public function testGettingRouter()
    {
        $this->assertInstanceOf("RDev\\Routing\\Router", $this->application->getRouter());
    }

    /**
     * Tests getting the session
     */
    public function testGettingSession()
    {
        $this->assertEquals(new Sessions\Session, $this->application->getSession());
    }

    /**
     * Tests registering an invalid bootstrapper
     */
    public function testRegisteringInvalidBootstrapper()
    {
        $this->setExpectedException("\\RuntimeException");
        $this->application->registerBootstrappers([get_class($this)]);
    }

    /**
     * Tests registering post-shutdown tasks
     */
    public function testRegisteringPostShutdownTask()
    {
        $value = "";
        $this->application->registerPostShutdownTask(function () use (&$value)
        {
            $value = "foo";
        });
        $this->application->start();
        $this->application->shutdown();
        $this->assertEquals("foo", $value);
    }

    /**
     * Tests registering post-start tasks
     */
    public function testRegisteringPostStartTask()
    {
        $value = "";
        $this->application->registerPostStartTask(function () use (&$value)
        {
            $value = "foo";
        });
        $this->application->start();
        $this->assertEquals("foo", $value);
    }

    /**
     * Tests registering pre- and post-shutdown tasks
     */
    public function testRegisteringPreAndPostShutdownTasks()
    {
        $preShutdownValue = "";
        $postShutdownValue = "";
        $this->application->registerPreShutdownTask(function () use (&$preShutdownValue)
        {
            $preShutdownValue = "foo";
        });
        $this->application->registerPostShutdownTask(function () use (&$postShutdownValue)
        {
            $postShutdownValue = "bar";
        });
        $this->application->start();
        $this->application->shutdown();
        $this->assertEquals("foo", $preShutdownValue);
        $this->assertEquals("bar", $postShutdownValue);
    }

    /**
     * Tests registering pre- and post-start tasks
     */
    public function testRegisteringPreAndPostStartTasks()
    {
        $preStartValue = "";
        $postStartValue = "";
        $this->application->registerPreStartTask(function () use (&$preStartValue)
        {
            $preStartValue = "foo";
        });
        $this->application->registerPostStartTask(function () use (&$postStartValue)
        {
            $postStartValue = "bar";
        });
        $this->application->start();
        $this->assertEquals("foo", $preStartValue);
        $this->assertEquals("bar", $postStartValue);
    }

    /**
     * Tests registering pre-shutdown tasks
     */
    public function testRegisteringPreShutdownTask()
    {
        $value = "";
        $this->application->registerPreShutdownTask(function () use (&$value)
        {
            $value = "foo";
        });
        $this->application->start();
        $this->application->shutdown();
        $this->assertEquals("foo", $value);
    }

    /**
     * Tests registering pre-start tasks
     */
    public function testRegisteringPreStartTask()
    {
        $value = "";
        $this->application->registerPreStartTask(function () use (&$value)
        {
            $value = "foo";
        });
        $this->application->start();
        $this->assertEquals("foo", $value);
    }

    /**
     * Tests registering a bad pre-start task
     */
    public function testRuntimeExceptionIsThrownWithBadPreStartTask()
    {
        $this->application->registerPreStartTask(function ()
        {
            // Throw anything other than a runtime exception
            throw new \InvalidArgumentException("foobar");
        });
        $this->application->start();
        $this->application->shutdown();
        $this->assertEquals(
            HTTP\ResponseHeaders::HTTP_INTERNAL_SERVER_ERROR,
            $this->application->getConnection()->getResponse()->getStatusCode()
        );
    }

    /**
     * Tests checking if a shutdown application is no longer running
     */
    public function testsCheckingIfAShutdownApplicationIsNotRunning()
    {
        $this->application->start();
        $this->application->shutdown();
        $this->assertFalse($this->application->isRunning());
    }

    /**
     * Tests checking if a started application is running
     */
    public function testsCheckingIfAStartedApplicationIsRunning()
    {
        $this->application->start();
        $this->assertTrue($this->application->isRunning());
    }
} 