<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Databases\Migrations;

/**
 * Defines the interface for migration resolvers to implement
 */
interface IMigrationResolver
{
    /**
     * Resolves a migration class
     *
     * @param string $migrationClassName The name of the migration class to resolve
     * @return IMigration The resolved migration
     * @throws MigrationResolutionException Thrown if the migration class could not be resolved
     */
    public function resolve(string $migrationClassName) : IMigration;
}
