<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Framework\Databases\Migrations;

use DateTime;
use Opulence\Databases\Migrations\Migration;

/**
 * Defines the migration that sets up the executed migration table
 */
class CreateExecutedMigrationTable extends Migration
{
    /**
     * Gets the creation date, which is used for ordering
     *
     * @return DateTime The date this migration was created
     */
    public static function getCreationDate() : DateTime
    {
        return DateTime::createFromFormat(DateTime::ATOM, '2017-08-29T02:43:26+00:00');
    }

    /**
     * Executes the query that rolls back the migration
     */
    public function down() : void
    {
        $sql = 'DROP TABLE IF EXISTS :tableName;';
        $statement = $this->connection->prepare($sql);
        $statement->bindValue('tableName', SqlExecutedMigrationRepository::DEFAULT_TABLE_NAME);
        $statement->execute();
    }

    /**
     * Executes the query that commits the migration
     */
    public function up() : void
    {
        $sql = 'CREATE TABLE :tableName (migration text primary key, dateran timestamp with time zone NOT NULL);';
        $statement = $this->connection->prepare($sql);
        $statement->bindValue('tableName', SqlExecutedMigrationRepository::DEFAULT_TABLE_NAME);
        $statement->execute();
    }
}
