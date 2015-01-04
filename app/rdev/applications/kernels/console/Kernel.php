<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the console kernel
 */
namespace RDev\Applications\Kernels\Console;
use Monolog;
use RDev\Console\Input;

class Kernel
{
    /** @var Monolog\Logger The logger to use */
    private $logger = null;

    /**
     * @param Monolog\Logger $logger The logger to use
     */
    public function __construct(Monolog\Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Handles a console command
     *
     * @param Input\IInput $input The input to handle
     * @return int The status code
     */
    public function handle(Input\IInput $input = null)
    {
        // TODO:  Implement
    }
}