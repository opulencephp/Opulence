<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Debug\Exceptions\Handlers\Http;

use Exception;
use Opulence\Tests\Debug\Exceptions\Handlers\Http\Mocks\ExceptionRenderer as MockRenderer;

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

/**
 * Tests the HTTP exception renderer
 */
class ExceptionRendererTest extends \PHPUnit_Framework_TestCase
{
    /** @var ExceptionRenderer The renderer to use in tests */
    private $renderer = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->renderer = new MockRenderer(true);
    }

    /**
     * Tests rendering an exception without a view in the development environment
     */
    public function testRenderingExceptionWithoutViewInDevelopmentEnvironment()
    {
        $ex = new Exception("foo");
        ob_start();
        $this->renderer->render($ex);
        $contents = ob_get_clean();
        $this->assertTrue($this->hasHeaderString($contents, "HTTP/1.1 500"));
        $this->assertEquals($ex->getMessage(), $this->getContentWithoutHeaders($contents));
    }

    /**
     * Tests rendering an exception without a view in the production environment
     */
    public function testRenderingExceptionWithoutViewInProductionEnvironment()
    {
        $this->renderer = new MockRenderer(false);
        $ex = new Exception("foo");
        ob_start();
        $this->renderer->render($ex);
        $contents = ob_get_clean();
        $this->assertTrue($this->hasHeaderString($contents, "HTTP/1.1 500"));
        $this->assertEquals("Something went wrong", $this->getContentWithoutHeaders($contents));
    }

    /**
     * Gets the content without the headers
     *
     * @param string $rawContents The raw contents
     * @return string The content without the headers
     */
    private function getContentWithoutHeaders($rawContents)
    {
        return preg_replace("/header::.*\\$\\$/", "", $rawContents);
    }

    /**
     * Gets whether or not a header string was set
     *
     * @param string $rawContents The raw contents
     * @param string $string The string to search for
     * @return bool Whether or not the header string exists
     */
    private function hasHeaderString($rawContents, $string)
    {
        return strpos($rawContents, "header::$string$$") !== false;
    }
}