<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the application class
 */
namespace RDev\Applications;
use Monolog;
use RDev\IoC;
use RDev\Tests\Mocks as ModelMocks;
use RDev\Sessions;
use RDev\Tests\Applications\Mocks;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /** @var Application The application to use in the tests */
    private $application = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $logger = new Monolog\Logger("application");
        $logger->pushHandler(new Mocks\MonologHandler());
        $container = new IoC\Container();
        $this->application = new Application(
            $logger,
            new Environments\Environment("staging"),
            $container,
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
        $this->assertFalse($this->application->isRunning());
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
        $this->assertFalse($this->application->isRunning());
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
        $this->assertFalse($this->application->isRunning());
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
        $expectedEnvironment = new Environments\Environment("staging");
        $this->assertEquals($expectedEnvironment, $this->application->getEnvironment());
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
        $this->application->registerBootstrappers([get_class($this)]);
        $this->application->start();
        $this->assertFalse($this->application->isRunning());
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
     * Tests setting the container
     */
    public function testSettingContainer()
    {
        $container = new IoC\Container();
        $this->application->setIoCContainer($container);
        $this->assertSame($container, $this->application->getIoCContainer());
    }

    /**
     * Tests setting the environment
     */
    public function testSettingEnvironment()
    {
        $environment = new Environments\Environment("foo");
        $this->application->setEnvironment($environment);
        $this->assertEquals($environment, $this->application->getEnvironment());
    }

    /**
     * Tests setting the logger
     */
    public function testSettingLogger()
    {
        $logger = new Monolog\Logger("test");
        $this->application->setLogger($logger);
        $this->assertSame($logger, $this->application->getLogger());
    }

    /**
     * Tests setting the session
     */
    public function testSettingSession()
    {
        $session = new Sessions\Session();
        $this->application->setSession($session);
        $this->assertSame($session, $this->application->getSession());
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