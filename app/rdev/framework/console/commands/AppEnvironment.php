<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the flush view cache command
 */
namespace RDev\Framework\Console\Commands;
use RDev\Applications\Environments;
use RDev\Console\Commands;
use RDev\Console\Responses;

class AppEnvironment extends Commands\Command
{
    /** @var Environments\Environment The current environment */
    private $environment = null;

    /**
     * @param Commands\Commands $commands The list of registered commands
     * @param Environments\Environment $environment The current environment
     */
    public function __construct(Commands\Commands $commands, Environments\Environment $environment)
    {
        parent::__construct($commands);

        $this->environment = $environment;
    }

    /**
     * {@inheritdoc}
     */
    protected function define()
    {
        $this->setName("app:env")
            ->setDescription("Displays the current application environment");
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute(Responses\IResponse $response)
    {
        $response->writeln("<info>{$this->environment->getName()}</info>");
    }
}