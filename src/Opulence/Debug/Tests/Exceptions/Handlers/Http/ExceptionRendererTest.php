<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

// We have to override the built-in functions in the library's namespace
namespace Opulence\Debug\Exceptions\Handlers\Http
{
    /**
     * Overrides the native function for use in testing
     *
     * @param string $string The header string
     */
    function header($string)
    {
        echo "header::$string$$";
    }

    /**
     * Overrides the native function for use in testing
     *
     * @return bool Whether or not the headers were sent
     */
    function headers_sent()
    {
        return false;
    }
}

// The tests
namespace Opulence\Debug\Tests\Exceptions\Handlers\Http
{
    use Exception;
    use Opulence\Debug\Tests\Exceptions\Handlers\Http\Mocks\ExceptionRenderer as MockRenderer;

    /**
     * Tests the HTTP exception renderer
     */
    class ExceptionRendererTest extends \PHPUnit\Framework\TestCase
    {
        /** @var ExceptionRenderer The renderer to use in tests */
        private $renderer;

        /**
         * Sets up the tests
         */
        protected function setUp(): void
        {
            $this->renderer = new MockRenderer(true);
        }

        /**
         * Tests that JSON headers are set
         */
        public function testJsonHeadersSet(): void
        {
            $this->renderer = new MockRenderer(false);
            $this->renderer->setRequestFormat('json');
            $ex = new Exception('foo');
            ob_start();
            $this->renderer->render($ex);
            $contents = ob_get_clean();
            $this->assertTrue($this->hasHeaderString($contents, 'HTTP/1.1 500'));
            $this->assertTrue($this->hasHeaderString($contents, 'Content-Type:application/json'));
            $this->assertEquals('Something went wrong', $this->getContentWithoutHeaders($contents));
        }

        /**
         * Tests rendering an exception without a view in the development environment
         */
        public function testRenderingExceptionWithoutViewInDevelopmentEnvironment(): void
        {
            $ex = new Exception('foo');
            ob_start();
            $this->renderer->render($ex);
            $contents = ob_get_clean();
            $this->assertTrue($this->hasHeaderString($contents, 'HTTP/1.1 500'));
            $this->assertEquals($ex->getMessage(), $this->getContentWithoutHeaders($contents));
        }

        /**
         * Tests rendering an exception without a view in the production environment
         */
        public function testRenderingExceptionWithoutViewInProductionEnvironment(): void
        {
            $this->renderer = new MockRenderer(false);
            $ex = new Exception('foo');
            ob_start();
            $this->renderer->render($ex);
            $contents = ob_get_clean();
            $this->assertTrue($this->hasHeaderString($contents, 'HTTP/1.1 500'));
            $this->assertEquals('Something went wrong', $this->getContentWithoutHeaders($contents));
        }

        /**
         * Gets the content without the headers
         *
         * @param string $rawContents The raw contents
         * @return string The content without the headers
         */
        private function getContentWithoutHeaders($rawContents): string
        {
            return preg_replace('/header::.*\$\$/', '', $rawContents);
        }

        /**
         * Gets whether or not a header string was set
         *
         * @param string $rawContents The raw contents
         * @param string $string The string to search for
         * @return bool Whether or not the header string exists
         */
        private function hasHeaderString($rawContents, $string): bool
        {
            return strpos($rawContents, "header::$string$$") !== false;
        }
    }
}
