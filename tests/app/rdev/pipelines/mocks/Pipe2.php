<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a pipeline pipe
 */
namespace RDev\Tests\Pipelines\Mocks;

class Pipe2
{
    /**
     * Runs the callback on the input
     *
     * @param mixed $input The input
     * @param \Closure $next The next closure
     * @return string The result of the pipe
     */
    public function run($input, \Closure $next)
    {
        return $next($input . "2");
    }
}