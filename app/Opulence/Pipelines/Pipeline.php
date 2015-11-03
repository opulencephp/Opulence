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
use Opulence\Ioc\IContainer;
use Opulence\Ioc\IocException;

/**
 * Defines the pipeline
 */
class Pipeline implements IPipeline
{
    /** @var IContainer The dependency injection container to use */
    private $container;
    /** @var array The list of stages to send input through */
    private $stages = [];
    /** @var string The method to call if the stages are not closures */
    private $methodToCall = null;

    /**
     * @param IContainer $container The dependency injection container to use
     * @param Closure[]|array $stages The list of stages to send input through
     * @param string $methodToCall The method to call if the pipes are not closures
     */
    public function __construct(IContainer $container, array $stages, $methodToCall = null)
    {
        $this->container = $container;
        $this->setStages($stages, $methodToCall);
    }

    /**
     * @inheritdoc
     */
    public function send($input, Closure $callback = null)
    {
        return call_user_func(
            array_reduce(
                array_reverse($this->stages),
                $this->createStageCallback(),
                function ($input) use ($callback) {
                    if ($callback === null) {
                        return $input;
                    }

                    return call_user_func($callback, $input);
                }
            ),
            $input
        );
    }

    /**
     * @inheritdoc
     */
    public function setStages(array $stages, $methodToCall = null)
    {
        $this->stages = $stages;
        $this->methodToCall = $methodToCall;
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
                        throw new PipelineException("Failed to pipeline input: " . $ex->getMessage());
                    }
                }
            };
        };
    }
}