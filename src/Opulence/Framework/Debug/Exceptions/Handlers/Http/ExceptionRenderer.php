<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Debug\Exceptions\Handlers\Http;

use Opulence\Debug\Exceptions\Handlers\Http\ExceptionRenderer as BaseRenderer;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;
use Opulence\Views\Compilers\ICompiler;
use Opulence\Views\Factories\IViewFactory;

/**
 * Defines the HTTP exception handler
 */
class ExceptionRenderer extends BaseRenderer implements IExceptionRenderer
{
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
     * @inheritDoc
     */
    protected function getResponseContent($ex, $statusCode, array $headers)
    {
        $viewName = "errors/$statusCode";

        if ($this->viewFactory !== null && $this->viewCompiler !== null && $this->viewFactory->has($viewName)) {
            $view = $this->viewFactory->create($viewName);
            $view->setVar("__exception", $ex);
            $view->setVar("__inDevelopmentEnvironment", $this->inDevelopmentEnvironment);
            $content = $this->viewCompiler->compile($view);
        } else {
            $content = $this->getDefaultResponseContent($ex);
        }

        $this->response = new Response($content, $statusCode);

        foreach ($headers as $name => $values) {
            $this->response->getHeaders()->add($name, $values);
        }

        return $content;
    }
}