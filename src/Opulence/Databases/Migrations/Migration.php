<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Databases\Migrations;

use Opulence\Databases\IConnection;

/**
 * Defines a database migration
 */
abstract class Migration implements IMigration
{
    /** @var IConnection The connection to use in the migration */
    protected IConnection $connection;

    /**
     * @param IConnection $connection The connection to use in the migration
     */
    public function __construct(IConnection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        // Left intentionally blank
    }
}
