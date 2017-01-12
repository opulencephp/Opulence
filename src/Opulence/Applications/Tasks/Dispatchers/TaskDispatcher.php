<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Applications\Tasks\Dispatchers;

use Exception;
use RuntimeException;

/**
 * Defines the task dispatcher
 */
class TaskDispatcher implements ITaskDispatcher
{
    /** @var callable[][] The list of task callbacks */
    private $tasks = [
        'preStart' => [],
        'postStart' => [],
        'preShutdown' => [],
        'postShutdown' => []
    ];

    /**
     * @inheritdoc
     */
    public function dispatch(string $taskType)
    {
        try {
            foreach ($this->tasks[$taskType] as $task) {
                $task();
            }
        } catch (Exception $ex) {
            throw new RuntimeException('Failed to run tasks', 0, $ex);
        }
    }

    /**
     * @inheritdoc
     */
    public function registerTask(string $taskType, callable $task)
    {
        if (!isset($this->tasks[$taskType])) {
            $this->tasks[$taskType] = [];
        }

        $this->tasks[$taskType][] = $task;
    }
}
