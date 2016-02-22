<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Http\Middleware;

use Closure;
use Opulence\Authentication\Credentials\Authenticators\IAuthenticator;
use Opulence\Authentication\IAuthenticationContext;
use Opulence\Authorization\IAuthority;
use Opulence\Http\Middleware\IMiddleware;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;

/**
 * Defines the authentication and authorization middleware
 */
class Auth implements IMiddleware
{
    /** @var IAuthenticator The authenticator */
    protected $authenticator = null;
    /** @var IAuthenticationContext The authentication context */
    protected $authenticationContext = null;
    /** @var IAuthority The authority */
    protected $authority = null;

    /**
     * @param IAuthenticator $authenticator The authenticator
     * @param IAuthenticationContext $authenticationContext The authentication context
     * @param IAuthority $authority The authority
     */
    public function __construct(
        IAuthenticator $authenticator,
        IAuthenticationContext $authenticationContext,
        IAuthority $authority
    ) {
        $this->authenticator = $authenticator;
        $this->authenticationContext = $authenticationContext;
        $this->authority = $authority;
    }

    /**
     * @inheritdoc
     */
    public function handle(Request $request, Closure $next) : Response
    {
        /**
         * Todo:  Set authenticator, user, and user Id, eg:
         *
         * if($this->authenticator->authenticate([$request->getInput("token")], $user)
         * {
         *      $this->authenticationContext->setUser($user);
         *      $this->authority->setUserId($user->getId());
         * }
         */
        return $next($request);
    }
}