<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the application class
 */
namespace RDev\Applications;
use InvalidArgumentException;
use Monolog\Logger;
use RDev\Applications\Environments\Environment;
use RDev\IoC\Container;
use RDev\Tests\Applications\Bootstrappers\Mocks\EnvironmentBootstrapper;
use RDev\Tests\Applications\Mocks\MonologHandler;
use ReflectionClass;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /** @var Application The application to use in the tests */
    private $application = null;
    /** @var Environment The environment used by the application */
    private $environment = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $logger = new Logger("application");
        $logger->pushHandler(new MonologHandler());
        $this->environment = new Environment("testing");
        $this->application = new Application(
            new Paths(["foo" => "bar"]),
            $logger,
            $this->environment,
            new Container()
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
            throw new InvalidArgumentException("foobar");
        });
        $this->assertNull($this->application->start());
        $this->assertNull($this->application->shutdown());
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
            throw new InvalidArgumentException("foobar");
        });
        $this->assertNull($this->application->start());
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
            throw new InvalidArgumentException("foobar");
        });
        $this->assertNull($this->application->start());
        $this->assertNull($this->application->shutdown());
        $this->assertFalse($this->application->isRunning());
    }

    /**
     * Tests registering a bad shutdown task
     */
    public function testBadShutdownTask()
    {
        $this->assertNull($this->application->start());
        $this->assertNull($this->application->shutdown(function ()
        {
            // Throw anything other than a runtime exception
            throw new InvalidArgumentException("foobar");
        }));
        $this->assertFalse($this->application->isRunning());
    }

    /**
     * Tests registering a bad start task
     */
    public function testBadStartTask()
    {
        $this->assertNull($this->application->start(function ()
        {
            // Throw anything other than a runtime exception
            throw new InvalidArgumentException("foobar");
        }));
        $this->assertFalse($this->application->isRunning());
    }

    /**
     * Tests checking if a shutdown application is no longer running
     */
    public function testCheckingIfAShutdownApplicationIsNotRunning()
    {
        $this->application->start();
        $this->application->shutdown();
        $this->assertFalse($this->application->isRunning());
    }

    /**
     * Tests checking if a started application is running
     */
    public function testCheckingIfAStartedApplicationIsRunning()
    {
        $this->application->start();
        $this->assertTrue($this->application->isRunning());
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
        $expectedEnvironment = new Environment("testing");
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
     * Tests getting paths
     */
    public function testGettingPaths()
    {
        $paths = new Paths(["foo" => "bar"]);
        $this->assertEquals($paths, $this->application->getPaths());
    }

    /**
     * Tests getting the application version
     */
    public function testGettingVersion()
    {
        $reflectionClass = new ReflectionClass($this->application);
        $property = $reflectionClass->getProperty("version");
        $property->setAccessible(true);
        $this->assertEquals($property->getValue(), Application::getVersion());
    }

    /**
     * Tests registering an invalid bootstrapper
     */
    public function testRegisteringInvalidBootstrapper()
    {
        $bootstrapperName = "RDev\\Tests\\Applications\\Bootstrappers\\Mocks\\InvalidBootstrapper";
        $this->application->registerBootstrappers([$bootstrapperName]);
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
     * Tests registering a shutdown task
     */
    public function testRegisteringShutdownTask()
    {
        $preShutdownValue = "";
        $shutdownValue = "";
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
        $this->assertNull($this->application->shutdown(function () use (&$shutdownValue)
        {
            $shutdownValue = "baz";
        }));
        $this->assertEquals("foo", $preShutdownValue);
        $this->assertEquals("baz", $shutdownValue);
        $this->assertEquals("bar", $postShutdownValue);
    }

    /**
     * Tests registering a start task
     */
    public function testRegisteringStartTask()
    {
        $preStartValue = "";
        $startValue = "";
        $postStartValue = "";
        $this->application->registerPreStartTask(function () use (&$preStartValue)
        {
            $preStartValue = "foo";
        });
        $this->application->registerPostStartTask(function () use (&$postStartValue)
        {
            $postStartValue = "bar";
        });
        $this->assertNull($this->application->start(function () use (&$startValue)
        {
            $startValue = "baz";
        }));
        $this->assertEquals("foo", $preStartValue);
        $this->assertEquals("baz", $startValue);
        $this->assertEquals("bar", $postStartValue);
    }

    /**
     * Tests that run and shutdown are called on a registered bootstrapper
     */
    public function testRunAndShutdownAreCalledOnBootstrapper()
    {
        $this->application->registerBootstrappers([EnvironmentBootstrapper::class]);
        $this->application->start();
        $this->assertEquals("running", $this->environment->getName());
        $this->application->shutdown();
        $this->assertEquals("shutting down", $this->environment->getName());
    }

    /**
     * Tests setting the container
     */
    public function testSettingContainer()
    {
        $container = new Container();
        $this->application->setIoCContainer($container);
        $this->assertSame($container, $this->application->getIoCContainer());
    }

    /**
     * Tests setting the environment
     */
    public function testSettingEnvironment()
    {
        $environment = new Environment("foo");
        $this->application->setEnvironment($environment);
        $this->assertEquals($environment, $this->application->getEnvironment());
    }

    /**
     * Tests setting the logger
     */
    public function testSettingLogger()
    {
        $logger = new Logger("test");
        $this->application->setLogger($logger);
        $this->assertSame($logger, $this->application->getLogger());
    }

    /**
     * Tests setting paths
     */
    public function testSettingPaths()
    {
        $paths = new Paths(["baz" => "blah"]);
        $this->application->setPaths($paths);
        $this->assertSame($paths, $this->application->getPaths());
    }

    /**
     * Tests a shutdown task that returns something
     */
    public function testShutdownTaskThatReturnsSomething()
    {
        $this->application->start();
        $this->assertEquals("foo", $this->application->shutdown(function ()
        {
            return "foo";
        }));
    }

    /**
     * Tests a start task that returns something
     */
    public function testStartTaskThatReturnsSomething()
    {
        $this->assertEquals("foo", $this->application->start(function ()
        {
            return "foo";
        }));
    }
} 