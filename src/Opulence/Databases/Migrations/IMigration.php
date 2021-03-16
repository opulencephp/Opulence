<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Databases\Migrations;

use DateTime;

/**
 * Defines the interface for database migrations to implement
 */
interface IMigration
{
    /**
     * Gets the creation date, which is used for ordering
     *
     * @return DateTime The date this migration was created
     */
    public static function getCreationDate() : DateTime;

    /**
     * Executes the query that rolls back the migration
     */
    public function down() : void;

    /**
     * Executes the query that commits the migration
     */
    public function up() : void;
}
