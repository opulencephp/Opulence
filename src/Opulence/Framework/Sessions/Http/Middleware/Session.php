<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Framework\Sessions\Http\Middleware;

use Closure;
use Opulence\Http\Requests\Request;
use Opulence\Http\Requests\RequestMethods;
use Opulence\Http\Responses\Response;
use Opulence\Routing\Middleware\IMiddleware;
use Opulence\Sessions\ISession;
use SessionHandlerInterface;

/**
 * Defines the session middleware
 */
abstract class Session implements IMiddleware
{
    /** The key of the previous URL */
    const PREVIOUS_URL_KEY = '__opulence_previous_url';

    /** @var ISession The session used by the application */
    protected $session = null;
    /** @var SessionHandlerInterface The session handler used by the application */
    protected $sessionHandler = null;
    /** @var string */
    protected $savePath = '';

    /**
     * @param ISession $session The session used by the application
     * @param SessionHandlerInterface $sessionHandler The session handler used by the application
     */
    public function __construct(ISession $session, SessionHandlerInterface $sessionHandler)
    {
        $this->session = $session;
        $this->sessionHandler = $sessionHandler;
    }

    /**
     * @param string $savePath
     */
    public function setSavePath(string $savePath) : void
    {
        $this->savePath = $savePath;
    }

    /**
     * @inheritdoc
     */
    public function handle(Request $request, Closure $next) : Response
    {
        $this->startSession($request);

        // Set the previous URL in the request
        if ($previousUrl = $this->session->get(self::PREVIOUS_URL_KEY) !== null) {
            $request->setPreviousUrl($previousUrl);
        }

        $response = $next($request);

        // Store the current URL for next time
        if ($request->getMethod() === RequestMethods::GET && !$request->isAjax()) {
            $request->setPreviousUrl($request->getFullUrl());
        }

        $this->writeSession($response);

        return $response;
    }

    /**
     * Runs garbage collection, if necessary
     */
    abstract protected function gc();

    /**
     * Writes any session data needed in the response
     *
     * @param Response $response The response to write to
     * @return Response The response with data written to it
     */
    abstract protected function writeToResponse(Response $response) : Response;

    /**
     * Starts the session
     *
     * @param Request $request The current request
     */
    protected function startSession(Request $request)
    {
        $this->gc();
        $this->session->setId($request->getCookies()->get($this->session->getName()));
        $this->sessionHandler->open($this->savePath, $this->session->getName());
        $sessionVars = @unserialize($this->sessionHandler->read($this->session->getId()));

        if ($sessionVars === false) {
            $sessionVars = [];
        }

        $this->session->start($sessionVars);
    }

    /**
     * Writes the session data to the response
     *
     * @param Response $response The response
     */
    protected function writeSession(Response $response)
    {
        $this->session->ageFlashData();
        $this->sessionHandler->write($this->session->getId(), serialize($this->session->getAll()));
        $this->writeToResponse($response);
    }
}
