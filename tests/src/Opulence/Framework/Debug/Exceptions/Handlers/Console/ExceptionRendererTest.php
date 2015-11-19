<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Debug\Exceptions\Handlers\Console;

use Opulence\Console\Responses\IResponse;
use InvalidArgumentException;
use RuntimeException;

/**
 * Tests the console exception renderer
 */
class ExceptionRendererTest extends \PHPUnit_Framework_TestCase
{
    /** @var ExceptionRenderer The renderer to use in tests */
    private $renderer = null;
    /** @var IResponse|\PHPUnit_Framework_MockObject_MockObject The response to write to */
    private $response = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->response = $this->getMock(IResponse::class);
        $this->renderer = new ExceptionRenderer();
        $this->renderer->setResponse($this->response);
    }

    /**
     * Tests rendering an invalid argument exception
     */
    public function testRenderingInvalidArgumentException()
    {
        $exception = new InvalidArgumentException("foo");
        $this->response->expects($this->once())
            ->method("writeln")
            ->with("<error>{$exception->getMessage()}</error>");
        $this->renderer->render($exception);
    }

    /**
     * Tests rendering a runtime exception
     */
    public function testRenderingRuntimeException()
    {
        $exception = new RuntimeException("foo");
        $this->response->expects($this->once())
            ->method("writeln")
            ->with("<fatal>{$exception->getMessage()}</fatal>");
        $this->renderer->render($exception);
    }
}