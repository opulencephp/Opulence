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
 * Defines the command that flushes the framework's cache
 */
final class FlushFrameworkCacheCommand extends Command
{
    public function __construct()
    {
        parent::__construct('framework:flushcache', [], [], 'Flushes all of the framework\'s cached files');
    }
}
