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
use Opulence\Debug\Exceptions\Handlers\IExceptionHandler;
use Psr\Log\LoggerInterface;

/**
 * Tests the error handler
 */
class ErrorHandlerTest extends \PHPUnit\Framework\TestCase
{
    /** @var IExceptionHandler|\PHPUnit_Framework_MockObject_MockObject */
    private $exceptionHandler = null;
    /** @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject The logger to use in tests */
    private $logger = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->exceptionHandler = $this->getMockBuilder(IExceptionHandler::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    /**
     * Does some housekeeping before ending the tests
     */
    public function tearDown()
    {
        restore_error_handler();
    }

    /**
     * Tests that default levels are not thrown
     */
    public function testDefaultLevelsAreNotThrown()
    {
        $handler = $this->getErrorHandler();
        $handler->handle(E_DEPRECATED, 'foo');
        $handler->handle(E_USER_DEPRECATED, 'foo');
    }

    /**
     * Tests that the error handler is set
     */
    public function testErrorHandlerIsSet()
    {
        $this->expectException(ErrorException::class);
        $handler = $this->getErrorHandler();
        $handler->register();
        trigger_error('foo', E_USER_NOTICE);
    }

    /**
     * Tests that an error is converted to an exception
     */
    public function testErrorIsConvertedToException()
    {
        $exceptionCaught = false;

        try {
            $handler = $this->getErrorHandler();
            $handler->handle(1, 'foo', 'bar', 2, ['baz']);
        } catch (ErrorException $ex) {
            $exceptionCaught = true;
            $this->assertEquals(1, $ex->getSeverity());
            $this->assertEquals('foo', $ex->getMessage());
            $this->assertEquals('bar', $ex->getFile());
            $this->assertEquals(2, $ex->getLine());
            $this->assertEquals(0, $ex->getCode());
        }

        $this->assertTrue($exceptionCaught);
    }

    /**
     * Tests that the logger is never used by default
     */
    public function testLoggerIsNeverUsedByDefault()
    {
        $this->expectException(ErrorException::class);
        $handler = $this->getErrorHandler();
        $this->logger->expects($this->never())
            ->method('log');
        $handler->handle(E_USER_NOTICE, 'foo');
    }

    /**
     * Tests that specified levels are logged
     */
    public function testSpecifiedLevelsAreLogged()
    {
        $handler = $this->getErrorHandler(E_NOTICE, 0);
        $this->logger->expects($this->once())
            ->method('log')
            ->with(E_NOTICE, 'foo', []);
        $handler->handle(E_NOTICE, 'foo');
    }

    /**
     * Tests that specified levels are thrown
     */
    public function testSpecifiedLevelsAreThrown()
    {
        $this->expectException(ErrorException::class);
        $handler = $this->getErrorHandler(null, E_DEPRECATED);
        $handler->handle(E_DEPRECATED, 'foo');
    }

    /**
     * Gets the error handler
     *
     * @param int|null $loggedErrors The logged errors
     * @param int|null $thrownErrors The errors that are converted to exceptions
     * @return ErrorHandler The handler to use in tests
     */
    private function getErrorHandler($loggedErrors = null, $thrownErrors = null)
    {
        return new ErrorHandler(
            $this->logger,
            $this->exceptionHandler,
            $loggedErrors,
            $thrownErrors
        );
    }
}
