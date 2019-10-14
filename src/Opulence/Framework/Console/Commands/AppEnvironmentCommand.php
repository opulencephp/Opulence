<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Console\Commands;

use Aphiria\Console\Commands\Command;

/**
 * Defines the command that shows the current environment
 */
final class AppEnvironmentCommand extends Command
{
    public function __construct()
    {
        parent::__construct('app:env', [], [], 'Displays the current application environment');
    }
}
