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
use Opulence\Console\Prompts\Prompt;
use Opulence\Console\Prompts\Questions\Confirmation;
use Opulence\Console\Requests\Argument;
use Opulence\Console\Requests\ArgumentTypes;
use Opulence\Console\Responses\IResponse;
use Opulence\Files\FileSystem;
use Opulence\Framework\Configuration\Config;

/**
 * Defines the command that renames an application
 */
class RenameAppCommand extends Command
{
    /** @var FileSystem The filesystem to use to write to files */
    protected $fileSystem = null;
    /** @var Prompt The prompt to confirm things with the user */
    protected $prompt = null;

    /**
     * @param FileSystem $fileSystem The filesystem to use to write to files
     * @param Prompt $prompt The prompt to confirm things with the user
     */
    public function __construct(FileSystem $fileSystem, Prompt $prompt)
    {
        parent::__construct();

        $this->fileSystem = $fileSystem;
        $this->prompt = $prompt;
    }

    /**
     * @inheritdoc
     */
    protected function define()
    {
        $this->setName('app:rename')
            ->setDescription('Renames an Opulence application')
            ->addArgument(new Argument(
                'currName',
                ArgumentTypes::REQUIRED,
                "The current application name ('Project' if it hasn't already been changed)"
            ))
            ->addArgument(new Argument(
                'newName',
                ArgumentTypes::REQUIRED,
                'The new application name'
            ))
            ->setHelpText('Be sure to use the correct capitalization for your app names.');
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
    {
        $currName = $this->getArgumentValue('currName');
        $newName = $this->getArgumentValue('newName');

        if (preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $currName) !== 1) {
            $response->writeln("<error>Current name \"$currName\" is not a valid PHP namespace</error>");

            return;
        }

        if (preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $newName) !== 1) {
            $response->writeln("<error>New name \"$newName\" is not a valid PHP namespace</error>");

            return;
        }

        $confirmationQuestion = new Confirmation(
            sprintf(
                'Are you sure you want to rename %s to %s? [y/n] ',
                $currName,
                $newName
            )
        );
        $renameDirectoriesQuestion = new Confirmation(
            sprintf(
                'Do you want to rename src/%s to src/%s and tests/%s to tests/%s? [y/n] ',
                $currName,
                $newName,
                $currName,
                $newName
            )
        );

        if ($this->prompt->ask($confirmationQuestion, $response)) {
            $renameDirectories = $this->prompt->ask($renameDirectoriesQuestion, $response);
            $this->updateComposer($currName, $newName, $renameDirectories);
            $this->updateSrcAndTestDirectories($currName, $newName, $renameDirectories);
            $this->updateNamespaces($currName, $newName);
            $this->updateConfigs($currName, $newName);
            $response->writeln('<success>Updated name successfully</success>');
            $this->commandCollection->call('composer:dump-autoload', $response);
            $this->commandCollection->call('framework:flushcache', $response);
        }
    }

    /**
     * Updates the Composer config
     *
     * @param string $currName The current application name
     * @param string $newName The new application name
     * @param bool $renameDirectories Whether or not to rename directories
     */
    protected function updateComposer(string $currName, string $newName, bool $renameDirectories)
    {
        $rootPath = Config::get('paths', 'root');
        $currComposerContents = $this->fileSystem->read("$rootPath/composer.json");
        // Change the PSR-4 namespace
        $updatedComposerContents = str_replace(
            "$currName\\\\",
            "$newName\\\\",
            $currComposerContents
        );

        if ($renameDirectories) {
            // Change the PSR-4 directory
            $updatedComposerContents = str_replace(
                "src/$currName",
                "src/$newName",
                $updatedComposerContents
            );
        }
        $this->fileSystem->write("$rootPath/composer.json", $updatedComposerContents);
    }

    /**
     * Updates any class names that appear in configs
     *
     * @param string $currName The current application name
     * @param string $newName The new application name
     */
    protected function updateConfigs(string $currName, string $newName)
    {
        $configFiles = $this->fileSystem->getFiles(Config::get('paths', 'config'), true);

        foreach ($configFiles as $file) {
            $currentContents = $this->fileSystem->read($file);
            $updatedContents = str_replace(
                "$currName\\",
                "$newName\\",
                $currentContents
            );
            $this->fileSystem->write($file, $updatedContents);
        }
    }

    /**
     * Updates the namespaces
     *
     * @param string $currName The current application name
     * @param string $newName The new application name
     */
    protected function updateNamespaces(string $currName, string $newName)
    {
        $paths = [Config::get('paths', 'src'), Config::get('paths', 'tests')];

        foreach ($paths as $pathToUpdate) {
            $files = $this->fileSystem->getFiles($pathToUpdate, true);

            foreach ($files as $file) {
                $currContents = $this->fileSystem->read($file);
                // Change the "namespace" statements
                $updatedContents = str_replace(
                    [
                        "namespace $currName;",
                        "namespace $currName\\"
                    ],
                    [
                        "namespace $newName;",
                        "namespace $newName\\"
                    ],
                    $currContents
                );
                // Change the "use" statements
                $updatedContents = str_replace(
                    [
                        "use $currName;",
                        "use $currName\\"
                    ],
                    [
                        "use $newName;",
                        "use $newName\\"
                    ],
                    $updatedContents
                );
                $this->fileSystem->write($file, $updatedContents);
            }
        }
    }

    /**
     * Updates the src and test directories
     *
     * @param string $currName The current application name
     * @param string $newName The new application name
     * @param bool $renameDirectories Whether or not to rename directories
     */
    protected function updateSrcAndTestDirectories(string $currName, string $newName, bool $renameDirectories)
    {
        $paths = [Config::get('paths', 'src'), Config::get('paths', 'tests')];

        foreach ($paths as $pathToUpdate) {
            if ($renameDirectories) {
                $this->fileSystem->move(
                    "$pathToUpdate/$currName",
                    "$pathToUpdate/$newName"
                );
            }

            // Rename any references to the new namespace
            $appFiles = $this->fileSystem->getFiles($pathToUpdate, true);

            foreach ($appFiles as $file) {
                $currentContents = $this->fileSystem->read($file);
                $updatedContents = str_replace(
                    "$currName\\",
                    "$newName\\",
                    $currentContents
                );
                $this->fileSystem->write($file, $updatedContents);
            }
        }
    }
}
