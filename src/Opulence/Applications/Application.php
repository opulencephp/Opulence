<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Applications;

use Exception;
use Opulence\Applications\Tasks\Dispatchers\ITaskDispatcher;
use Opulence\Applications\Tasks\TaskTypes;

/**
 * Defines an application
 */
class Application
{
    /** @var string The current Opulence version */
    private static $opulenceVersion = '1.0.5';
    /** @var ITaskDispatcher The task dispatcher */
    private $taskDispatcher = null;
    /** @var string The version of the application */
    private $version = '';
    /** @var bool Whether or not the application is currently running */
    private $isRunning = false;

    /**
     * @param ITaskDispatcher $taskDispatcher The task dispatcher
     * @param string $version The version of the application
     */
    public function __construct(ITaskDispatcher $taskDispatcher, string $version = null)
    {
        $this->taskDispatcher = $taskDispatcher;
        $this->version = $version ?? self::$opulenceVersion;
    }

    /**
     * @return string
     */
    public function getVersion() : string
    {
        return $this->version;
    }

    /**
     * @return bool
     */
    public function isRunning() : bool
    {
        return $this->isRunning;
    }

    /**
     * Shuts down this application
     *
     * @param callable $shutdownTask The task to perform on shutdown
     * @return mixed|null The return value of the task if there was one, otherwise null
     * @throws Exception Thrown if there was an error shutting down the application
     */
    public function shutDown(callable $shutdownTask = null)
    {
        $taskReturnValue = null;

        // Don't shut down a shutdown application
        if ($this->isRunning) {
            try {
                $this->taskDispatcher->dispatch(TaskTypes::PRE_SHUTDOWN);
                $this->isRunning = false;

                if ($shutdownTask !== null) {
                    $taskReturnValue = $shutdownTask();
                }

                $this->taskDispatcher->dispatch(TaskTypes::POST_SHUTDOWN);
            } catch (Exception $ex) {
                $this->isRunning = false;

                throw $ex;
            }
        }

        return $taskReturnValue;
    }

    /**
     * Starts this application
     *
     * @param callable $startTask The task to perform on startup
     * @return mixed|null The return value of the task if there was one, otherwise null
     * @throws Exception Thrown if there was a problem starting the application
     */
    public function start(callable $startTask = null)
    {
        $taskReturnValue = null;

        // Don't start a running application
        if (!$this->isRunning) {
            try {
                $this->taskDispatcher->dispatch(TaskTypes::PRE_START);
                $this->isRunning = true;

                if ($startTask !== null) {
                    $taskReturnValue = $startTask();
                }

                $this->taskDispatcher->dispatch(TaskTypes::POST_START);
            } catch (Exception $ex) {
                $this->shutDown();

                throw $ex;
            }
        }

        return $taskReturnValue;
    }
}
