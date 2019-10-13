<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Composer\Console\Commands;

use Aphiria\Console\Commands\Command;

/**
 * Defines the Composer update command
 */
final class ComposerUpdateCommand extends Command
{
    public function __construct()
    {
        parent::__construct('composer:update', [], [], 'Updates any Composer dependencies');
    }
}
