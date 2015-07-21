<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the task dispatcher
 */
namespace Opulence\Applications\Tasks\Dispatchers;
use Exception;
use RuntimeException;

class Dispatcher implements IDispatcher
{
    /** @var array The list of task callbacks */
    private $tasks = [
        "preStart" => [],
        "postStart" => [],
        "preShutdown" => [],
        "postShutdown" => []
    ];

    /**
     * @inheritdoc
     */
    public function dispatch($taskType)
    {
        try
        {
            foreach($this->tasks[$taskType] as $task)
            {
                call_user_func($task);
            }
        }
        catch(Exception $ex)
        {
            throw new RuntimeException("Failed to run tasks: {$ex->getMessage()}");
        }
    }

    /**
     * @inheritdoc
     */
    public function registerTask($taskType, callable $task)
    {
        if(!isset($this->tasks[$taskType]))
        {
            $this->tasks[$taskType] = [];
        }

        $this->tasks[$taskType][] = $task;
    }
}