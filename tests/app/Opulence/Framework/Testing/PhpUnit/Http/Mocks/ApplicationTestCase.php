<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Tests\Framework\Testing\PhpUnit\Http\Mocks;

use Monolog\Logger;
use Opulence\Applications\Application;
use Opulence\Applications\Environments\Environment;
use Opulence\Applications\Paths;
use Opulence\Applications\Tasks\Dispatchers\Dispatcher as TaskDispatcher;
use Opulence\Applications\Tasks\TaskTypes;
use Opulence\Bootstrappers\BootstrapperRegistry;
use Opulence\Bootstrappers\Dispatchers\Dispatcher;
use Opulence\Framework\Bootstrappers\Http\Requests\RequestBootstrapper;
use Opulence\Framework\Bootstrappers\Http\Routing\RouterBootstrapper;
use Opulence\Framework\Bootstrappers\Http\Views\ViewFunctionsBootstrapper;
use Opulence\Framework\Testing\PhpUnit\Http\ApplicationTestCase as BaseApplicationTestCase;
use Opulence\Ioc\Container;
use Opulence\Ioc\IContainer;
use Opulence\Tests\Applications\Mocks\MonologHandler;

/**
 * Mocks the HTTP application for use in testing
 */
class ApplicationTestCase extends BaseApplicationTestCase
{
    /** @var array The list of bootstrapper classes to include */
    private static $bootstrappers = [
        RequestBootstrapper::class,
        RouterBootstrapper::class,
        ViewFunctionsBootstrapper::class
    ];

    /**
     * @inheritdoc
     */
    protected function getGlobalMiddleware()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    protected function getKernelLogger()
    {
        $logger = new Logger("application");
        $logger->pushHandler(new MonologHandler());

        return $logger;
    }

    /**
     * @inheritdoc
     */
    protected function setApplicationAndIocContainer()
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
        $this->container->bind(IContainer::class, $this->container);
        $this->application = new Application($taskDispatcher, $environment);

        // Setup the bootstrappers
        $bootstrapperRegistry = new BootstrapperRegistry($paths, $environment);
        $bootstrapperDispatcher = new Dispatcher($taskDispatcher, $this->container);
        $bootstrapperRegistry->registerEagerBootstrapper(self::$bootstrappers);
        $taskDispatcher->registerTask(TaskTypes::PRE_START,
            function () use ($bootstrapperDispatcher, $bootstrapperRegistry) {
                $bootstrapperDispatcher->dispatch($bootstrapperRegistry);
            });
    }
}