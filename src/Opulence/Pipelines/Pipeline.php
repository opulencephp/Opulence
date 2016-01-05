<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Pipelines;

use Closure;
use Opulence\Ioc\IContainer;
use Opulence\Ioc\IocException;

/**
 * Defines the pipeline
 */
class Pipeline implements IPipeline
{
    /** @var IContainer The dependency injection container to use */
    private $container;
    /** @var mixed The input to send through the pipeline */
    private $input = null;
    /** @var array The list of stages to send input through */
    private $stages = [];
    /** @var string The method to call if the stages are not closures */
    private $methodToCall = null;
    /** @var callable The callback to execute at the end */
    private $callback = null;

    /**
     * @param IContainer $container The dependency injection container to use
     */
    public function __construct(IContainer $container)
    {
        $this->container = $container;
    }

    public function execute()
    {
        return call_user_func(
            array_reduce(
                array_reverse($this->stages),
                $this->createStageCallback(),
                function ($input) {
                    if ($this->callback === null) {
                        return $input;
                    }

                    return call_user_func($this->callback, $input);
                }
            ),
            $this->input
        );
    }

    /**
     * @inheritdoc
     */
    public function send($input)
    {
        $this->input = $input;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function then(callable $callback)
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function through(array $stages, $methodToCall = null)
    {
        $this->stages = $stages;
        $this->methodToCall = $methodToCall;

        return $this;
    }

    /**
     * Creates a callback for an individual stage
     *
     * @return Closure The callback
     * @throws PipelineException Thrown if there was a problem creating a stage
     */
    private function createStageCallback()
    {
        return function ($stages, $stage) {
            return function ($input) use ($stages, $stage) {
                if ($stage instanceof Closure) {
                    return call_user_func($stage, $input, $stages);
                } else {
                    if ($this->methodToCall === null) {
                        throw new PipelineException("Method must not be null");
                    }

                    try {
                        if (is_string($stage)) {
                            $stage = $this->container->makeShared($stage);
                        }

                        if (!method_exists($stage, $this->methodToCall)) {
                            throw new PipelineException(get_class($stage) . "::{$this->methodToCall} does not exist");
                        }

                        return call_user_func_array(
                            [$stage, $this->methodToCall],
                            [$input, $stages]
                        );
                    } catch (IocException $ex) {
                        throw new PipelineException("Failed to pipeline input", 0, $ex);
                    }
                }
            };
        };
    }
}