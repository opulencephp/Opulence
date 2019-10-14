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
use Aphiria\Console\Input\Option;
use Aphiria\Console\Input\OptionTypes;

/**
 * Defines the command for running down migrations
 */
final class RunDownMigrationsCommand extends Command
{
    public function __construct()
    {
        parent::__construct(
            'migrations:down',
            [],
            [
                new Option(
                    'number',
                    null,
                    OptionTypes::REQUIRED_VALUE,
                    'The number of migrations to roll back',
                    1
                )
            ],
            'Runs the "down" database migrations'
        );
    }
}
