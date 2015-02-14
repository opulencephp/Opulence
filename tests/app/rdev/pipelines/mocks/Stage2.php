<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a pipeline stage
 */
namespace RDev\Tests\Pipelines\Mocks;

class Stage2
{
    /**
     * Runs the callback on the input
     *
     * @param mixed $input The input
     * @param \Closure $next The next closure
     * @return string The result of the stage
     */
    public function run($input, \Closure $next)
    {
        return $next($input . "2");
    }
}