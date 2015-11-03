<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Pipelines;

use Closure;

/**
 * Defines the interface for pipelines to implement
 */
interface IPipeline
{
    /**
     * Sends the output through the pipeline
     *
     * @param mixed $input The input to send
     * @param Closure $callback The callback to run after the pipeline
     *      It must accept the result of the pipeline as a parameter
     * @return mixed The output of the pipeline
     * @throws PipelineException Thrown if there was a problem sending the input down the pipeline
     */
    public function send($input, Closure $callback = null);

    /**
     * Sets the list of stages in the pipeline
     *
     * @param Closure[]|array $stages The list of stages in the pipeline
     * @param string $methodToCall Sets the method to call if the stages are a list of objects or class names
     */
    public function setStages(array $stages, $methodToCall = null);
}