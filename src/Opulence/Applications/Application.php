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
use Opulence\Applications\Environments\Environment;
use Opulence\Applications\Tasks\Dispatchers\IDispatcher;
use Opulence\Applications\Tasks\TaskTypes;
use RuntimeException;

/**
 * Defines an application
 */
class Application
{
    /** @var string The current Opulence version */
    private static $version = "1.0.0-alpha15";
    /** @var IDispatcher The task dispatcher */
    private $taskDispatcher = null;
    /** @var Environment The environment the application is running on */
    private $environment = null;
    /** @var bool Whether or not the application is currently running */
    private $isRunning = false;

    /**
     * @param IDispatcher $taskDispatcher The task dispatcher
     * @param Environment $environment The current environment
     */
    public function __construct(IDispatcher $taskDispatcher, Environment $environment)
    {
        $this->taskDispatcher = $taskDispatcher;
        $this->environment = $environment;
    }

    /**
     * @return string
     */
    public static function getVersion()
    {
        return self::$version;
    }

    /**
     * @return Environment
     */
    public function getEnvironment()
    {
        return $this->environment;
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
     * @throws RuntimeException Thrown if there was an error shutting down the application
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
            }
        }

        return $taskReturnValue;
    }

    /**
     * Starts this application
     *
     * @param Closure $startTask The task to perform on startup
     * @return mixed|null The return value of the task if there was one, otherwise null
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
            }
        }

        return $taskReturnValue;
    }
} 