<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the session bootstrapper
 */
namespace Opulence\Framework\Bootstrappers\HTTP\Sessions;
use Opulence\Applications\Bootstrappers\Bootstrapper;
use Opulence\IoC\IContainer;
use Opulence\Sessions\ISession;
use SessionHandlerInterface;

abstract class Session extends Bootstrapper
{
    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        $container->bind(ISession::class, $this->getSession($container));
        $container->bind(SessionHandlerInterface::class, $this->getSessionHandler($container));
    }

    /**
     * Gets the session object to use
     *
     * @param IContainer $container The IoC Container
     * @return ISession The session to use
     */
    abstract protected function getSession(IContainer $container);

    /**
     * Gets the session handler object to use
     *
     * @param IContainer $container The IoC Container
     * @return SessionHandlerInterface The session handler to use
     */
    abstract protected function getSessionHandler(IContainer $container);
}