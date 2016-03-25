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
use Opulence\Authentication\Credentials\Credential;
use Opulence\Authentication\Credentials\CredentialTypes;
use Opulence\Authentication\Credentials\ICredential;
use Opulence\Authentication\IAuthenticationContext;
use Opulence\Authentication\ISubject;
use Opulence\Authorization\IAuthority;
use Opulence\Http\HttpException;
use Opulence\Http\Middleware\IMiddleware;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;

/**
 * Defines the authentication and authorization middleware
 */
class Authenticate implements IMiddleware
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
        $credential = $this->getCredential($request);

        if (!$this->authenticator->authenticate($credential, $subject)) {
            throw new HttpException(403);
        }

        /** @var ISubject $subject */
        $this->authenticationContext->setSubject($subject);
        $this->authority->setSubject($subject->getPrimaryPrincipal()->getId(), $subject->getRoles());

        return $next($request);
    }

    /**
     * @inheritdoc
     */
    protected function getCredential(Request $request) : ICredential
    {
        $values = ["token" => $request->getInput("access-token")];

        return new Credential(CredentialTypes::JWT_ACCESS_TOKEN, $values);
    }
}