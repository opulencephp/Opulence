<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Debug\Errors\Handlers;

use ErrorException;
use Opulence\Debug\Exceptions\FatalErrorException;
use Opulence\Debug\Exceptions\Handlers\IExceptionHandler;

/**
 * Defines the PHP error handler
 */
class ErrorHandler implements IErrorHandler
{
    /** @var IExceptionHandler The exception handler */
    protected $exceptionHandler = null;

    /**
     * @param IExceptionHandler $exceptionHandler The exception handler
     */
    public function __construct(IExceptionHandler $exceptionHandler)
    {
        $this->exceptionHandler = $exceptionHandler;
    }

    /**
     * @inheritDoc
     */
    public function handle($level, $message, $file = "", $line = 0, array $context = [])
    {
        if (error_reporting() & $level !== 0) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * @inheritdoc
     */
    public function handleShutdown()
    {
        $error = error_get_last();

        if ($error !== null && in_array($error["type"], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $this->exceptionHandler->handle(new FatalErrorException(
                $error["message"],
                $error["type"],
                0,
                $error["file"],
                $error["line"]
            ));
        }
    }

    /**
     * @inheritDoc
     */
    public function register()
    {
        ini_set("display_errors", "off");
        error_reporting(-1);
        set_error_handler([$this, "handle"]);
        register_shutdown_function([$this, "handleShutdown"]);
    }
}