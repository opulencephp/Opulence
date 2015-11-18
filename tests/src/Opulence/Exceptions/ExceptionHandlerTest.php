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
use RuntimeException;
use Throwable;

/**
 * Tests the exception handler
 */
class ExceptionHandlerTest extends \PHPUnit_Framework_TestCase
{
    /** @var ExceptionHandler The handler to use in tests */
    private $handler = null;
    /** @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject The logger to use in tests */
    private $logger = null;
    /** @var IExceptionRenderer|\PHPUnit_Framework_MockObject_MockObject The renderer to use in tests */
    private $renderer = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->logger = $this->getMock(LoggerInterface::class);
        $this->renderer = $this->getMock(IExceptionRenderer::class);
        $this->handler = new ExceptionHandler($this->logger, $this->renderer);
    }

    /**
     * Does some housekeeping before ending the tests
     */
    public function tearDown()
    {
        restore_error_handler();
        restore_exception_handler();
    }

    /**
     * Tests that the error handler is set
     */
    public function testErrorHandlerIsSet()
    {
        $this->setExpectedException(ErrorException::class);
        trigger_error("foo", 1);
    }

    /**
     * Tests that an error is converted to an exception
     */
    public function testErrorIsConvertedToException()
    {
        $this->setExpectedException(ErrorException::class);
        $this->handler->handleError(1, "foo", "bar", 2, ["baz"]);
    }

    /**
     * Tests that exception is reported and rendered
     */
    public function testExceptionIsReportedAndRendered()
    {
        $exception = new Exception();
        $this->logger->expects($this->once())
            ->method("error")
            ->with($exception);
        $this->renderer->expects($this->once())
            ->method("render")
            ->with($exception);
        $this->handler->handleException($exception);
    }

    /**
     * Tests that exceptions are not logged when told not to
     */
    public function testExceptionsNotLoggedWhenToldNotTo()
    {
        $exception = new RuntimeException();
        $this->handler->doNotLog(RuntimeException::class);
        $this->logger->expects($this->never())
            ->method("error");
        $this->renderer->expects($this->once())
            ->method("render")
            ->with($exception);
        $this->handler->handleException($exception);
    }

    /**
     * Tests handling a throwable exception
     */
    public function testHandlingThrowableException()
    {
        /** @var Throwable|\PHPUnit_Framework_MockObject_MockObject $throwable */
        $throwable = $this->getMock(Throwable::class, ["getCode", "getFile", "getLine", "getMessage"]);
        $this->logger->expects($this->once())
            ->method("error");
        $this->renderer->expects($this->once())
            ->method("render");
        $this->handler->handleException($throwable);
    }
}