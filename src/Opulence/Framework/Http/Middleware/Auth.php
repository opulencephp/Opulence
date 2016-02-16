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
    /** @var IAuthenticationContext The authentication context */
    private $authenticationContext = null;
    /** @var IAuthority The authority */
    private $authority = null;

    /**
     * @param IAuthenticationContext $authenticationContext The authentication context
     * @param IAuthority $authority The authority
     */
    public function __construct(IAuthenticationContext $authenticationContext, IAuthority $authority)
    {
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