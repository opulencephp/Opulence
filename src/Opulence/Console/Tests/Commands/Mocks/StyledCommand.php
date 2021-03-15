<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Console\Tests\Commands\Mocks;

use Opulence\Console\Commands\Command;
use Opulence\Console\Responses\IResponse;

/**
 * Mocks a command with styled output
 */
class StyledCommand extends Command
{
    /**
     * @inheritdoc
     */
    protected function define()
    {
        $this->setName('stylish');
        $this->setDescription('Shows an output with style');
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
    {
        $response->write("<b>I've got style</b>");
    }
}
