<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Http;

use Exception;
use Monolog\Logger;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;
use Opulence\Http\Responses\ResponseHeaders;
use Opulence\Ioc\IContainer;
use Opulence\Pipelines\Pipeline;
use Opulence\Routing\Router;

/**
 * Defines the HTTP kernel
 */
class Kernel
{
    /** @var IContainer The dependency injection container */
    private $container = null;
    /** @var Router The router to use for requests */
    private $router = null;
    /** @var Logger The logger to use */
    private $logger = null;
    /** @var array The list of global middleware */
    private $middleware = [];
    /** @var bool Whether or not all middleware are disabled */
    private $middlewareAreDisabled = false;
    /** @var array The list of enabled middleware */
    private $enabledMiddleware = [];
    /** @var array The list of disabled middleware */
    private $disabledMiddleware = [];

    /**
     * @param IContainer $container The dependency injection container
     * @param Router $router The router to use
     * @param Logger $logger The logger to use
     */
    public function __construct(IContainer $container, Router $router, Logger $logger)
    {
        $this->container = $container;
        $this->router = $router;
        $this->logger = $logger;
    }

    /**
     * Adds middleware to the kernel
     *
     * @param string|array $middleware The middleware class or list of middleware classes to add
     */
    public function addMiddleware($middleware)
    {
        $this->middleware = array_merge($this->middleware, (array)$middleware);
    }

    /**
     * Disables all middleware
     */
    public function disableAllMiddleware()
    {
        $this->middlewareAreDisabled = true;
    }

    /**
     * @return array
     */
    public function getMiddleware()
    {
        if ($this->middlewareAreDisabled) {
            return [];
        }

        if (count($this->enabledMiddleware) > 0) {
            return $this->enabledMiddleware;
        }

        if (count($this->disabledMiddleware) > 0) {
            return array_values(array_diff($this->middleware, $this->disabledMiddleware));
        }

        return $this->middleware;
    }

    /**
     * Handles an HTTP request
     *
     * @param Request $request The HTTP request to handle
     * @return Response The HTTP response
     */
    public function handle(Request $request)
    {
        try {
            $pipeline = new Pipeline($this->container, $this->getMiddleware(), "handle");

            return $pipeline->send($request, function ($request) {
                return $this->router->route($request);
            });
        } catch (Exception $ex) {
            $this->logger->addError("Failed to handle request: $ex");

            return new Response("", ResponseHeaders::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Disables the argument middleware
     *
     * @param array $middleware The list of middleware classes to disable
     */
    public function onlyDisableMiddleware(array $middleware)
    {
        $this->disabledMiddleware = $middleware;
    }

    /**
     * Enables only the argument middleware
     *
     * @param array $middleware The list of middleware classes to enable
     */
    public function onlyEnableMiddleware(array $middleware)
    {
        $this->enabledMiddleware = $middleware;
    }
}