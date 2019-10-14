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

use Opulence\Framework\Console\Commands\MakeCommand;

/**
 * Defines the command that makes a migration
 */
final class MakeMigrationCommand extends MakeCommand
{
    public function __construct()
    {
        parent::__construct('make:migration', [], [], 'Creates a database migration class');
    }
}
