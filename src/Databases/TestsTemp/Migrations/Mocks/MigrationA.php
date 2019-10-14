<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Databases\TestsTemp\Migrations\Mocks;

use DateTime;
use Opulence\Databases\Migrations\Migration;

/**
 * Defines a mock migration
 */
class MigrationA extends Migration
{
    /**
     * @inheritdoc
     */
    public static function getCreationDate(): DateTime
    {
        return new DateTime('2017-08-13T12:00:00Z');
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        // Don't do anything
    }
}
