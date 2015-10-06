<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the middleware that checks the CSRF token, if it was set
 */
namespace Opulence\Framework\HTTP\Middleware;

use Closure;
use Opulence\Applications\Paths;
use Opulence\Framework\HTTP\CSRFTokenChecker;
use Opulence\HTTP\InvalidCSRFTokenException;
use Opulence\HTTP\Middleware\IMiddleware;
use Opulence\HTTP\Requests\Request;
use Opulence\HTTP\Responses\Response;
use Opulence\Sessions\ISession;

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
     * @inheritdoc
     * @throws InvalidCSRFTokenException Thrown if the CSRF token is invalid
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$this->csrfTokenChecker->tokenIsValid($request, $this->session)) {
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