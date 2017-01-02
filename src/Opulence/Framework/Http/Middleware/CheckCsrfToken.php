<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Http\Middleware;

use Closure;
use Opulence\Framework\Http\CsrfTokenChecker;
use Opulence\Http\InvalidCsrfTokenException;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;
use Opulence\Routing\Middleware\IMiddleware;
use Opulence\Sessions\ISession;

/**
 * Defines the middleware that checks the CSRF token, if it was set
 */
abstract class CheckCsrfToken implements IMiddleware
{
    /** @var CsrfTokenChecker The CSRF token checker */
    protected $csrfTokenChecker = null;
    /** @var ISession The current session */
    protected $session = null;

    /**
     * @param CsrfTokenChecker $csrfTokenChecker The CSRF token checker
     * @param ISession $session The current session
     */
    public function __construct(CsrfTokenChecker $csrfTokenChecker, ISession $session)
    {
        $this->csrfTokenChecker = $csrfTokenChecker;
        $this->session = $session;
    }

    /**
     * @inheritdoc
     * @throws InvalidCsrfTokenException Thrown if the CSRF token is invalid
     */
    public function handle(Request $request, Closure $next) : Response
    {
        if (!$this->csrfTokenChecker->tokenIsValid($request, $this->session)) {
            throw new InvalidCsrfTokenException("Invalid CSRF token");
        }

        return $this->writeToResponse($next($request));
    }

    /**
     * Writes data to the response
     *
     * @param Response $response The response to write to
     * @return Response The response with data written to it
     */
    abstract protected function writeToResponse(Response $response) : Response;
}