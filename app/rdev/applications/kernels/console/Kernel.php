<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the console kernel
 */
namespace RDev\Applications\Kernels\Console;
use Monolog;
use RDev\Console\Commands;
use RDev\Console\Input;
use RDev\Console\Output;

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
     * @param Input\IInput $input The input to handle
     * @param Output\IOutput $output The output to write to
     * @return int The status code
     */
    public function handle(Input\IInput $input = null, Output\IOutput $output = null)
    {
        if($input === null)
        {
            $input = new Input\Argv();
        }

        if($output === null)
        {
            $output = new Output\Console();
        }
    }
}