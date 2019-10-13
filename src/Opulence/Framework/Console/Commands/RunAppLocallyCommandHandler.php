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

/**
 * Defines the command handler that lets you run your application locally
 */
final class RunAppLocallyCommandHandler implements ICommandHandler
{
    /**
     * @inheritdoc
     */
    public function handle(Input $input, IOutput $output)
    {
        $domain = $input->options['domain'];
        $port = (int)$input->options['port'];
        $output->writeln("Running at http://$domain:$port");
        $command = sprintf(
            '%s -S %s:%d -t %s %s',
            PHP_BINARY,
            $domain,
            $port,
            $input->options['docroot'],
            $input->options['router']
        );
        passthru($command);
    }
}
