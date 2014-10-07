<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the application class
 */
namespace RDev\Models\Applications;
use RDev\Models\HTTP;
use RDev\Models\Routing;
use RDev\Tests\Models\Applications\Mocks;

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
            ],
            "monolog" => [
                "handlers" => [
                    "main" => [
                        "type" => new Mocks\MonologHandler(),
                        "handler" => new Mocks\MonologHandler()
                    ]
                ]
            ]
        ];
        $this->application = new Mocks\Application($this->config);
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
            $this->application->getHTTPConnection()->getResponse()->getStatusCode()
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
            $this->application->getHTTPConnection()->getResponse()->getStatusCode()
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
            $this->application->getHTTPConnection()->getResponse()->getStatusCode()
        );
    }

    /**
     * Tests that the bindings are registered
     */
    public function testBindings()
    {
        $interfaceName = "RDev\\Tests\\Models\\IoC\\Mocks\\IFoo";
        $concreteClassName = "RDev\\Tests\\Models\\IoC\\Mocks\\Bar";
        $secondConcreteClassName = "RDev\\Tests\\Models\\IoC\\Mocks\\Blah";
        $constructorWithInterfaceName = "RDev\\Tests\\Models\\IoC\\Mocks\\ConstructorWithInterface";
        $configArray = [
            "bindings" => [
                "universal" => [
                    $interfaceName => $concreteClassName
                ],
                "targeted" => [
                    $constructorWithInterfaceName => [
                        $interfaceName => $secondConcreteClassName
                    ]
                ]
            ]
        ];
        $application = new Mocks\Application($configArray);
        $object1 = $application->getIoCContainer()->createNew($interfaceName);
        $object2 = $application->getIoCContainer()->createNew($constructorWithInterfaceName)->getFoo();
        $this->assertInstanceOf($concreteClassName, $object1);
        $this->assertInstanceOf($secondConcreteClassName, $object2);
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
        $this->assertEquals(new HTTP\Connection, $this->application->getHTTPConnection());
    }

    /**
     * Tests getting the dependency injection container
     */
    public function testGettingIoCContainer()
    {
        $this->assertInstanceOf("RDev\\Models\\IoC\\IContainer", $this->application->getIoCContainer());
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
        $this->assertInstanceOf("RDev\\Models\\Routing\\Router", $this->application->getRouter());
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
            $this->application->getHTTPConnection()->getResponse()->getStatusCode()
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