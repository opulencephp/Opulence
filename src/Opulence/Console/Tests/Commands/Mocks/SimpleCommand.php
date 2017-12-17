<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Console\Tests\Commands\Mocks;

use Opulence\Console\Commands\Command;
use Opulence\Console\Responses\IResponse;

/**
 * Mocks a simple command for use in testing
 */
class SimpleCommand extends Command
{
    /**
     * @param string $name The name of the command
     * @param string $description A brief description of the command
     * @param string $helpText The help text of the command
     */
    public function __construct($name, $description, $helpText = '')
    {
        $this->setName($name);
        $this->setDescription($description);
        $this->setHelpText($helpText);

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function define() : void
    {
        // Don't do anything
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
    {
        $response->write('foo');
    }
}
