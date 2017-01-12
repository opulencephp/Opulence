<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Debug\Exceptions\Handlers\Http;

use Exception;
use Opulence\Debug\Exceptions\Handlers\IExceptionRenderer;
use Opulence\Http\HttpException;
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
    public function __construct(bool $inDevelopmentEnvironment = false)
    {
        $this->inDevelopmentEnvironment = $inDevelopmentEnvironment;
    }

    /**
     * @inheritdoc
     */
    public function render($ex)
    {
        // Add support for HTTP library without having to necessarily depend on it
        if (get_class($ex) === HttpException::class) {
            /** @var HttpException $ex */
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

            switch ($this->getRequestFormat()) {
                case 'json':
                    $headers['Content-Type'] = 'application/json';
                    break;
                default:
                    $headers['Content-Type'] = 'text/html';
            }

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
     * @param int $statusCode The HTTP status code
     * @return string The content of the response
     */
    protected function getDefaultResponseContent(Exception $ex, int $statusCode) : string
    {
        if ($this->inDevelopmentEnvironment) {
            $content = $this->getDevelopmentEnvironmentContent($ex, $statusCode);
        } else {
            $content = $this->getProductionEnvironmentContent($ex, $statusCode);
        }

        return $content;
    }

    /**
     * Gets the page contents for the default production exception page
     *
     * @param Exception $ex The exception
     * @param int $statusCode The HTTP status code
     * @return string The contents of the page
     */
    protected function getDevelopmentEnvironmentContent(Exception $ex, int $statusCode) : string
    {
        ob_start();

        if ($statusCode === 503) {
            require __DIR__ . "/templates/{$this->getRequestFormat()}/MaintenanceMode.php";
        } else {
            require __DIR__ . "/templates/{$this->getRequestFormat()}/DevelopmentException.php";
        }

        return ob_get_clean();
    }

    /**
     * Gets the page contents for the default production exception page
     *
     * @param Exception $ex The exception
     * @param int $statusCode The HTTP status code
     * @return string The contents of the page
     */
    protected function getProductionEnvironmentContent(Exception $ex, int $statusCode) : string
    {
        ob_start();

        if ($statusCode === 503) {
            require __DIR__ . "/templates/{$this->getRequestFormat()}/MaintenanceMode.php";
        } else {
            require __DIR__ . "/templates/{$this->getRequestFormat()}/ProductionException.php";
        }

        return ob_get_clean();
    }

    /**
     * Gets the request format
     *
     * @return string The request format, eg "html" (default), "json"
     */
    protected function getRequestFormat() : string
    {
        if (isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] === 'application/json') {
            return 'json';
        } else {
            return 'html';
        }
    }

    /**
     * Gets the content for the response
     *
     * @param Throwable|Exception $ex The exception
     * @param int $statusCode The HTTP status code
     * @param array $headers The HTTP headers
     * @return string The response content
     */
    protected function getResponseContent($ex, int $statusCode, array $headers) : string
    {
        return $this->getDefaultResponseContent($ex, $statusCode);
    }
}
