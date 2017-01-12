<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Framework\Console\Commands;

use Opulence\Console\Commands\Command;
use Opulence\Console\Responses\IResponse;
use Opulence\Framework\Configuration\Config;

/**
 * Defines the application-down command
 */
class AppDownCommand extends Command
{
    /**
     * @inheritdoc
     */
    protected function define()
    {
        $this->setName('app:down')
            ->setDescription('Puts the application into maintenance mode');
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
    {
        if (file_put_contents(Config::get('paths', 'tmp.framework.http') . '/down', 'down') === false) {
            $response->writeln('<error>Failed to put application into maintenance mode</error>');
        } else {
            $response->writeln('<success>Application in maintenance mode</success>');
        }
    }
}
