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
    /** The default path */
    const DEFAULT_PATH = "/tmp";
    /** @var ISession The session used by the application */
    protected $session = null;
    /** @var SessionHandlerInterface The session handler interface used by the application */
    protected $sessionHandler = null;

    /**
     * {@inheritdoc}
     */
    public function registerBindings(IContainer $container)
    {
        $this->session = $this->getSession();
        $this->sessionHandler = $this->getSessionHandler();
        $container->bind("RDev\\Sessions\\ISession", $this->session);
        $container->bind("\\SessionHandlerInterface", $this->sessionHandler);
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        // TODO:  Implement creating of session
    }

    /**
     * {@inheritdoc}
     */
    public function shutdown()
    {
        // TODO:  Implement shutting down of session
    }

    /**
     * Gets the session object to use
     *
     * @return ISession The session to use
     */
    protected function getSession()
    {
        return new RDevSession();
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