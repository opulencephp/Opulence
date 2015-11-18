<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Exceptions;

use ErrorException;
use Exception;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Defines the exception handler
 */
class ExceptionHandler
{
    /** @var LoggerInterface The logger */
    protected $logger = null;
    /** @var IExceptionRenderer The exception renderer */
    protected $exceptionRenderer = null;
    /** @var array The list of exception classes to not log */
    protected $exceptionsNotLogged = [];

    /**
     * @param LoggerInterface $logger The logger
     * @param IExceptionRenderer $exceptionRenderer The exception renderer
     */
    public function __construct(LoggerInterface $logger, IExceptionRenderer $exceptionRenderer)
    {
        $this->logger = $logger;
        $this->exceptionRenderer = $exceptionRenderer;
        $this->configurePhp();
    }

    /**
     * Adds exception classes to not log
     *
     * @param string|array $exceptionClasses The class or classes of exceptions to not log
     */
    public function doNotLog($exceptionClasses)
    {
        $this->exceptionsNotLogged = array_merge($this->exceptionsNotLogged, (array)$exceptionClasses);
    }

    /**
     * Handles an error
     *
     * @param int $level The level of the error
     * @param string $message The message
     * @param string $file The file the error occurred in
     * @param int $line The line number the error occurred at
     * @param array $context The symbol table
     * @throws ErrorException Thrown because the error is converted to an exception
     */
    public function handleError($level, $message, $file = "", $line = 0, array $context = [])
    {
        if (error_reporting() & $level !== 0) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * Handles an exception
     *
     * @param Throwable $ex The exception to handle
     */
    public function handleException($ex)
    {
        // It's Throwable, but not an Exception
        if (!$ex instanceof Exception) {
            $ex = new FatalThrowableError($ex);
        }

        if (!in_array(get_class($ex), $this->exceptionsNotLogged)) {
            $this->logger->error($ex);
        }

        $this->exceptionRenderer->render($ex);
    }

    /**
     * Handles a PHP shutdown
     */
    public function handleShutdown()
    {
        $error = error_get_last();

        if ($error !== null && in_array($error["type"], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $this->handleException(new FatalErrorException(
                $error["message"],
                $error["type"],
                0,
                $error["file"],
                $error["line"]
            ));
        }
    }

    /**
     * Configures PHP
     */
    protected function configurePhp()
    {
        ini_set("display_errors", "off");
        error_reporting(-1);
        set_exception_handler([$this, "handleException"]);
        set_error_handler([$this, "handleError"]);
        register_shutdown_function([$this, "handleShutdown"]);
    }
}