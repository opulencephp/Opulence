<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
/**
 * Defines the session bootstrapper
 */
namespace Opulence\Framework\Bootstrappers\Http\Sessions;

use Opulence\Bootstrappers\Bootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Sessions\ISession;
use SessionHandlerInterface;

abstract class SessionBootstrapper extends Bootstrapper
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