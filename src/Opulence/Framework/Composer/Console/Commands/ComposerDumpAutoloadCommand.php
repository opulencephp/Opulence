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
 * Defines the composer dump autoload command
 */
final class ComposerDumpAutoloadCommand extends Command
{
    public function __construct()
    {
        parent::__construct('composer:dump-autoload', [], [], 'Dump the Composer autoload');
    }
}
