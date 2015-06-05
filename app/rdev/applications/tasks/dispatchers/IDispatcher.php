<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for task dispatchers to implement
 */
namespace RDev\Applications\Tasks\Dispatchers;

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