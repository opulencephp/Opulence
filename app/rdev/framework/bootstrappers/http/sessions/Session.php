<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the session bootstrapper
 */
namespace RDev\Framework\Bootstrappers\HTTP\Sessions;
use RDev\Applications\Bootstrappers\Bootstrapper;
use RDev\Files\FileSystem;
use RDev\IoC\IContainer;
use RDev\Sessions\Handlers\FileSessionHandler;
use RDev\Sessions\ISession;
use RDev\Sessions\Session as RDevSession;
use SessionHandlerInterface;

class Session extends Bootstrapper
{
    /** The default session cookie name */
    const DEFAULT_COOKIE_NAME = "__rdev_session";
    /** The default path */
    const DEFAULT_PATH = "/tmp";

    /**
     * {@inheritdoc}
     */
    public function registerBindings(IContainer $container)
    {
        $session = $this->getSession();
        $sessionHandler = $this->getSessionHandler();
        $container->bind(ISession::class, $session);
        $container->bind(SessionHandlerInterface::class, $sessionHandler);
    }

    /**
     * Gets the session object to use
     *
     * @return ISession The session to use
     */
    protected function getSession()
    {
        $session = new RDevSession();
        $session->setName(self::DEFAULT_COOKIE_NAME);

        return $session;
    }

    /**
     * Gets the session handler object to use
     *
     * @return SessionHandlerInterface The session handler to use
     */
    protected function getSessionHandler()
    {
        return new FileSessionHandler(new FileSystem(), self::DEFAULT_PATH);
    }
}