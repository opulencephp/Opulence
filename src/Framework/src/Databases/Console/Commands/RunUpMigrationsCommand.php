<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Databases\Console\Commands;

use Aphiria\Console\Commands\Command;

/**
 * Defines the command for running up migrations
 */
final class RunUpMigrationsCommand extends Command
{
    public function __construct()
    {
        parent::__construct('migrations:up', [], [], 'Runs the "up" database migrations');
    }
}