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
use Aphiria\Console\Input\Option;
use Aphiria\Console\Input\OptionTypes;

/**
 * Defines the command that runs the application locally
 */
final class RunAppLocallyCommand extends Command
{
    /**
     * @param string $defaultRouterPath The path to the default router file
     */
    public function __construct(string $defaultRouterPath)
    {
        parent::__construct(
            'app:runlocally',
            [],
            [
                new Option(
                    'domain',
                    null,
                    OptionTypes::REQUIRED_VALUE,
                    'The domain to run your application at',
                    'localhost'
                ),
                new Option(
                    'port',
                    null,
                    OptionTypes::REQUIRED_VALUE,
                    'The port to run your application at',
                    80
                ),
                new Option(
                    'docroot',
                    null,
                    OptionTypes::REQUIRED_VALUE,
                    'The document root of your application, eg the "public" directory',
                    'public'
                ),
                new Option(
                    'router',
                    null,
                    OptionTypes::REQUIRED_VALUE,
                    'The router file in your application',
                    $defaultRouterPath
                )
            ],
            'Runs your application locally'
        );
    }
}
