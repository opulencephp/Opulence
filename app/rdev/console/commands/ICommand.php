<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the interface for console commands to implement
 */
namespace RDev\Console\Commands;
use RDev\Console\Requests;
use RDev\Console\Responses;

interface ICommand 
{
    /**
     * Executes the command
     *
     * @param Requests\IRequest $request The console request
     * @param Responses\IResponse $response The console response to write to
     * @return int|null Null or the status code of the command
     */
    public function execute(Requests\IRequest $request, Responses\IResponse $response);
}