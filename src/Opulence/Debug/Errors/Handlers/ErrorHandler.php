<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Debug\Errors\Handlers;

use ErrorException;
use Opulence\Debug\Exceptions\FatalErrorException;
use Opulence\Debug\Exceptions\Handlers\IExceptionHandler;
use Psr\Log\LoggerInterface;

/**
 * Defines the PHP error handler
 */
class ErrorHandler implements IErrorHandler
{
    /** @var LoggerInterface The logger */
    protected $logger = null;
    /** @var IExceptionHandler The exception handler */
    protected $exceptionHandler = null;
    /** @var int $loggedLevels The bitwise value of error levels that are to be logged */
    protected $loggedLevels = 0;
    /** @var int $thrownLevels The bitwise value of error levels that are to be thrown as exceptions */
    protected $thrownLevels = 0;

    /**
     * @param LoggerInterface $logger The logger
     * @param IExceptionHandler $exceptionHandler The exception handler
     * @param int|null $loggedLevels The bitwise value of error levels that are to be logged
     * @param int|null $thrownLevels The bitwise value of error levels that are to be thrown as exceptions
     */
    public function __construct(
        LoggerInterface $logger,
        IExceptionHandler $exceptionHandler,
        int $loggedLevels = null,
        int $thrownLevels = null
    ) {
        $this->logger = $logger;
        $this->exceptionHandler = $exceptionHandler;
        $this->loggedLevels = $loggedLevels ?? 0;
        $this->thrownLevels = $thrownLevels ?? (E_ALL & ~(E_DEPRECATED | E_USER_DEPRECATED));
    }

    /**
     * @inheritdoc
     */
    public function handle(int $level, string $message, string $file = '', int $line = 0, array $context = [])
    {
        if ($this->levelIsLoggable($level)) {
            $this->logger->log($level, $message, $context);
        }

        if ($this->levelIsThrowable($level)) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * @inheritdoc
     */
    public function handleShutdown()
    {
        $error = error_get_last();

        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $this->exceptionHandler->handle(new FatalErrorException(
                $error['message'],
                $error['type'],
                0,
                $error['file'],
                $error['line']
            ));
        }
    }

    /**
     * @inheritdoc
     */
    public function register()
    {
        ini_set('display_errors', 'off');
        error_reporting(-1);
        set_error_handler([$this, 'handle']);
        register_shutdown_function([$this, 'handleShutdown']);
    }

    /**
     * Gets whether or not the level is loggable
     *
     * @param int $level The bitwise level
     * @return bool True if the level is loggable, otherwise false
     */
    protected function levelIsLoggable(int $level) : bool
    {
        return ($this->loggedLevels & $level) !== 0;
    }

    /**
     * Gets whether or not the level is throwable
     *
     * @param int $level The bitwise level
     * @return bool True if the level is throwable, otherwise false
     */
    protected function levelIsThrowable(int $level) : bool
    {
        return ($this->thrownLevels & $level) !== 0;
    }
}
