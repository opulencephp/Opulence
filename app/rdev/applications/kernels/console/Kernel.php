<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the console kernel
 */
namespace RDev\Applications\Kernels\Console;
use Monolog;
use RDev\Console\Commands;
use RDev\Console\Requests;
use RDev\Console\Responses;

class Kernel
{
    /** @var Commands\Commands The list of commands to choose from */
    private $commands = null;
    /** @var Monolog\Logger The logger to use */
    private $logger = null;

    /**
     * @param Commands\Commands $commands The list of commands to choose from
     * @param Monolog\Logger $logger The logger to use
     */
    public function __construct(Commands\Commands $commands, Monolog\Logger $logger)
    {
        $this->commands = $commands;
        $this->logger = $logger;
    }

    /**
     * Handles a console command
     *
     * @param Requests\IRequest $request The request to handle
     * @param Responses\IResponse $response The response to write to
     * @return int The status code
     */
    public function handle(Requests\IRequest $request = null, Responses\IResponse $response = null)
    {
        if($request === null)
        {
            $request = new Requests\Argv();
        }

        if($response === null)
        {
            $response = new Responses\Console();
        }
    }
}