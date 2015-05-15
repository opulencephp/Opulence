<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the session middleware
 */
namespace RDev\Framework\HTTP\Middleware;
use Closure;
use RDev\Applications\Paths;
use RDev\HTTP\Middleware\IMiddleware;
use RDev\HTTP\Requests\Request;
use RDev\HTTP\Responses\Response;
use RDev\Sessions\ISession;
use SessionHandlerInterface;

abstract class Session implements IMiddleware
{
    /** @var Paths The application paths */
    protected $paths = null;
    /** @var ISession The session used by the application */
    protected $session = null;
    /** @var SessionHandlerInterface The session handler used by the application */
    protected $sessionHandler = null;

    /**
     * @param Paths $paths The application paths
     * @param ISession $session The session used by the application
     * @param SessionHandlerInterface $sessionHandler The session handler used by the application
     */
    public function __construct(Paths $paths, ISession $session, SessionHandlerInterface $sessionHandler)
    {
        $this->paths = $paths;
        $this->session = $session;
        $this->sessionHandler = $sessionHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, Closure $next)
    {
        $this->startSession($request);
        $response = $next($request);
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
     */
    abstract protected function writeToResponse(Response $response);

    /**
     * Starts the session
     *
     * @param Request $request The current request
     */
    protected function startSession(Request $request)
    {
        $this->gc();
        $this->session->setId($request->getCookies()->get($this->session->getName()));
        $this->sessionHandler->open(null, $this->session->getName());
        $sessionVariables = @unserialize($this->sessionHandler->read($this->session->getId()));

        if($sessionVariables === false)
        {
            $sessionVariables = [];
        }

        $this->session->start($sessionVariables);
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