<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the command that renames an application
 */
namespace RDev\Framework\Console\Commands;
use RDev\Applications;
use RDev\Console\Commands;
use RDev\Console\Prompts;
use RDev\Console\Prompts\Questions;
use RDev\Console\Requests;
use RDev\Console\Responses;
use RDev\Files;

class RenameApp extends Commands\Command
{
    /** @var Files\FileSystem The filesystem to use to write to files */
    private $fileSystem = null;
    /** @var Prompts\Prompt The prompt to confirm things with the user */
    private $prompt = null;
    /** @var Applications\Paths The paths of the application */
    private $paths = null;

    /**
     * @param Files\FileSystem $fileSystem The filesystem to use to write to files
     * @param Prompts\Prompt $prompt The prompt to confirm things with the user
     * @param Applications\Paths $paths The paths of the application
     */
    public function __construct(Files\FileSystem $fileSystem, Prompts\Prompt $prompt, Applications\Paths $paths)
    {
        parent::__construct();

        $this->fileSystem = $fileSystem;
        $this->prompt = $prompt;
        $this->paths = $paths;
    }

    /**
     * {@inheritdoc}
     */
    protected function define()
    {
        $this->setName("app:rename")
            ->setDescription("Renames an RDev application")
            ->addArgument(new Requests\Argument(
                "currName",
                Requests\ArgumentTypes::REQUIRED,
                "The current application name ('Project' if it hasn't already been changed)"
            ))
            ->addArgument(new Requests\Argument(
                "newName",
                Requests\ArgumentTypes::REQUIRED,
                "The new application name"
            ))
            ->setHelpText("Be sure to use the correct capitalization for your app names.");
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute(Responses\IResponse $response)
    {
        $confirmationQuestion = new Questions\Confirmation(
            sprintf(
                "Are you sure you want to rename \"%s\" to \"%s\"?",
                $this->getArgumentValue("currName"),
                $this->getArgumentValue("newName")
            )
        );

        if($this->prompt->ask($confirmationQuestion, $response))
        {
            $this->updateComposer();
            $this->updateAppDirectory();
            $this->updateNamespaces();
            $this->updateConfigs();
            $response->writeln("<success>Updated name successfully</success>");
            $response->writeln("<comment>Run \"composer dump-autoload -o\" to update the autoloader</comment>");
        }
    }

    /**
     * Updates the app directory
     */
    private function updateAppDirectory()
    {
        // Move the directory to the new name
        $this->fileSystem->move(
            $this->paths["app"] . "/" . strtolower($this->getArgumentValue("currName")),
            $this->paths["app"] . "/" . strtolower($this->getArgumentValue("newName"))
        );

        // Rename any references to the new namespace
        $appFiles = $this->fileSystem->getFiles($this->paths["app"], true);

        foreach($appFiles as $file)
        {
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
     * Updates the Composer config
     */
    private function updateComposer()
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
            "app/" . strtolower($this->getArgumentValue("currName")),
            "app/" . strtolower($this->getArgumentValue("newName")),
            $updatedComposerContents
        );
        $this->fileSystem->write($this->paths["root"] . "/composer.json", $updatedComposerContents);
    }

    /**
     * Updates any class names that appear configs
     */
    private function updateConfigs()
    {
        $configFiles = $this->fileSystem->getFiles($this->paths["configs"], true);

        foreach($configFiles as $file)
        {
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
     * Updates the namespaces
     */
    private function updateNamespaces()
    {
        $files = $this->fileSystem->getFiles($this->paths["app"], true);

        foreach($files as $file)
        {
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