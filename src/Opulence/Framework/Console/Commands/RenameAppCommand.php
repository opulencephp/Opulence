<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Console\Commands;

use Opulence\Applications\Paths;
use Opulence\Console\Commands\Command;
use Opulence\Console\Prompts\Prompt;
use Opulence\Console\Prompts\Questions\Confirmation;
use Opulence\Console\Requests\Argument;
use Opulence\Console\Requests\ArgumentTypes;
use Opulence\Console\Responses\IResponse;
use Opulence\Files\FileSystem;

/**
 * Defines the command that renames an application
 */
class RenameAppCommand extends Command
{
    /** @var FileSystem The filesystem to use to write to files */
    protected $fileSystem = null;
    /** @var Prompt The prompt to confirm things with the user */
    protected $prompt = null;
    /** @var Paths The paths of the application */
    protected $paths = null;

    /**
     * @param FileSystem $fileSystem The filesystem to use to write to files
     * @param Prompt $prompt The prompt to confirm things with the user
     * @param Paths $paths The paths of the application
     */
    public function __construct(FileSystem $fileSystem, Prompt $prompt, Paths $paths)
    {
        parent::__construct();

        $this->fileSystem = $fileSystem;
        $this->prompt = $prompt;
        $this->paths = $paths;
    }

    /**
     * @inheritdoc
     */
    protected function define()
    {
        $this->setName("app:rename")
            ->setDescription("Renames an Opulence application")
            ->addArgument(new Argument(
                "currName",
                ArgumentTypes::REQUIRED,
                "The current application name ('Project' if it hasn't already been changed)"
            ))
            ->addArgument(new Argument(
                "newName",
                ArgumentTypes::REQUIRED,
                "The new application name"
            ))
            ->setHelpText("Be sure to use the correct capitalization for your app names.");
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
    {
        $confirmationQuestion = new Confirmation(
            sprintf(
                "Are you sure you want to rename \"%s\" to \"%s\"? ",
                $this->getArgumentValue("currName"),
                $this->getArgumentValue("newName")
            )
        );

        if ($this->prompt->ask($confirmationQuestion, $response)) {
            $this->updateComposer();
            $this->updateDirectories();
            $this->updateNamespaces();
            $this->updateConfigs();
            $response->writeln("<success>Updated name successfully</success>");
            $this->commandCollection->call("composer:dump-autoload", $response);
            $this->commandCollection->call("php apex framework:flushcache", $response);
        }
    }

    /**
     * Updates the Composer config
     */
    protected function updateComposer()
    {
        $currComposerContents = $this->fileSystem->read($this->paths["root"] . "/composer.json");
        // Change the PSR-4 namespace
        $updatedComposerContents = str_replace(
            $this->getArgumentValue("currName") . "\\\\",
            $this->getArgumentValue("newName") . "\\\\",
            $currComposerContents
        );
        // Change the PSR-4 directory
        $updatedComposerContents = str_replace(
            "src/" . $this->getArgumentValue("currName"),
            "src/" . $this->getArgumentValue("newName"),
            $updatedComposerContents
        );
        $this->fileSystem->write($this->paths["root"] . "/composer.json", $updatedComposerContents);
    }

    /**
     * Updates any class names that appear configs
     */
    protected function updateConfigs()
    {
        $configFiles = $this->fileSystem->getFiles($this->paths["configs"], true);

        foreach ($configFiles as $file) {
            $currentContents = $this->fileSystem->read($file);
            $updatedContents = str_replace(
                $this->getArgumentValue("currName") . "\\",
                $this->getArgumentValue("newName") . "\\",
                $currentContents
            );
            $this->fileSystem->write($file, $updatedContents);
        }
    }

    /**
     * Updates the app and test directories
     */
    protected function updateDirectories()
    {
        foreach (["src", "tests"] as $pathToUpdate) {
            // Move the directory to the new name
            $this->fileSystem->move(
                $this->paths[$pathToUpdate] . "/" . $this->getArgumentValue("currName"),
                $this->paths[$pathToUpdate] . "/" . $this->getArgumentValue("newName")
            );

            // Rename any references to the new namespace
            $appFiles = $this->fileSystem->getFiles($this->paths[$pathToUpdate], true);

            foreach ($appFiles as $file) {
                $currentContents = $this->fileSystem->read($file);
                $updatedContents = str_replace(
                    $this->getArgumentValue("currName") . "\\",
                    $this->getArgumentValue("newName") . "\\",
                    $currentContents
                );
                $this->fileSystem->write($file, $updatedContents);
            }
        }
    }

    /**
     * Updates the namespaces
     */
    protected function updateNamespaces()
    {
        foreach (["src", "tests"] as $pathToUpdate) {
            $files = $this->fileSystem->getFiles($this->paths[$pathToUpdate], true);

            foreach ($files as $file) {
                $currContents = $this->fileSystem->read($file);
                // Change the "namespace" statements
                $updatedContents = str_replace(
                    [
                        "namespace " . $this->getArgumentValue("currName") . ";",
                        "namespace " . $this->getArgumentValue("currName") . "\\"
                    ],
                    [
                        "namespace " . $this->getArgumentValue("newName") . ";",
                        "namespace " . $this->getArgumentValue("newName") . "\\"
                    ],
                    $currContents
                );
                // Change the "use" statements
                $updatedContents = str_replace(
                    [
                        "use " . $this->getArgumentValue("currName") . ";",
                        "use " . $this->getArgumentValue("currName") . "\\"
                    ],
                    [
                        "use " . $this->getArgumentValue("newName") . ";",
                        "use " . $this->getArgumentValue("newName") . "\\"
                    ],
                    $updatedContents
                );
                $this->fileSystem->write($file, $updatedContents);
            }
        }
    }
}