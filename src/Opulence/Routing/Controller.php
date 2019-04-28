<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Routing;

use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;
use Opulence\Views\Compilers\ICompiler;
use Opulence\Views\Factories\IViewFactory;
use Opulence\Views\IView;

/**
 * Defines a base controller
 */
class Controller
{
    /** @var IView The view used in the response */
    protected $view;
    /** @var ICompiler The view compiler to use */
    protected $viewCompiler;
    /** @var IViewFactory The view factory to use */
    protected $viewFactory;
    /** @var Request The HTTP request */
    protected $request;

    /**
     * Actually calls the method in the controller
     * Rather than calling the method directly from the route dispatcher, call this method
     *
     * @param string $methodName The name of the method in $this to call
     * @param array $parameters The list of parameters to pass into the action method
     * @return Response The HTTP response returned by the method
     */
    public function callMethod(string $methodName, array $parameters): Response
    {
        $this->setUpView();
        /** @var Response $response */
        $response = $this->$methodName(...$parameters);

        if ($response === null || is_string($response)) {
            $response = new Response($response === null ? '' : $response);

            if ($this->viewCompiler instanceof ICompiler && $this->view !== null) {
                $response->setContent($this->viewCompiler->compile($this->view));
            }
        }

        return $response;
    }

    /**
     * @return IView|null
     */
    public function getView(): ?IView
    {
        return $this->view;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    /**
     * @param ICompiler $viewCompiler
     */
    public function setViewCompiler(ICompiler $viewCompiler): void
    {
        $this->viewCompiler = $viewCompiler;
    }

    /**
     * @param IViewFactory $viewFactory
     */
    public function setViewFactory(IViewFactory $viewFactory): void
    {
        $this->viewFactory = $viewFactory;
    }

    /**
     * Sets up the view
     * Useful for setting up a view's components that are the same across controller methods
     */
    protected function setUpView(): void
    {
        // Don't do anything
    }
}
