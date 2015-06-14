<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the middleware that checks the CSRF token, if it was set
 */
namespace RDev\Framework\HTTP\Middleware;
use Closure;
use RDev\Applications\Paths;
use RDev\Framework\HTTP\CSRFTokenChecker;
use RDev\HTTP\InvalidCSRFTokenException;
use RDev\HTTP\Middleware\IMiddleware;
use RDev\HTTP\Requests\Request;
use RDev\HTTP\Responses\Response;
use RDev\Sessions\ISession;

abstract class CheckCSRFToken implements IMiddleware
{
    /** @var Paths The application paths */
    protected $paths = null;
    /** @var CSRFTokenChecker The CSRF token checker */
    protected $csrfTokenChecker = null;
    /** @var ISession The current session */
    protected $session = null;

    /**
     * @param Paths $paths The application paths
     * @param CSRFTokenChecker $csrfTokenChecker The CSRF token checker
     * @param ISession $session The current session
     */
    public function __construct(Paths $paths, CSRFTokenChecker $csrfTokenChecker, ISession $session)
    {
        $this->paths = $paths;
        $this->csrfTokenChecker = $csrfTokenChecker;
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     * @throws InvalidCSRFTokenException Thrown if the CSRF token is invalid
     */
    public function handle(Request $request, Closure $next)
    {
        if(!$this->csrfTokenChecker->tokenIsValid($request, $this->session))
        {
            throw new InvalidCSRFTokenException("Invalid CSRF token");
        }

        return $this->writeToResponse($next($request));
    }

    /**
     * Writes data to the response
     *
     * @param Response $response The response to write to
     */
    abstract protected function writeToResponse(Response $response);
}