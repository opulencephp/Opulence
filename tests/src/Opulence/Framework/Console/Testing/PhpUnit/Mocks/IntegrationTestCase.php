<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Tests\Framework\Console\Testing\PhpUnit\Mocks;

use Opulence\Applications\Application;
use Opulence\Applications\Tasks\Dispatchers\Dispatcher as TaskDispatcher;
use Opulence\Applications\Tasks\TaskTypes;
use Opulence\Bootstrappers\BootstrapperRegistry;
use Opulence\Bootstrappers\Caching\ICache as BootstrapperCache;
use Opulence\Bootstrappers\Dispatchers\Dispatcher;
use Opulence\Bootstrappers\Paths;
use Opulence\Console\Commands\CommandCollection;
use Opulence\Environments\Environment;
use Opulence\Framework\Composer\Bootstrappers\ComposerBootstrapper;
use Opulence\Framework\Console\Bootstrappers\CommandsBootstrapper;
use Opulence\Framework\Console\Testing\PhpUnit\Assertions\ResponseAssertions;
use Opulence\Framework\Console\Testing\PhpUnit\IntegrationTestCase as BaseIntegrationTestCase;
use Opulence\Ioc\Container;
use Opulence\Ioc\IContainer;
use Opulence\Routing\Routes\Caching\ICache as RouteCache;
use Opulence\Views\Caching\ICache as ViewCache;

/**
 * Mocks the console integration test for use in testing
 */
class IntegrationTestCase extends BaseIntegrationTestCase
{
    /** @var array The list of bootstrapper classes to include */
    private static $bootstrappers = [
        CommandsBootstrapper::class,
        ComposerBootstrapper::class,
    ];

    /**
     * @return CommandCollection
     */
    public function getCommandCollection()
    {
        return $this->commandCollection;
    }

    /**
     * Gets the response assertions for use in testing
     *
     * @return ResponseAssertions The response assertions
     */
    public function getResponseAssertions()
    {
        return $this->assertResponse;
    }

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
        $this->environment = new Environment("foo");
        $this->container = new Container();
        $this->container->bindInstance(Paths::class, $paths);
        $this->container->bindInstance(TaskDispatcher::class, $taskDispatcher);
        $this->container->bindInstance(Environment::class, $this->environment);
        $this->container->bindInstance(BootstrapperCache::class, $this->createMock(BootstrapperCache::class));
        $this->container->bindInstance(RouteCache::class, $this->createMock(RouteCache::class));
        $this->container->bindInstance(ViewCache::class, $this->createMock(ViewCache::class));
        $this->container->bindInstance(IContainer::class, $this->container);
        $this->application = new Application($taskDispatcher);

        // Setup the bootstrappers
        $bootstrapperRegistry = new BootstrapperRegistry($paths, $this->environment);
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