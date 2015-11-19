<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Debug\Exceptions\Handlers\Http;

use Exception;
use Opulence\Applications\Environments\Environment;
use Opulence\Http\HttpException;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;
use Opulence\Views\Compilers\ICompiler;
use Opulence\Views\Factories\IViewFactory;

/**
 * Defines the HTTP exception handler
 */
class ExceptionRenderer implements IHttpExceptionRenderer
{
    /** @var Environment The current environment */
    protected $environment = null;
    /** @var Request The current HTTP request */
    protected $request = null;
    /** @var Response The last HTTP response */
    protected $response = null;
    /** @var ICompiler|null The view compiler */
    protected $viewCompiler = null;
    /** @var IViewFactory|null The view factory */
    protected $viewFactory = null;

    /**
     * @inheritdoc
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @inheritDoc
     */
    public function render($ex)
    {
        $statusCode = $ex instanceof HttpException ? $ex->getStatusCode() : 500;
        $viewName = "errors/$statusCode";

        if ($this->viewFactory !== null && $this->viewCompiler !== null && $this->viewFactory->has($viewName)) {
            $view = $this->viewFactory->create($viewName);
            $view->setVar("__exception", $ex);
            $view->setVar("__environment", $this->environment);
            $content = $this->viewCompiler->compile($view);
            $this->response = new Response($content, $statusCode);
        } else {
            $this->response = $this->getDefaultExceptionResponse($ex, $statusCode);
        }
    }

    /**
     * @inheritDoc
     */
    public function setEnvironment(Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @inheritDoc
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @inheritdoc
     */
    public function setViewCompiler(ICompiler $viewCompiler)
    {
        $this->viewCompiler = $viewCompiler;
    }

    /**
     * @inheritdoc
     */
    public function setViewFactory(IViewFactory $viewFactory)
    {
        $this->viewFactory = $viewFactory;
    }

    /**
     * Gets the default response, which is useful when no custom views have been defined for the exception
     *
     * @param Exception $ex The exception
     * @param int $statusCode The status code
     * @return Response The response
     */
    protected function getDefaultExceptionResponse(Exception $ex, $statusCode = 500)
    {
        if ($this->environment->getName() === Environment::DEVELOPMENT) {
            $content = $this->getDevelopmentExceptionPage($ex);
        } else {
            $content = $this->getProductionExceptionPage($ex);
        }

        return new Response($content, $statusCode);
    }

    /**
     * Gets the page contents for the default production exception page
     *
     * @param Exception $ex The exception
     * @return string The contents of the page
     */
    protected function getDevelopmentExceptionPage(Exception $ex)
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
    protected function getProductionExceptionPage(Exception $ex)
    {
        ob_start();
        require __DIR__ . "/templates/ProductionExceptionPage.php";

        return ob_get_clean();
    }
}