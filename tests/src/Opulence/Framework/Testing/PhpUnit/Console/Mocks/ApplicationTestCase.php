<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Tests\Framework\Testing\PhpUnit\Console\Mocks;

use Opulence\Applications\Application;
use Opulence\Applications\Environments\Environment;
use Opulence\Applications\Tasks\Dispatchers\Dispatcher as TaskDispatcher;
use Opulence\Applications\Tasks\TaskTypes;
use Opulence\Bootstrappers\BootstrapperRegistry;
use Opulence\Bootstrappers\Caching\ICache as BootstrapperCache;
use Opulence\Bootstrappers\Dispatchers\Dispatcher;
use Opulence\Bootstrappers\Paths;
use Opulence\Framework\Bootstrappers\Console\Commands\CommandsBootstrapper;
use Opulence\Framework\Bootstrappers\Console\Composer\ComposerBootstrapper;
use Opulence\Framework\Testing\PhpUnit\Console\ApplicationTestCase as BaseApplicationTestCase;
use Opulence\Ioc\Container;
use Opulence\Ioc\IContainer;
use Opulence\Routing\Routes\Caching\ICache as RouteCache;
use Opulence\Views\Caching\ICache as ViewCache;

/**
 * Mocks the console application for use in testing
 */
class ApplicationTestCase extends BaseApplicationTestCase
{
    /** @var array The list of bootstrapper classes to include */
    private static $bootstrappers = [
        CommandsBootstrapper::class,
        ComposerBootstrapper::class,
    ];

    /**
     * Sets up the application and container
     */
    public function setUp()
    {
        // Create and bind all of the components of our application
        $paths = new Paths([
            "configs" => __DIR__ . "/../../configs"
        ]);
        $taskDispatcher = new TaskDispatcher();
        // Purposely set this to a weird value so we can test that it gets overwritten with the "test" environment
        $environment = new Environment("foo");
        $this->container = new Container();
        $this->container->bind(Paths::class, $paths);
        $this->container->bind(TaskDispatcher::class, $taskDispatcher);
        $this->container->bind(Environment::class, $environment);
        $this->container->bind(BootstrapperCache::class, $this->getMock(BootstrapperCache::class));
        $this->container->bind(RouteCache::class, $this->getMock(RouteCache::class));
        $this->container->bind(ViewCache::class, $this->getMock(ViewCache::class));
        $this->container->bind(IContainer::class, $this->container);
        $this->application = new Application($taskDispatcher, $environment);

        // Setup the bootstrappers
        $bootstrapperRegistry = new BootstrapperRegistry($paths, $environment);
        $bootstrapperDispatcher = new Dispatcher($taskDispatcher, $this->container);
        $bootstrapperRegistry->registerEagerBootstrapper(self::$bootstrappers);
        $taskDispatcher->registerTask(
            TaskTypes::PRE_START,
            function () use ($bootstrapperDispatcher, $bootstrapperRegistry) {
                $bootstrapperDispatcher->dispatch($bootstrapperRegistry);
            }
        );

        parent::setUp();
    }
}