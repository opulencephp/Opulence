<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Pipelines;

use Closure;

/**
 * Defines the interface for pipelines to implement
 */
interface IPipeline
{
    /**
     * Executes the pipeline
     *
     * @return mixed The output of the pipeline
     * @throws PipelineException Thrown if there was a problem sending the input down the pipeline
     */
    public function execute();

    /**
     * Sets the input to send through the pipeline
     *
     * @param mixed $input The input to send
     * @return self For method chaining
     */
    public function send($input) : self;

    /**
     * Sets the callback to call at the end of the pipeline
     *
     * @param callable $callback The callback to run after the pipeline
     *      It must accept the result of the pipeline as a parameter
     * @return self For method chaining
     */
    public function then(callable $callback) : self;

    /**
     * Sets the list of stages in the pipeline
     *
     * @param Closure[]|array $stages The list of stages in the pipeline
     * @param string|null $methodToCall Sets the method to call if the stages are a list of objects or class names
     * @return self For method chaining
     */
    public function through(array $stages, string $methodToCall = null) : self;
}
