<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Console\Commands;

use Opulence\Console\Requests\Argument;
use Opulence\Console\Requests\ArgumentTypes;
use Opulence\Console\Requests\Option;
use Opulence\Console\Responses\Formatters\CommandFormatter;
use Opulence\Console\Responses\Formatters\PaddingFormatter;
use Opulence\Console\Responses\IResponse;

/**
 * Defines the help command
 */
class HelpCommand extends Command
{
    /** @var string The template for the output */
    private static $template = <<<EOF
-----------------------------
Command: <info>{{name}}</info>
-----------------------------
<b>{{command}}</b>

<comment>Description:</comment>
  {{description}}
<comment>Arguments:</comment>
{{arguments}}
<comment>Options:</comment>
{{options}}{{helpText}}
EOF;
    /** @var ICommand The command to help with */
    private $command = null;
    /** @var CommandFormatter The formatter that converts a command object to text */
    private $commandFormatter = null;
    /** @var PaddingFormatter The space padding formatter to use */
    private $paddingFormatter = null;

    /**
     * @param CommandFormatter $commandFormatter The formatter that converts a command object to text
     * @param PaddingFormatter $paddingFormatter The space padding formatter to use
     */
    public function __construct(CommandFormatter $commandFormatter, PaddingFormatter $paddingFormatter)
    {
        parent::__construct();

        $this->commandFormatter = $commandFormatter;
        $this->paddingFormatter = $paddingFormatter;
    }

    /**
     * Sets the command to help with
     *
     * @param ICommand $command The command to help with
     */
    public function setCommand(ICommand $command)
    {
        $this->command = $command;
    }

    /**
     * @inheritdoc
     */
    protected function define()
    {
        $this->setName('help')
            ->setDescription('Displays information about a command')
            ->addArgument(new Argument(
                'command',
                ArgumentTypes::OPTIONAL,
                'The command to get help with'
            ));
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
    {
        if ($this->command === null) {
            $response->writeln("<comment>Pass in the name of the command you'd like help with</comment>");
        } else {
            $descriptionText = 'No description';
            $helpText = '';

            if ($this->command->getDescription() !== '') {
                $descriptionText = $this->command->getDescription();
            }

            if ($this->command->getHelpText() !== '') {
                $helpText = PHP_EOL . '<comment>Help:</comment>' . PHP_EOL . '  ' . $this->command->getHelpText();
            }

            // Compile the template
            $compiledTemplate = self::$template;
            $compiledTemplate = str_replace('{{command}}', $this->commandFormatter->format($this->command),
                $compiledTemplate);
            $compiledTemplate = str_replace('{{description}}', $descriptionText, $compiledTemplate);
            $compiledTemplate = str_replace('{{name}}', $this->command->getName(), $compiledTemplate);
            $compiledTemplate = str_replace('{{arguments}}', $this->getArgumentText(), $compiledTemplate);
            $compiledTemplate = str_replace('{{options}}', $this->getOptionText(), $compiledTemplate);
            $compiledTemplate = str_replace('{{helpText}}', $helpText, $compiledTemplate);

            $response->writeln($compiledTemplate);
        }
    }

    /**
     * Converts the command arguments to text
     *
     * @return string The arguments as text
     */
    private function getArgumentText() : string
    {
        if (count($this->command->getArguments()) === 0) {
            return '  No arguments';
        }

        $argumentTexts = [];

        foreach ($this->command->getArguments() as $argument) {
            $argumentTexts[] = [$argument->getName(), $argument->getDescription()];
        }

        return $this->paddingFormatter->format($argumentTexts, function ($row) {
            return "  <info>{$row[0]}</info> - {$row[1]}";
        });
    }

    /**
     * Gets the option names as a formatted string
     *
     * @param Option $option The option to convert to text
     * @return string The option names as text
     */
    private function getOptionNames(Option $option) : string
    {
        $optionNames = "--{$option->getName()}";

        if ($option->getShortName() !== null) {
            $optionNames .= "|-{$option->getShortName()}";
        }

        return $optionNames;
    }

    /**
     * Gets the options as text
     *
     * @return string The options as text
     */
    private function getOptionText() : string
    {
        if (count($this->command->getOptions()) === 0) {
            return '  No options';
        }

        $optionTexts = [];

        foreach ($this->command->getOptions() as $option) {
            $optionTexts[] = [$this->getOptionNames($option), $option->getDescription()];
        }

        return $this->paddingFormatter->format($optionTexts, function ($row) {
            return "  <info>{$row[0]}</info> - {$row[1]}";
        });
    }
}
