<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Debug\Tests\Exceptions\Handlers;

use Exception;
use Opulence\Debug\Exceptions\Handlers\ExceptionHandler;
use Opulence\Debug\Exceptions\Handlers\IExceptionRenderer;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use RuntimeException;

/**
 * Tests the exception handler
 */
class ExceptionHandlerTest extends \PHPUnit\Framework\TestCase
{
    /** @var ExceptionHandler The handler to use in tests */
    private $handler;
    /** @var LoggerInterface|MockObject The logger to use in tests */
    private $logger;
    /** @var IExceptionRenderer|MockObject The renderer to use in tests */
    private $renderer;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->renderer = $this->createMock(IExceptionRenderer::class);
        $this->handler = new ExceptionHandler($this->logger, $this->renderer);
        $this->handler->register();
    }

    /**
     * Does some housekeeping before ending the tests
     */
    protected function tearDown(): void
    {
        restore_exception_handler();
    }

    /**
     * Tests that exception is reported and rendered
     */
    public function testExceptionIsReportedAndRendered(): void
    {
        $exception = new Exception();
        $this->logger->expects($this->once())
            ->method('error')
            ->with($exception);
        $this->renderer->expects($this->once())
            ->method('render')
            ->with($exception);
        $this->handler->handle($exception);
    }

    /**
     * Tests that exceptions are not logged when told not to
     */
    public function testExceptionNotLoggedWhenToldNotTo(): void
    {
        /** @var LoggerInterface|MockObject $logger */
        $logger = $this->createMock(LoggerInterface::class);
        /** @var IExceptionRenderer|MockObject $renderer */
        $renderer = $this->createMock(IExceptionRenderer::class);
        $handler = new ExceptionHandler($logger, $renderer, RuntimeException::class);
        $exception = new RuntimeException();
        $logger->expects($this->never())
            ->method('error');
        $renderer->expects($this->exactly(2))
            ->method('render')
            ->with($exception);
        $handler->handle($exception);
        // Try an array
        $handler = new ExceptionHandler($logger, $renderer, [RuntimeException::class]);
        $handler->handle($exception);
    }
}
