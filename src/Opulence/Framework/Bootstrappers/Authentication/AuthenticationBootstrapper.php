<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Bootstrappers\Authentication;

use Opulence\Authentication\AuthenticationContext;
use Opulence\Authentication\Credentials\Authenticators\Authenticator;
use Opulence\Authentication\Credentials\Authenticators\AuthenticatorRegistry;
use Opulence\Authentication\Credentials\Authenticators\IAuthenticator;
use Opulence\Authentication\Credentials\Authenticators\IAuthenticatorRegistry;
use Opulence\Authentication\Users\Orm\IUserRepository;
use Opulence\Authentication\IAuthenticationContext;
use Opulence\Bootstrappers\Bootstrapper;
use Opulence\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;

/**
 * Defines the authentication bootstrapper
 */
abstract class AuthenticationBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @inheritdoc
     */
    public function getBindings() : array
    {
        return [
            IAuthenticatorRegistry::class,
            IAuthenticator::class,
            IUserRepository::class,
            IAuthenticationContext::class
        ];
    }

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        // Create the components
        $authenticationContext = $this->getAuthenticationContext($container);
        $authenticator = $this->getAuthenticator($container);
        $userRepository = $this->getUserRepository($container);

        // Bind to the container
        $container->bind(IAuthenticationContext::class, $authenticationContext);
        $container->bind(IAuthenticator::class, $authenticator);
        $container->bind(IUserRepository::class, $userRepository);
    }

    /**
     * Gets the user repository
     *
     * @param IContainer $container The IoC container
     * @return IUserRepository The user repository
     */
    abstract protected function getUserRepository(IContainer $container) : IUserRepository;

    /**
     * Gets the authentication context
     *
     * @param IContainer $container The IoC container
     * @return IAuthenticationContext The authentication context
     */
    protected function getAuthenticationContext(IContainer $container) : IAuthenticationContext
    {
        return new AuthenticationContext();
    }

    /**
     * Gets the authenticator
     *
     * @param IContainer $container The IoC container
     * @return IAuthenticator The authenticator
     */
    protected function getAuthenticator(IContainer $container) : IAuthenticator
    {
        $authenticatorRegistry = new AuthenticatorRegistry();
        $authenticator = new Authenticator($authenticatorRegistry);
        $container->bind(IAuthenticatorRegistry::class, $authenticatorRegistry);

        return $authenticator;
    }
}