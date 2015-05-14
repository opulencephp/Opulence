<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the session middleware
 */
namespace RDev\Framework\HTTP\Middleware;
use Closure;
use DateTime;
use RDev\HTTP\Middleware\IMiddleware;
use RDev\HTTP\Requests\Request;
use RDev\HTTP\Responses\Cookie;
use RDev\HTTP\Responses\Response;
use RDev\Sessions\ISession;
use SessionHandlerInterface;

class Session implements IMiddleware
{
    /** @var ISession The session used by the application */
    protected $session = null;
    /** @var SessionHandlerInterface The session handler used by the application */
    protected $sessionHandler = null;

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
     * {@inheritdoc}
     */
    public function handle(Request $request, Closure $next)
    {
        $this->startSession($request);
        $response = $next($request);
        $this->writeSession($response);
    }

    /**
     * Starts the session
     *
     * @param Request $request The current request
     */
    protected function startSession(Request $request)
    {
        $this->session->setId($request->getCookies()->get($this->session->getName()));
        $this->sessionHandler->open(/* TODO: IMPLEMENT */
            null, $this->session->getId());
        $this->session->start($this->sessionHandler->read($this->session->getId()));
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
        $sessionCookie = new Cookie(
            $this->session->getName(),
            $this->session->getId(),
            /* TODO: IMPLEMENT */
            new DateTime(),
            /* TODO: IMPLEMENT */
            "",
            /* TODO: IMPLEMENT */
            "",
            /* TODO: IMPLEMENT */
            false
        );
        $response->getHeaders()->setCookie($sessionCookie);
    }
}