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

use Aphiria\Console\Commands\CommandRegistry;
use Aphiria\DependencyInjection\Bootstrappers\Bootstrapper;
use Aphiria\DependencyInjection\Container;
use Aphiria\DependencyInjection\IContainer;
use Opulence\Databases\Migrations\IMigrator;
use Opulence\Framework\Configuration\Config;
use Opulence\Framework\Console\Bootstrappers\CommandsBootstrapper;
use Opulence\Framework\Console\Testing\PhpUnit\Assertions\OutputAssertions;
use Opulence\Framework\Console\Testing\PhpUnit\IntegrationTestCase as BaseIntegrationTestCase;
use Opulence\Views\Caching\ICache as ViewCache;

/**
 * Mocks the console integration test for use in testing
 */
class IntegrationTestCase extends BaseIntegrationTestCase
{
    /** @var array The list of bootstrapper classes to include */
    private static array $bootstrappers = [
        CommandsBootstrapper::class
    ];

    /**
     * @return CommandRegistry
     */
    public function getCommands(): CommandRegistry
    {
        return $this->commands;
    }

    /**
     * @return IContainer
     */
    public function getContainer(): IContainer
    {
        return $this->container;
    }

    /**
     * Gets the output assertions for use in testing
     *
     * @return OutputAssertions The output assertions
     */
    public function getOutputAssertions(): OutputAssertions
    {
        return $this->assertOutput;
    }

    protected function setUp(): void
    {
        Config::setCategory('paths', [
            'root' => realpath(__DIR__ . '/../../../../..'),
            'src' => realpath(__DIR__ . '/../../../../../src')
        ]);
        // Purposely set this to a weird value so we can test that it gets overwritten with the "test" environment
        $this->container = new Container();
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
