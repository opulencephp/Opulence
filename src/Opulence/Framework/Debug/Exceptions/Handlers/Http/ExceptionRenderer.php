<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Debug\Exceptions\Handlers\Http;

use Exception;
use LogicException;
use Opulence\Debug\Exceptions\Handlers\Http\ExceptionRenderer as BaseRenderer;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\JsonResponse;
use Opulence\Http\Responses\Response;
use Opulence\Views\Compilers\ICompiler;
use Opulence\Views\Factories\IViewFactory;
use Throwable;

/**
 * Defines the HTTP exception handler
 */
class ExceptionRenderer extends BaseRenderer implements IExceptionRenderer
{
    /** @var Request The current HTTP request */
    protected $request;
    /** @var Response The last HTTP response */
    protected $response;
    /** @var ICompiler|null The view compiler */
    protected $viewCompiler;
    /** @var IViewFactory|null The view factory */
    protected $viewFactory;

    /**
     * @inheritdoc
     */
    public function getResponse(): Response
    {
        if ($this->response === null) {
            throw new LogicException('Response not set yet');
        }

        return $this->response;
    }

    /**
     * @inheritdoc
     */
    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    /**
     * @inheritdoc
     */
    public function setViewCompiler(ICompiler $viewCompiler): void
    {
        $this->viewCompiler = $viewCompiler;
    }

    /**
     * @inheritdoc
     */
    public function setViewFactory(IViewFactory $viewFactory): void
    {
        $this->viewFactory = $viewFactory;
    }

    /**
     * @inheritdoc
     */
    protected function getRequestFormat(): string
    {
        if ($this->request === null) {
            return 'html';
        }

        if ($this->request->isJson()) {
            return 'json';
        } else {
            return 'html';
        }
    }

    /**
     * @inheritdoc
     */
    protected function getResponseContent($ex, int $statusCode, array $headers): string
    {
        $viewName = $this->getViewName($ex, $statusCode, $headers);

        if ($this->viewFactory !== null && $this->viewCompiler !== null && $this->viewFactory->hasView($viewName)) {
            $view = $this->viewFactory->createView($viewName);
            $view->setVar('__exception', $ex);
            $view->setVar('__inDevelopmentEnvironment', $this->inDevelopmentEnvironment);
            $content = $this->viewCompiler->compile($view);
        } else {
            $content = $this->getDefaultResponseContent($ex, $statusCode);
        }

        switch ($this->getRequestFormat()) {
            case 'json':
                // The response will be JSON-encoded, but JsonResponse requires a decoded array
                $this->response = new JsonResponse(json_decode($content, true), $statusCode);
                break;
            default:
                $this->response = new Response($content, $statusCode);
        }

        foreach ($headers as $name => $values) {
            $this->response->getHeaders()->add($name, $values);
        }

        return $content;
    }

    /**
     * Gets the name of the view file for the input exception and status code
     *
     * @param Throwable|Exception $ex The throwable
     * @param int $statusCode The status code
     * @param array $headers The headers for the exception
     * @return string The view name
     */
    protected function getViewName($ex, int $statusCode, array $headers): string
    {
        return "errors/{$this->getRequestFormat()}/$statusCode";
    }
}
