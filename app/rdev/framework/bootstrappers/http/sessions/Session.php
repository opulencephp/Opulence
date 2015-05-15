<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the session bootstrapper
 */
namespace RDev\Framework\Bootstrappers\HTTP\Sessions;
use RDev\Applications\Bootstrappers\Bootstrapper;
use RDev\IoC\IContainer;
use RDev\Sessions\ISession;
use SessionHandlerInterface;

abstract class Session extends Bootstrapper
{
    /**
     * {@inheritdoc}
     */
    public function registerBindings(IContainer $container)
    {
        $container->bind(ISession::class, $this->getSession());
        $container->bind(SessionHandlerInterface::class, $this->getSessionHandler());
    }

    /**
     * Gets the session object to use
     *
     * @return ISession The session to use
     */
    abstract protected function getSession();

    /**
     * Gets the session handler object to use
     *
     * @return SessionHandlerInterface The session handler to use
     */
    abstract protected function getSessionHandler();
}