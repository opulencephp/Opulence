<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Console\Commands;

use Aphiria\Console\Commands\Command;

/**
 * Defines the command the brings an application back up
 */
final class AppUpCommand extends Command
{
    public function __construct()
    {
        parent::__construct('app:up', [], [], 'Takes the application out of maintenance mode');
    }
}
