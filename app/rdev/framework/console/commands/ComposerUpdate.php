<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the update command
 */
namespace RDev\Framework\Console\Commands;
use RDev\Console\Commands;
use RDev\Console\Responses;
use RDev\Framework\Composer;

class ComposerUpdate extends Commands\Command
{
    /** @var Composer\Composer The Composer wrapper */
    private $composer = null;

    /**
     * @param Composer\Composer $composer The Composer wrapper
     */
    public function __construct(Composer\Composer $composer)
    {
        parent::__construct();

        $this->composer = $composer;
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
        $response->write($this->composer->update());
        $response->write($this->composer->dumpAutoload("-o"));
    }
}