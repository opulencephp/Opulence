<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a base controller
 */
namespace RDev\Controllers;
use RDev\Models\HTTP;
use RDev\Views;

abstract class Controller
{
    /** @var Views\IView The view used in the response */
    protected $view = null;

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
        $this->setUpView();
        /** @var HTTP\Response $response */
        $response = call_user_func_array([$this, $methodName], $parameters);

        if($response === null && $this->view !== null)
        {
            $response->setContent($this->view->render());
        }

        return $response;
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