<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Databases\Migrations;

use Aphiria\DependencyInjection\IContainer;
use Aphiria\DependencyInjection\ResolutionException;
use Opulence\Databases\Migrations\IMigration;
use Opulence\Databases\Migrations\IMigrationResolver;
use Opulence\Databases\Migrations\MigrationResolutionException;

/**
 * Defines the container migration resolver
 */
final class ContainerMigrationResolver implements IMigrationResolver
{
    /** @var IContainer The DI container */
    private IContainer $container;

    /**
     * @param IContainer $container The DI container
     */
    public function __construct(IContainer $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritdoc
     */
    public function resolve(string $migrationClassName): IMigration
    {
        try {
            return $this->container->resolve($migrationClassName);
        } catch (ResolutionException $ex) {
            throw new MigrationResolutionException("Failed to resolve migration $migrationClassName", 0, $ex);
        }
    }
}
