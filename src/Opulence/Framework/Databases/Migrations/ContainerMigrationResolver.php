<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Framework\Databases\Migrations;

use Opulence\Databases\Migrations\IMigration;
use Opulence\Databases\Migrations\IMigrationResolver;
use Opulence\Databases\Migrations\MigrationResolutionException;
use Opulence\Ioc\IContainer;
use Opulence\Ioc\IocException;

/**
 * Defines the container migration resolver
 */
class ContainerMigrationResolver implements IMigrationResolver
{
    /** @var IContainer The DI container */
    private $container = null;

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
    public function resolve(string $migrationClassName) : IMigration
    {
        try {
            return $this->container->resolve($migrationClassName);
        } catch (IocException $ex) {
            throw new MigrationResolutionException("Failed to resolve migration $migrationClassName", 0, $ex);
        }
    }
}
