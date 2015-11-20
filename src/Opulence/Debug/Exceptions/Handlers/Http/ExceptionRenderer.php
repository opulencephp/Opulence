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
use Opulence\Debug\Exceptions\Handlers\IExceptionRenderer;
use Throwable;

/**
 * Defines the HTTP exception renderer
 */
class ExceptionRenderer implements IExceptionRenderer
{
    /** @var bool Whether or not we are in the development environment */
    protected $inDevelopmentEnvironment = true;

    /**
     * @param bool $inDevelopmentEnvironment Whether or not we are in the development environment
     */
    public function __construct($inDevelopmentEnvironment = false)
    {
        $this->inDevelopmentEnvironment = $inDevelopmentEnvironment;
    }

    /**
     * @inheritDoc
     */
    public function render($ex)
    {
        // Add support for HTTP library without having to necessarily depend on it
        if (get_class($ex) == "Opulence\\Http\\HttpException") {
            $statusCode = $ex->getStatusCode();
            $headers = $ex->getHeaders();
        } else {
            $statusCode = 500;
            $headers = [];
        }

        // Always get the content, even if headers are sent, so that we can unit test this
        $content = $this->getResponseContent($ex, $statusCode, $headers);

        if (!headers_sent()) {
            header("HTTP/1.1 $statusCode", true, $statusCode);

            foreach ($headers as $name => $values) {
                $values = (array)$values;

                foreach ($values as $value) {
                    header("$name:$value", false, $statusCode);
                }
            }

            echo $content;
            // To prevent any potential output buffering, let's flush
            flush();
        }
    }

    /**
     * Gets the default response content
     *
     * @param Exception $ex The exception
     * @return string The content of the response
     */
    protected function getDefaultResponseContent(Exception $ex)
    {
        if ($this->inDevelopmentEnvironment) {
            $content = $this->getDevelopmentEnvironmentContent($ex);
        } else {
            $content = $this->getProductionEnvironmentContent($ex);
        }

        return $content;
    }

    /**
     * Gets the page contents for the default production exception page
     *
     * @param Exception $ex The exception
     * @return string The contents of the page
     */
    protected function getDevelopmentEnvironmentContent(Exception $ex)
    {
        ob_start();
        require __DIR__ . "/templates/DevelopmentExceptionPage.php";

        return ob_get_clean();
    }

    /**
     * Gets the page contents for the default production exception page
     *
     * @param Exception $ex The exception
     * @return string The contents of the page
     */
    protected function getProductionEnvironmentContent(Exception $ex)
    {
        ob_start();
        require __DIR__ . "/templates/ProductionExceptionPage.php";

        return ob_get_clean();
    }

    /**
     * Gets the content for the response
     *
     * @param Throwable|Exception $ex The exception
     * @param int $statusCode The HTTP status code
     * @param array $headers The HTTP headers
     * @return string The response content
     */
    protected function getResponseContent($ex, $statusCode, array $headers)
    {
        return $this->getDefaultResponseContent($ex);
    }
}