<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Databases\Tests\Migrations\Mocks;

use DateTime;
use Opulence\Databases\Migrations\Migration;

/**
 * Defines a mock migration
 */
class MigrationB extends Migration
{
    /**
     * @inheritdoc
     */
    public static function getCreationDate() : DateTime
    {
        return new DateTime('2017-08-14T12:00:00Z');
    }

    /**
     * @inheritdoc
     */
    public function up() : void
    {
        // Don't do anything
    }
}
