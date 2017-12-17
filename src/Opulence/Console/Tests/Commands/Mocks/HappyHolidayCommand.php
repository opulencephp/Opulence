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
use Opulence\Console\Requests\Argument;
use Opulence\Console\Requests\ArgumentTypes;
use Opulence\Console\Requests\Option;
use Opulence\Console\Requests\OptionTypes;
use Opulence\Console\Responses\IResponse;

/**
 * Mocks a command with arguments and options
 */
class HappyHolidayCommand extends Command
{
    /**
     * @inheritdoc
     */
    protected function define() : void
    {
        $this->setName('holiday');
        $this->setDescription('Wishes someone a happy holiday');
        $this->addArgument(new Argument(
            'holiday',
            ArgumentTypes::REQUIRED,
            'Holiday to wish someone'
        ));
        $this->addOption(new Option(
            'yell',
            'y',
            OptionTypes::OPTIONAL_VALUE,
            'Whether or not we yell',
            'yes'
        ));
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
    {
        $message = 'Happy ' . $this->getArgumentValue('holiday');

        if ($this->getOptionValue('yell') === 'yes') {
            $message .= '!';
        }

        $response->write($message);
    }
}
