<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the Composer update command
 */
namespace RDev\Framework\Console\Commands;
use RDev\Console\Commands;
use RDev\Console\Responses;
use RDev\Framework\Composer;

class ComposerUpdate extends Commands\Command
{
    /** @var Composer\Executable The executable wrapper */
    private $executable = null;

    /**
     * @param Composer\Executable $executable The Composer executable
     */
    public function __construct(Composer\Executable $executable)
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
    protected function doExecute(Responses\IResponse $response)
    {
        $response->write($this->executable->update());
        $response->write($this->executable->dumpAutoload("-o"));
    }
}