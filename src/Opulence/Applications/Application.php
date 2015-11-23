<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Applications;

use Closure;
use Exception;
use Opulence\Applications\Tasks\Dispatchers\IDispatcher;
use Opulence\Applications\Tasks\TaskTypes;

/**
 * Defines an application
 */
class Application
{
    /** @var string The current Opulence version */
    private static $version = "1.0.0-alpha19";
    /** @var IDispatcher The task dispatcher */
    private $taskDispatcher = null;
    /** @var bool Whether or not the application is currently running */
    private $isRunning = false;

    /**
     * @param IDispatcher $taskDispatcher The task dispatcher
     */
    public function __construct(IDispatcher $taskDispatcher)
    {
        $this->taskDispatcher = $taskDispatcher;
    }

    /**
     * @return string
     */
    public static function getVersion()
    {
        return self::$version;
    }

    /**
     * @return bool
     */
    public function isRunning()
    {
        return $this->isRunning;
    }

    /**
     * Shuts down this application
     *
     * @param Closure $shutdownTask The task to perform on shutdown
     * @return mixed|null The return value of the task if there was one, otherwise null
     * @throws Exception Thrown if there was an error shutting down the application
     */
    public function shutDown(Closure $shutdownTask = null)
    {
        $taskReturnValue = null;

        // Don't shut down a shutdown application
        if ($this->isRunning) {
            try {
                $this->taskDispatcher->dispatch(TaskTypes::PRE_SHUTDOWN);
                $this->isRunning = false;

                if ($shutdownTask !== null) {
                    $taskReturnValue = call_user_func($shutdownTask);
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
     * @param Closure $startTask The task to perform on startup
     * @return mixed|null The return value of the task if there was one, otherwise null
     * @throws Exception Thrown if there was a problem starting the application
     */
    public function start(Closure $startTask = null)
    {
        $taskReturnValue = null;

        // Don't start a running application
        if (!$this->isRunning) {
            try {
                $this->taskDispatcher->dispatch(TaskTypes::PRE_START);
                $this->isRunning = true;

                if ($startTask !== null) {
                    $taskReturnValue = call_user_func($startTask);
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