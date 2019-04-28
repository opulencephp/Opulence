<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Tests\Console\Testing\PhpUnit\Mocks;

use Opulence\Console\Commands\CommandCollection;
use Opulence\Databases\Migrations\IMigrator;
use Opulence\Framework\Composer\Bootstrappers\ComposerBootstrapper;
use Opulence\Framework\Configuration\Config;
use Opulence\Framework\Console\Bootstrappers\CommandsBootstrapper;
use Opulence\Framework\Console\Testing\PhpUnit\Assertions\ResponseAssertions;
use Opulence\Framework\Console\Testing\PhpUnit\IntegrationTestCase as BaseIntegrationTestCase;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
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
        ComposerBootstrapper::class
    ];

    /**
     * @return CommandCollection
     */
    public function getCommandCollection(): CommandCollection
    {
        return $this->commandCollection;
    }

    /**
     * Gets the response assertions for use in testing
     *
     * @return ResponseAssertions The response assertions
     */
    public function getResponseAssertions(): ResponseAssertions
    {
        return $this->assertResponse;
    }

    /**
     * Sets up the application and container
     */
    protected function setUp(): void
    {
        Config::setCategory('paths', [
            'configs' => realpath(__DIR__ . '/../../configs'),
            'root' => realpath(__DIR__ . '/../../../../../../../..'),
            'src' => realpath(__DIR__ . '/../../../../../../../../src')
        ]);
        // Purposely set this to a weird value so we can test that it gets overwritten with the "test" environment
        $this->container = new Container();
        $this->container->bindInstance(RouteCache::class, $this->createMock(RouteCache::class));
        $this->container->bindInstance(ViewCache::class, $this->createMock(ViewCache::class));
        $this->container->bindInstance(IMigrator::class, $this->createMock(IMigrator::class));
        $this->container->bindInstance(IContainer::class, $this->container);

        // Setup the bootstrappers
        foreach (self::$bootstrappers as $bootstrapperClass) {
            /** @var Bootstrapper $bootstrapper */
            $bootstrapper = new $bootstrapperClass();
            $bootstrapper->registerBindings($this->container);
        }

        parent::setUp();
    }
}
