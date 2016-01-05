<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Applications\Tasks\Dispatchers;

/**
 * Defines the interface for task dispatchers to implement
 */
interface IDispatcher
{
    /**
     * Dispatches all events of a particular type
     *
     * @param string $taskType The type of tasks to dispatch
     */
    public function dispatch($taskType);

    /**
     * Registers a task of a certain type
     *
     * @param string $taskType The type of task being registered
     * @param callable $task The task to run
     */
    public function registerTask($taskType, callable $task);
}