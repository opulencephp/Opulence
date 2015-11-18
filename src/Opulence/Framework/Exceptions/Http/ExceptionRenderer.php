<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Exceptions\Http;

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
        if ($ex instanceof HttpException) {
            $this->response = $this->getHttpExceptionResponse($ex);
        } else {
            $this->response = $this->getDefaultExceptionResponse($ex);
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
            $content = $ex->getMessage();
        } else {
            $content = "Something went wrong";
        }

        return new Response($content, $statusCode);
    }

    /**
     * Gets the response for an HTTP exception
     *
     * @param HttpException $ex The HTTP exception
     * @return Response The HTTP response
     */
    protected function getHttpExceptionResponse(HttpException $ex)
    {
        $statusCode = $ex->getStatusCode();
        $viewName = "errors/$statusCode";

        if ($this->viewFactory !== null && $this->viewCompiler !== null && $this->viewFactory->has($viewName)) {
            $content = $this->viewCompiler->compile($this->viewFactory->create($viewName));

            return new Response($content, $statusCode);
        } else {
            return $this->getDefaultExceptionResponse($ex, $statusCode);
        }
    }
}