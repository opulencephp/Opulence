<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the Composer dump autoload command
 */
namespace RDev\Framework\Console\Commands;
use RDev\Console\Commands;
use RDev\Console\Responses;
use RDev\Framework\Composer;

class ComposerDumpAutoload extends Commands\Command
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
        $this->setName("composer:dump-autoload")
            ->setDescription("Dumps the Composer autoload");
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute(Responses\IResponse $response)
    {
        $response->write($this->executable->dumpAutoload("-o"));
    }
}