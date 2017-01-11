<?php
/**
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
        $confirmationQuestion = new Confirmation(
            sprintf(
                'Are you sure you want to rename %s to %s? [y/n] ',
                $this->getArgumentValue('currName'),
                $this->getArgumentValue('newName')
            )
        );
        $renameDirectoriesQuestion = new Confirmation(
            sprintf(
                'Do you want to rename src/%s to src/%s and tests/%s to tests/%s? [y/n] ',
                $this->getArgumentValue('currName'),
                $this->getArgumentValue('newName'),
                $this->getArgumentValue('currName'),
                $this->getArgumentValue('newName')
            )
        );

        if ($this->prompt->ask($confirmationQuestion, $response)) {
            $renameDirectories = $this->prompt->ask($renameDirectoriesQuestion, $response);
            $this->updateComposer($renameDirectories);
            $this->updateSrcAndTestDirectories($renameDirectories);
            $this->updateNamespaces();
            $this->updateConfigs();
            $response->writeln('<success>Updated name successfully</success>');
            $this->commandCollection->call('composer:dump-autoload', $response);
            $this->commandCollection->call('framework:flushcache', $response);
        }
    }

    /**
     * Updates the Composer config
     *
     * @param bool $renameDirectories Whether or not to rename directories
     */
    protected function updateComposer(bool $renameDirectories)
    {
        $rootPath = Config::get('paths', 'root');
        $currComposerContents = $this->fileSystem->read("$rootPath/composer.json");
        // Change the PSR-4 namespace
        $updatedComposerContents = str_replace(
            $this->getArgumentValue('currName') . "\\\\",
            $this->getArgumentValue('newName') . "\\\\",
            $currComposerContents
        );

        if ($renameDirectories) {
            // Change the PSR-4 directory
            $updatedComposerContents = str_replace(
                'src/' . $this->getArgumentValue('currName'),
                'src/' . $this->getArgumentValue('newName'),
                $updatedComposerContents
            );
        }
        $this->fileSystem->write("$rootPath/composer.json", $updatedComposerContents);
    }

    /**
     * Updates any class names that appear in configs
     */
    protected function updateConfigs()
    {
        $configFiles = $this->fileSystem->getFiles(Config::get('paths', 'config'), true);

        foreach ($configFiles as $file) {
            $currentContents = $this->fileSystem->read($file);
            $updatedContents = str_replace(
                $this->getArgumentValue('currName') . "\\",
                $this->getArgumentValue('newName') . "\\",
                $currentContents
            );
            $this->fileSystem->write($file, $updatedContents);
        }
    }

    /**
     * Updates the namespaces
     */
    protected function updateNamespaces()
    {
        $paths = [Config::get('paths', 'src'), Config::get('paths', 'tests')];

        foreach ($paths as $pathToUpdate) {
            $files = $this->fileSystem->getFiles($pathToUpdate, true);

            foreach ($files as $file) {
                $currContents = $this->fileSystem->read($file);
                // Change the "namespace" statements
                $updatedContents = str_replace(
                    [
                        "namespace {$this->getArgumentValue('currName')};",
                        "namespace {$this->getArgumentValue('currName')}\\"
                    ],
                    [
                        "namespace {$this->getArgumentValue('newName')};",
                        "namespace {$this->getArgumentValue('newName')}\\"
                    ],
                    $currContents
                );
                // Change the "use" statements
                $updatedContents = str_replace(
                    [
                        "use {$this->getArgumentValue('currName')};",
                        "use {$this->getArgumentValue('currName')}\\"
                    ],
                    [
                        "use {$this->getArgumentValue('newName')};",
                        "use {$this->getArgumentValue('newName')}\\"
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
     * @param bool $renameDirectories Whether or not to rename directories
     */
    protected function updateSrcAndTestDirectories(bool $renameDirectories)
    {
        $paths = [Config::get('paths', 'src'), Config::get('paths', 'tests')];

        foreach ($paths as $pathToUpdate) {
            if ($renameDirectories) {
                $this->fileSystem->move(
                    "$pathToUpdate/{$this->getArgumentValue('currName')}",
                    "$pathToUpdate/{$this->getArgumentValue('newName')}"
                );
            }

            // Rename any references to the new namespace
            $appFiles = $this->fileSystem->getFiles($pathToUpdate, true);

            foreach ($appFiles as $file) {
                $currentContents = $this->fileSystem->read($file);
                $updatedContents = str_replace(
                    $this->getArgumentValue('currName') . "\\",
                    $this->getArgumentValue('newName') . "\\",
                    $currentContents
                );
                $this->fileSystem->write($file, $updatedContents);
            }
        }
    }
}
