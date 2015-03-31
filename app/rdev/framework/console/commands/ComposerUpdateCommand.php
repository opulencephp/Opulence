<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the Composer update command
 */
namespace RDev\Framework\Console\Commands;
use RDev\Console\Commands\Command;
use RDev\Console\Responses\IResponse;
use RDev\Framework\Composer\Executable;

class ComposerUpdateCommand extends Command
{
    /** @var Executable The executable wrapper */
    private $executable = null;

    /**
     * @param Executable $executable The Composer executable
     */
    public function __construct(Executable $executable)
    {
        parent::__construct();

        $this->executable = $executable;
    }

    /**
     * {@inheritdoc}
     */
    protected function define()
    {
        $this->setName("composer:update")
            ->setDescription("Updates any Composer dependencies");
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute(IResponse $response)
    {
        $response->write($this->executable->update());
        $this->commandCollection->call("composer:dump-autoload", $response);
    }
}