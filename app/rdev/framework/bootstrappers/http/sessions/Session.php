<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the session bootstrapper
 */
namespace RDev\Framework\Bootstrappers\HTTP\Sessions;
use RDev\Applications\Bootstrappers\Bootstrapper;
use RDev\Files\FileSystem;
use RDev\HTTP\Requests\Request;
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
        $container->bind(ISession::class, $this->session);
        $container->bind(SessionHandlerInterface::class, $this->sessionHandler);
    }

    /**
     * {@inheritdoc}
     */
    public function run(Request $request)
    {
        $this->session->setId($request->getCookies()->get($this->session->getName()));
        $this->sessionHandler->open(/* TODO: IMPLEMENT */
            null, $this->session->getId());
        $this->session->start($this->sessionHandler->read($this->session->getId()));
    }

    /**
     * {@inheritdoc}
     */
    public function shutdown()
    {
        $this->session->ageFlashData();
        $this->sessionHandler->write($this->session->getId(), serialize($this->session->getAll()));
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