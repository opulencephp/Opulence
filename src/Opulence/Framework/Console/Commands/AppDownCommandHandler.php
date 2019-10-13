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

use Aphiria\Console\Commands\ICommandHandler;
use Aphiria\Console\Input\Input;
use Aphiria\Console\Output\IOutput;
use Opulence\Framework\Configuration\Config;

/**
 * Defines the application-down command handler
 */
final class AppDownCommandHandler implements ICommandHandler
{
    /**
     * @inheritdoc
     */
    public function handle(Input $input, IOutput $output)
    {
        if (file_put_contents(Config::get('paths', 'tmp.framework.http') . '/down', 'down') === false) {
            $output->writeln('<error>Failed to put application into maintenance mode</error>');
        } else {
            $output->writeln('<success>Application in maintenance mode</success>');
        }
    }
}
