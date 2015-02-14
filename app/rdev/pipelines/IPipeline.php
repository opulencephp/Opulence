<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the interface for pipelines to implement
 */
namespace RDev\Pipelines;

interface IPipeline 
{
    /**
     * Sends the output through the pipeline
     *
     * @param mixed $input The input to send
     * @param callable $callback The callback to run after the pipeline
     *      It must accept the result of the pipeline as a parameter
     * @return mixed The output of the pipeline
     * @throws PipelineException Thrown if there was a problem sending the input down the pipeline
     */
    public function send($input, \Closure $callback = null);

    /**
     * Sets the list of pipes in the pipeline
     *
     * @param \Closure[]|array $pipes The list of pipes in the pipeline
     * @param string $methodToCall Sets the method to call if the pipes are a list of objects (and not closures)
     */
    public function setPipes(array $pipes, $methodToCall = null);
}