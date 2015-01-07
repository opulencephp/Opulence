<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the about command
 */
namespace RDev\Console\Commands;
use RDev\Console\Responses;

class About extends Command
{
    public function __construct()
    {
        parent::__construct("about", "Describes the RDev console application");
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Responses\IResponse $response)
    {
        $response->write(<<<EOF
RDev Console
EOF
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function setArgumentsAndOptions()
    {
        // Don't do anything
    }
}