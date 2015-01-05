<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Mocks a command for use in testing
 */
namespace RDev\Tests\Console\Commands\Mocks;
use RDev\Console\Commands;
use RDev\Console\Requests;
use RDev\Console\Responses;

class Command implements Commands\ICommand
{
    /**
     * {@inheritdoc}
     */
    public function execute(Requests\IRequest $request, Responses\IResponse $response)
    {
        $response->write("foo");
    }
}