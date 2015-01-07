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
    /**
     * {@inheritdoc}
     */
    public function execute(Responses\IResponse $response)
    {
        $response->writeln(<<<EOF
RDev Console
EOF
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function define()
    {
        $this->setName("about")
            ->setDescription("Describes the RDev console application");
    }
}