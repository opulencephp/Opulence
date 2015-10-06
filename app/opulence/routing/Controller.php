<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a base controller
 */
namespace Opulence\Routing;

use Opulence\HTTP\Requests\Request;
use Opulence\HTTP\Responses\Response;
use Opulence\Views\Compilers\ICompiler;
use Opulence\Views\Factories\IViewFactory;
use Opulence\Views\IView;

class Controller
{
    /** @var IView The view used in the response */
    protected $view = null;
    /** @var ICompiler The view compiler to use */
    protected $viewCompiler = null;
    /** @var IViewFactory The view factory to use */
    protected $viewFactory = null;
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
        $this->setUpView();
        /** @var Response $response */
        $response = call_user_func_array([$this, $methodName], $parameters);

        if ($response === null && $this->viewCompiler instanceof ICompiler && $this->view !== null) {
            $response->setContent($this->viewCompiler->compile($this->view));
        }

        return $response;
    }

    /**
     * @return IView|null
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param ICompiler $viewCompiler
     */
    public function setViewCompiler(ICompiler $viewCompiler)
    {
        $this->viewCompiler = $viewCompiler;
    }

    /**
     * @param IViewFactory $viewFactory
     */
    public function setViewFactory(IVIewFactory $viewFactory)
    {
        $this->viewFactory = $viewFactory;
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
     * Sets up the view
     * Useful for setting up a view's components that are the same across controller methods
     */
    protected function setUpView()
    {
        // Don't do anything
    }
} 