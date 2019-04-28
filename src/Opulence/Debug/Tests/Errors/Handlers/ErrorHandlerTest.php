<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Debug\Tests\Errors\Handlers;

use ErrorException;
use Opulence\Debug\Errors\Handlers\ErrorHandler;
use Opulence\Debug\Exceptions\Handlers\IExceptionHandler;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;

/**
 * Tests the error handler
 */
class ErrorHandlerTest extends \PHPUnit\Framework\TestCase
{
    /** @var IExceptionHandler|MockObject */
    private $exceptionHandler;
    /** @var LoggerInterface|MockObject The logger to use in tests */
    private $logger;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->exceptionHandler = $this->getMockBuilder(IExceptionHandler::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    /**
     * Does some housekeeping before ending the tests
     */
    protected function tearDown(): void
    {
        restore_error_handler();
    }

    /**
     * Tests that default levels are not thrown
     */
    public function testDefaultLevelsAreNotThrown(): void
    {
        $handler = $this->getErrorHandler();
        $handler->handle(E_DEPRECATED, 'foo');
        $handler->handle(E_USER_DEPRECATED, 'foo');
        // Essentially just test that we got here
        $this->assertTrue(true);
    }

    /**
     * Tests that the error handler is set
     */
    public function testErrorHandlerIsSet(): void
    {
        $this->expectException(ErrorException::class);
        $handler = $this->getErrorHandler();
        $handler->register();
        trigger_error('foo', E_USER_NOTICE);
    }

    /**
     * Tests that an error is converted to an exception
     */
    public function testErrorIsConvertedToException(): void
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
    public function testLoggerIsNeverUsedByDefault(): void
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
    public function testSpecifiedLevelsAreLogged(): void
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
    public function testSpecifiedLevelsAreThrown(): void
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
    private function getErrorHandler(?int $loggedErrors = null, ?int $thrownErrors = null): ErrorHandler
    {
        return new ErrorHandler(
            $this->logger,
            $this->exceptionHandler,
            $loggedErrors,
            $thrownErrors
        );
    }
}
