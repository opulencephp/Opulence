<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the flush view cache command
 */
namespace RDev\Framework\Console\Commands;
use RDev\Applications\Environments\Environment;
use RDev\Console\Commands\Command;
use RDev\Console\Responses\IResponse;

class AppEnvironmentCommand extends Command
{
    /** @var Environment The current environment */
    private $environment = null;

    /**
     * @param Environment $environment The current environment
     */
    public function __construct(Environment $environment)
    {
        parent::__construct();

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
    protected function doExecute(IResponse $response)
    {
        $response->writeln("<info>{$this->environment->getName()}</info>");
    }
}