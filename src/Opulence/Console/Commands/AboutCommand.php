<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Console\Commands;

use Opulence\Console\Responses\Formatters\PaddingFormatter;
use Opulence\Console\Responses\IResponse;

/**
 * Defines the about command
 */
class AboutCommand extends Command
{
    /** @var string The template for the output */
    private static $template = <<<EOF
-----------------------------
About <b>Apex</b> {{version}}
-----------------------------
{{commands}}
EOF;
    /** @var PaddingFormatter The space padding formatter to use */
    private $paddingFormatter = null;
    /** @var string The version number of the application */
    private $applicationVersion = 'Unknown';

    /**
     * @param CommandCollection $commands The list of commands
     * @param PaddingFormatter $paddingFormatter The space padding formatter to use
     * @param string $applicationVersion The version number of the application
     */
    public function __construct(
        CommandCollection &$commands,
        PaddingFormatter $paddingFormatter,
        string $applicationVersion
    ) {
        parent::__construct();

        $this->setCommandCollection($commands);
        $this->paddingFormatter = $paddingFormatter;
        $this->applicationVersion = $applicationVersion;
    }

    /**
     * @inheritdoc
     */
    protected function define()
    {
        $this->setName('about')
            ->setDescription('Describes the Apex console application');
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
    {
        // Compile the template
        $compiledTemplate = self::$template;
        $compiledTemplate = str_replace('{{commands}}', $this->getCommandText(), $compiledTemplate);
        $compiledTemplate = str_replace('{{version}}', $this->applicationVersion, $compiledTemplate);

        $response->writeln($compiledTemplate);
    }

    /**
     * Converts commands to text
     *
     * @return string The commands as text
     */
    private function getCommandText() : string
    {
        if (count($this->commandCollection->getAll()) === 0) {
            return '  <info>No commands</info>';
        }

        /**
         * Sorts the commands by name
         * Uncategorized (commands without ":" in their names) always come first
         *
         * @param ICommand $a
         * @param ICommand $b
         * @return int The result of the comparison
         */
        $sort = function ($a, $b) {
            if (strpos($a->getName(), ':') === false) {
                if (strpos($b->getName(), ':') === false) {
                    // They're both uncategorized
                    return $a->getName() < $b->getName() ? -1 : 1;
                } else {
                    // B is categorized
                    return -1;
                }
            } else {
                if (strpos($b->getName(), ':') === false) {
                    // A is categorized
                    return 1;
                } else {
                    // They're both categorized
                    return $a->getName() < $b->getName() ? -1 : 1;
                }
            }
        };

        $commands = $this->commandCollection->getAll();
        usort($commands, $sort);
        $categorizedCommandNames = [];
        $commandTexts = [];
        $firstCommandNamesToCategories = [];

        foreach ($commands as $command) {
            $commandNameParts = explode(':', $command->getName());

            if (count($commandNameParts) > 1 && !in_array($commandNameParts[0], $firstCommandNamesToCategories)) {
                $categorizedCommandNames[] = $command->getName();

                // If this is the first command for this category
                if (!in_array($commandNameParts[0], $firstCommandNamesToCategories)) {
                    $firstCommandNamesToCategories[$command->getName()] = $commandNameParts[0];
                }
            }

            $commandTexts[] = [$command->getName(), $command->getDescription()];
        }

        return $this->paddingFormatter->format($commandTexts,
            function ($row) use ($categorizedCommandNames, $firstCommandNamesToCategories) {
                $output = '';

                // If this is the first command of its category, display the category
                if (in_array(trim($row[0]),
                        $categorizedCommandNames) && isset($firstCommandNamesToCategories[trim($row[0])])
                ) {
                    $output .= "<comment>{$firstCommandNamesToCategories[trim($row[0])]}</comment>" . PHP_EOL;
                }

                return $output . "  <info>{$row[0]}</info> - {$row[1]}";
            });
    }
}
