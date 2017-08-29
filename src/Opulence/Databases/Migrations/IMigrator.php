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
 * Defines the interface for database migrators to implement
 */
interface IMigrator
{
    /**
     * Rolls back all migrations
     *
     * @return string[] The list of rolled back migration classes
     */
    public function rollBackAllMigrations() : array;

    /**
     * Rolls back a certain number of migrations
     *
     * @param int $number The number of migrations from the end (1 is last migration)
     * @return string[] The list of rolled back migration classes
     */
    public function rollBackMigrations(int $number = 1) : array;

    /**
     * Runs the migrations
     *
     * @return string[] The list of run migration classes
     */
    public function runMigrations() : array;
}
