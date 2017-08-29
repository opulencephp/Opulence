<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Databases\Migrations;

/**
 * Defines the interface for executed database migration repositories to implement
 */
interface IExecutedMigrationRepository
{
    /**
     * Adds a migration that has been executed
     *
     * @param string $migrationClassName The class name of the migration that has been executed
     */
    public function add(string $migrationClassName) : void;

    /**
     * Deletes a migration that has been executed
     *
     * @param string $migrationClassName The class name of the migration that has been executed
     */
    public function delete(string $migrationClassName) : void;

    /**
     * Gets all executed migration class names in descending order they were executed
     *
     * @return string[] The list of migration class names
     */
    public function getAll() : array;

    /**
     * Gets the last executed migrations
     *
     * @param int $number The number from the last migration to get
     * @return string[] The last executed migration class names
     */
    public function getLast(int $number = 1) : array;
}
