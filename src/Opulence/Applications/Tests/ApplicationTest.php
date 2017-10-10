<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Applications\Tests;

use InvalidArgumentException;
use Opulence\Applications\Application;
use Opulence\Applications\Tasks\Dispatchers\ITaskDispatcher;
use Opulence\Applications\Tasks\TaskTypes;
use ReflectionClass;

/**
 * Tests the application class
 */
class ApplicationTest extends \PHPUnit\Framework\TestCase
{
    /** @var Application The application to use in the tests */
    private $application = null;
    /** @var ITaskDispatcher|\PHPUnit_Framework_MockObject_MockObject The task dispatcher */
    private $dispatcher = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->dispatcher = $this->createMock(ITaskDispatcher::class);
        $this->application = new Application($this->dispatcher);
    }

    /**
     * Tests that our application only attempts to shutdown once when it's already shutdown
     */
    public function testApplicationIsNotShutdownTwice()
    {
        $this->dispatcher->expects($this->at(0))
            ->method('dispatch')
            ->with(TaskTypes::PRE_START);
        $this->dispatcher->expects($this->at(1))
            ->method('dispatch')
            ->with(TaskTypes::POST_START);
        $this->dispatcher->expects($this->at(2))
            ->method('dispatch')
            ->with(TaskTypes::PRE_SHUTDOWN);
        $this->dispatcher->expects($this->at(3))
            ->method('dispatch')
            ->with(TaskTypes::POST_SHUTDOWN);
        $this->application->start();
        $this->application->shutDown();
        $this->application->shutDown();
    }

    /**
     * Tests that our application only attempts to start up once when it's already running
     */
    public function testApplicationIsNotStartedTwice()
    {
        $this->dispatcher->expects($this->at(0))
            ->method('dispatch')
            ->with(TaskTypes::PRE_START);
        $this->dispatcher->expects($this->at(1))
            ->method('dispatch')
            ->with(TaskTypes::POST_START);
        $this->application->start();
        $this->application->start();
    }

    /**
     * Tests registering a bad post-shutdown task
     */
    public function testBadPostShutdownTask()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->dispatcher->expects($this->at(3))
            ->method('dispatch')
            ->with(TaskTypes::POST_SHUTDOWN)
            ->will($this->throwException(new InvalidArgumentException('foo')));
        $this->assertNull($this->application->start());
        $this->assertNull($this->application->shutDown());
        $this->assertFalse($this->application->isRunning());
    }

    /**
     * Tests registering a bad post-start task
     */
    public function testBadPostStartTask()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->dispatcher->expects($this->at(1))
            ->method('dispatch')
            ->will($this->throwException(new InvalidArgumentException('foo')));
        $this->assertNull($this->application->start());
        $this->assertFalse($this->application->isRunning());
    }

    /**
     * Tests registering a bad pre-shutdown task
     */
    public function testBadPreShutdownTask()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->dispatcher->expects($this->at(2))
            ->method('dispatch')
            ->with(TaskTypes::PRE_SHUTDOWN)
            ->will($this->throwException(new InvalidArgumentException('foo')));
        $this->assertNull($this->application->start());
        $this->assertNull($this->application->shutDown());
        $this->assertFalse($this->application->isRunning());
    }

    /**
     * Tests registering a bad shutdown task
     */
    public function testBadShutdownTask()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->assertNull($this->application->start());
        $this->assertNull($this->application->shutDown(function () {
            // Throw anything other than a runtime exception
            throw new InvalidArgumentException('foobar');
        }));
        $this->assertFalse($this->application->isRunning());
    }

    /**
     * Tests registering a bad start task
     */
    public function testBadStartTask()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->assertNull($this->application->start(function () {
            // Throw anything other than a runtime exception
            throw new InvalidArgumentException('foobar');
        }));
        $this->assertFalse($this->application->isRunning());
    }

    /**
     * Tests checking if a shutdown application is no longer running
     */
    public function testCheckingIfAShutdownApplicationIsNotRunning()
    {
        $this->application->start();
        $this->application->shutDown();
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
     * Tests getting the custom application version
     */
    public function testGettingCustomVersion()
    {
        $application = new Application($this->createMock(ITaskDispatcher::class), 'foo');
        $this->assertEquals('foo', $application->getVersion());
    }

    /**
     * Tests getting the default application version
     */
    public function testGettingDefaultVersion()
    {
        $reflectionClass = new ReflectionClass($this->application);
        $property = $reflectionClass->getProperty('opulenceVersion');
        $property->setAccessible(true);
        $this->assertEquals($property->getValue(), $this->application->getVersion());
    }

    /**
     * Tests registering post-shutdown tasks
     */
    public function testRegisteringPostShutdownTask()
    {
        $this->dispatcher->expects($this->at(0))
            ->method('dispatch')
            ->with(TaskTypes::PRE_START);
        $this->dispatcher->expects($this->at(1))
            ->method('dispatch')
            ->with(TaskTypes::POST_START);
        $this->dispatcher->expects($this->at(2))
            ->method('dispatch')
            ->with(TaskTypes::PRE_SHUTDOWN);
        $this->dispatcher->expects($this->at(3))
            ->method('dispatch')
            ->with(TaskTypes::POST_SHUTDOWN);
        $this->application->start();
        $this->application->shutDown();
    }

    /**
     * Tests registering post-start tasks
     */
    public function testRegisteringPostStartTask()
    {
        $this->dispatcher->expects($this->at(0))
            ->method('dispatch')
            ->with(TaskTypes::PRE_START);
        $this->dispatcher->expects($this->at(1))
            ->method('dispatch')
            ->with(TaskTypes::POST_START);
        $this->application->start();
    }

    /**
     * Tests registering pre- and post-shutdown tasks
     */
    public function testRegisteringPreAndPostShutdownTasks()
    {
        $this->dispatcher->expects($this->at(0))
            ->method('dispatch')
            ->with(TaskTypes::PRE_START);
        $this->dispatcher->expects($this->at(1))
            ->method('dispatch')
            ->with(TaskTypes::POST_START);
        $this->dispatcher->expects($this->at(2))
            ->method('dispatch')
            ->with(TaskTypes::PRE_SHUTDOWN);
        $this->dispatcher->expects($this->at(3))
            ->method('dispatch')
            ->with(TaskTypes::POST_SHUTDOWN);
        $this->application->start();
        $this->application->shutDown();
    }

    /**
     * Tests registering pre- and post-start tasks
     */
    public function testRegisteringPreAndPostStartTasks()
    {
        $this->dispatcher->expects($this->at(0))
            ->method('dispatch')
            ->with(TaskTypes::PRE_START);
        $this->dispatcher->expects($this->at(1))
            ->method('dispatch')
            ->with(TaskTypes::POST_START);
        $this->application->start();
    }

    /**
     * Tests registering pre-shutdown tasks
     */
    public function testRegisteringPreShutdownTask()
    {
        $this->dispatcher->expects($this->at(0))
            ->method('dispatch')
            ->with(TaskTypes::PRE_START);
        $this->dispatcher->expects($this->at(1))
            ->method('dispatch')
            ->with(TaskTypes::POST_START);
        $this->dispatcher->expects($this->at(2))
            ->method('dispatch')
            ->with(TaskTypes::PRE_SHUTDOWN);
        $this->dispatcher->expects($this->at(3))
            ->method('dispatch')
            ->with(TaskTypes::POST_SHUTDOWN);
        $this->application->start();
        $this->application->shutDown();
    }

    /**
     * Tests registering pre-start tasks
     */
    public function testRegisteringPreStartTask()
    {
        $this->dispatcher->expects($this->at(0))
            ->method('dispatch')
            ->with(TaskTypes::PRE_START);
        $this->dispatcher->expects($this->at(1))
            ->method('dispatch')
            ->with(TaskTypes::POST_START);
        $this->application->start();
    }

    /**
     * Tests registering a shutdown task
     */
    public function testRegisteringShutdownTask()
    {
        $this->dispatcher->expects($this->at(0))
            ->method('dispatch')
            ->with(TaskTypes::PRE_START);
        $this->dispatcher->expects($this->at(1))
            ->method('dispatch')
            ->with(TaskTypes::POST_START);
        $this->dispatcher->expects($this->at(2))
            ->method('dispatch')
            ->with(TaskTypes::PRE_SHUTDOWN);
        $this->dispatcher->expects($this->at(3))
            ->method('dispatch')
            ->with(TaskTypes::POST_SHUTDOWN);
        $this->application->start();
        $shutdownValue = null;
        $this->assertNull($this->application->shutDown(function () use (&$shutdownValue) {
            $shutdownValue = 'baz';
        }));
        $this->assertEquals('baz', $shutdownValue);
    }

    /**
     * Tests registering a start task
     */
    public function testRegisteringStartTask()
    {
        $this->dispatcher->expects($this->at(0))
            ->method('dispatch')
            ->with(TaskTypes::PRE_START);
        $this->dispatcher->expects($this->at(1))
            ->method('dispatch')
            ->with(TaskTypes::POST_START);
        $startValue = '';
        $this->assertNull($this->application->start(function () use (&$startValue) {
            $startValue = 'baz';
        }));
        $this->assertEquals('baz', $startValue);
    }

    /**
     * Tests a shutdown task that returns something
     */
    public function testShutdownTaskThatReturnsSomething()
    {
        $this->application->start();
        $this->assertEquals('foo', $this->application->shutDown(function () {
            return 'foo';
        }));
    }

    /**
     * Tests a start task that returns something
     */
    public function testStartTaskThatReturnsSomething()
    {
        $this->assertEquals('foo', $this->application->start(function () {
            return 'foo';
        }));
    }
}
