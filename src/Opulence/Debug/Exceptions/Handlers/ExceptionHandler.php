<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Debug\Exceptions\Handlers;

use Exception;
use Opulence\Debug\Errors\FatalThrowableError;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Defines the exception handler
 */
class ExceptionHandler implements IExceptionHandler
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
     * @param string|array $exceptionsNotLogged The exception or list of exceptions to not log when thrown
     */
    public function __construct(
        LoggerInterface $logger,
        IExceptionRenderer $exceptionRenderer,
        $exceptionsNotLogged = []
    ) {
        $this->logger = $logger;
        $this->exceptionRenderer = $exceptionRenderer;
        $this->exceptionsNotLogged = (array)$exceptionsNotLogged;
    }

    /**
     * @inheritdoc
     */
    public function handle($ex)
    {
        // It's Throwable, but not an Exception
        if (!$ex instanceof Exception) {
            $ex = new FatalThrowableError($ex);
        }

        if ($this->shouldLog($ex)) {
            $this->logger->error($ex);
        }

        $this->exceptionRenderer->render($ex);
    }

    /**
     * @inheritdoc
     */
    public function register()
    {
        set_exception_handler([$this, "handle"]);
    }

    /**
     * Determines whether or not an exception should be logged
     *
     * @param Throwable|Exception $ex The exception to check
     * @return bool True if the exception should be logged, otherwise false
     */
    protected function shouldLog($ex) : bool
    {
        return !in_array(get_class($ex), $this->exceptionsNotLogged);
    }
}
