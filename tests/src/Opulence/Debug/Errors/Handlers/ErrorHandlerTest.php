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
use Opulence\Debug\Exceptions\Handlers\IExceptionHandler;

/**
 * Tests the error handler
 */
class ErrorHandlerTest extends \PHPUnit_Framework_TestCase
{
    /** @var ErrorHandler The handler to use in tests */
    private $handler = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        /** @var IExceptionHandler|\PHPUnit_Framework_MockObject_MockObject $exceptionHandler */
        $exceptionHandler = $this->getMock(IExceptionHandler::class, [], [], "", false);
        $this->handler = new ErrorHandler($exceptionHandler);
        $this->handler->register();
    }

    /**
     * Does some housekeeping before ending the tests
     */
    public function tearDown()
    {
        restore_error_handler();
    }

    /**
     * Tests that the error handler is set
     */
    public function testErrorHandlerIsSet()
    {
        $this->setExpectedException(ErrorException::class);
        trigger_error("foo", E_USER_NOTICE);
    }

    /**
     * Tests that an error is converted to an exception
     */
    public function testErrorIsConvertedToException()
    {
        $this->setExpectedException(ErrorException::class);
        $this->handler->handle(1, "foo", "bar", 2, ["baz"]);
    }
}