<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Debug\Tests\Exceptions\Handlers;

use Exception;
use Opulence\Debug\Exceptions\Handlers\ExceptionHandler;
use Opulence\Debug\Exceptions\Handlers\IExceptionRenderer;
use Psr\Log\LoggerInterface;
use RuntimeException;

/**
 * Tests the exception handler
 */
class ExceptionHandlerTest extends \PHPUnit\Framework\TestCase
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
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->renderer = $this->createMock(IExceptionRenderer::class);
        $this->handler = new ExceptionHandler($this->logger, $this->renderer);
        $this->handler->register();
    }

    /**
     * Does some housekeeping before ending the tests
     */
    public function tearDown()
    {
        restore_exception_handler();
    }

    /**
     * Tests that exception is reported and rendered
     */
    public function testExceptionIsReportedAndRendered()
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
    public function testExceptionNotLoggedWhenToldNotTo()
    {
        /** @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject $logger */
        $logger = $this->createMock(LoggerInterface::class);
        /** @var IExceptionRenderer|\PHPUnit_Framework_MockObject_MockObject $renderer */
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
