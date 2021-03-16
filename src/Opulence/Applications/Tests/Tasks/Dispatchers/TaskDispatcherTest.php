<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Applications\Tests\Tasks\Dispatchers;

use Opulence\Applications\Tasks\Dispatchers\TaskDispatcher;
use Opulence\Applications\Tasks\TaskTypes;

/**
 * Tests the task dispatcher
 */
class TaskDispatcherTest extends \PHPUnit\Framework\TestCase
{
    /** @var TaskDispatcher The dispatcher to use in tests */
    private $dispatcher = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->dispatcher = new TaskDispatcher();
    }

    public function testRegisteringCustomTaskType()
    {
        $value = '';
        $this->dispatcher->registerTask('foo', function () use (&$value) {
            $value = 'foo';
        });
        $this->dispatcher->dispatch('foo');
        $this->assertEquals('foo', $value);
    }

    /**
     * Tests registering post-shutdown tasks
     */
    public function testRegisteringPostShutdownTask()
    {
        $value = '';
        $this->dispatcher->registerTask(TaskTypes::POST_SHUTDOWN, function () use (&$value) {
            $value = 'foo';
        });
        $this->dispatcher->dispatch(TaskTypes::POST_SHUTDOWN);
        $this->assertEquals('foo', $value);
    }

    /**
     * Tests registering post-start tasks
     */
    public function testRegisteringPostStartTask()
    {
        $value = '';
        $this->dispatcher->registerTask(TaskTypes::POST_START, function () use (&$value) {
            $value = 'foo';
        });
        $this->dispatcher->dispatch(TaskTypes::POST_START);
        $this->assertEquals('foo', $value);
    }

    /**
     * Tests registering pre-shutdown tasks
     */
    public function testRegisteringPreShutdownTask()
    {
        $value = '';
        $this->dispatcher->registerTask(TaskTypes::PRE_SHUTDOWN, function () use (&$value) {
            $value = 'foo';
        });
        $this->dispatcher->dispatch(TaskTypes::PRE_SHUTDOWN);
        $this->assertEquals('foo', $value);
    }

    /**
     * Tests registering pre-start tasks
     */
    public function testRegisteringPreStartTask()
    {
        $value = '';
        $this->dispatcher->registerTask(TaskTypes::PRE_START, function () use (&$value) {
            $value = 'foo';
        });
        $this->dispatcher->dispatch(TaskTypes::PRE_START);
        $this->assertEquals('foo', $value);
    }
}
