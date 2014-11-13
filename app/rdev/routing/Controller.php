<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a base controller
 */
namespace RDev\Routing;
use RDev\HTTP;
use RDev\Views;
use RDev\Views\Compilers;

class Controller
{
    /** @var Views\ITemplate The template used in the response */
    protected $template = null;
    /** @var Compilers\ICompiler The template compiler to use */
    protected $compiler = null;
    /** @var HTTP\Request The HTTP request */
    protected $request = null;

    /**
     * @param HTTP\Request $request The HTTP request
     */
    public function __construct(HTTP\Request $request)
    {
        $this->request = $request;
    }

    /**
     * Actually calls the method in the controller
     * Rather than calling the method directly from the route dispatcher, call this method
     *
     * @param string $methodName The name of the method in $this to call
     * @param array $parameters The list of parameters to pass into the action method
     * @return HTTP\Response The HTTP response returned by the method
     */
    public function callMethod($methodName, array $parameters)
    {
        $this->setUpTemplate();
        /** @var HTTP\Response $response */
        $response = call_user_func_array([$this, $methodName], $parameters);

        if($response === null && $this->compiler instanceof Compilers\ICompiler && $this->template !== null)
        {
            $response->setContent($this->compiler->compile($this->template));
        }

        return $response;
    }

    /**
     * Shows an HTTP error response
     * To customize error messages, override this method
     *
     * @param int $statusCode The HTTP status code of the error
     * @return HTTP\Response The response
     */
    public function showHTTPError($statusCode)
    {
        return new HTTP\Response("", $statusCode);
    }

    /**
     * Sets up the template
     * Useful for setting up a template's components that are the same across controller methods
     */
    protected function setUpTemplate()
    {
        // Don't do anything
    }
} 