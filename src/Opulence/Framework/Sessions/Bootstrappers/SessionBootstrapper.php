<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Sessions\Bootstrappers;

use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Sessions\Handlers\ISessionEncrypter;
use Opulence\Sessions\Handlers\SessionEncrypter;
use Opulence\Sessions\ISession;
use SessionHandlerInterface;

/**
 * Defines the session bootstrapper
 */
abstract class SessionBootstrapper extends Bootstrapper
{
    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        $container->bindInstance(ISession::class, $this->getSession($container));
        $container->bindInstance(SessionHandlerInterface::class, $this->getSessionHandler($container));
    }

    /**
     * Gets the session object to use
     *
     * @param IContainer $container The IoC Container
     * @return ISession The session to use
     */
    abstract protected function getSession(IContainer $container) : ISession;

    /**
     * Gets the session handler object to use
     *
     * @param IContainer $container The IoC Container
     * @return SessionHandlerInterface The session handler to use
     */
    abstract protected function getSessionHandler(IContainer $container) : SessionHandlerInterface;

    /**
     * Gets the session encrypter to use if our sessions are encrypted
     *
     * @param IContainer $container The IoC Container
     * @return ISessionEncrypter The session encrypter to use
     */
    protected function getSessionEncrypter(IContainer $container) : ISessionEncrypter
    {
        return new SessionEncrypter($container->resolve($container));
    }
}
