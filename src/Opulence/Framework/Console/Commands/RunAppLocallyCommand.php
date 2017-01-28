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
use Opulence\Console\Requests\Option;
use Opulence\Console\Requests\OptionTypes;
use Opulence\Console\Responses\IResponse;

/**
 * Defines the command that lets you run your application locally
 */
class RunAppLocallyCommand extends Command
{
    /**
     * @inheritdoc
     */
    protected function define()
    {
        $this->setName('app:runlocally')
            ->setDescription('Runs your application locally')
            ->addOption(new Option(
                'domain',
                null,
                OptionTypes::REQUIRED_VALUE,
                'The domain to run your application at',
                'localhost'
            ))
            ->addOption(new Option(
                'port',
                null,
                OptionTypes::REQUIRED_VALUE,
                'The port to run your application at',
                80
            ))
            ->addOption(new Option(
                'docroot',
                null,
                OptionTypes::REQUIRED_VALUE,
                'The document root of your application, eg the "public" directory',
                'public'
            ))
            ->addOption(new Option(
                'router',
                null,
                OptionTypes::REQUIRED_VALUE,
                'The router file in your application',
                'localhost_router.php'
            ));
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
    {
        $domain = $this->getOptionValue('domain');
        $port = (int)$this->getOptionValue('port');
        $response->writeln("Running at http://$domain:$port");
        $command = sprintf(
            '%s -S %s:%d -t %s %s',
            PHP_BINARY,
            $domain,
            $port,
            $this->getOptionValue('docroot'),
            $this->getOptionValue('router')
        );
        passthru($command);
    }
}
