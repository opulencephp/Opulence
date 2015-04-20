<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a base controller
 */
namespace RDev\Routing;
use RDev\HTTP\Requests\Request;
use RDev\HTTP\Responses\Response;
use RDev\Views\Compilers\ICompiler;
use RDev\Views\ITemplate;

class Controller
{
    /** @var ITemplate The template used in the response */
    protected $template = null;
    /** @var ICompiler The template compiler to use */
    protected $compiler = null;
    /** @var Request The HTTP request */
    protected $request = null;

    /**
     * Actually calls the method in the controller
     * Rather than calling the method directly from the route dispatcher, call this method
     *
     * @param string $methodName The name of the method in $this to call
     * @param array $parameters The list of parameters to pass into the action method
     * @return Response The HTTP response returned by the method
     */
    public function callMethod($methodName, array $parameters)
    {
        $this->setUpTemplate();
        /** @var Response $response */
        $response = call_user_func_array([$this, $methodName], $parameters);

        if($response === null && $this->compiler instanceof ICompiler && $this->template !== null)
        {
            $response->setContent($this->compiler->compile($this->template));
        }

        return $response;
    }

    /**
     * @return ITemplate|null
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Shows an HTTP error response
     * To customize error messages, override this method
     *
     * @param int $statusCode The HTTP status code of the error
     * @return Response The response
     */
    public function showHTTPError($statusCode)
    {
        return new Response("", $statusCode);
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