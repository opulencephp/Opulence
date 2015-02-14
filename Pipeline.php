<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the pipeline
 */
namespace RDev\Pipelines;
use RDev\IoC;

class Pipeline implements IPipeline
{
    /** @var IoC\IContainer The dependency injection container to use */
    private $container;
    /** @var array The list of pipes to send input through */
    private $pipes = [];
    /** @var string The method to call if the pipes are not closures */
    private $methodToCall = null;

    /**
     * @param IoC\IContainer $container The dependency injection container to use
     * @param \Closure[]|array $pipes The list of pipes to send input through
     * @param string $methodToCall The method to call if the pipes are not closures
     */
    public function __construct(IoC\IContainer $container, array $pipes, $methodToCall = null)
    {
        $this->container = $container;
        $this->setPipes($pipes, $methodToCall);
    }

    /**
     * {@inheritdoc}
     */
    public function send($input, \Closure $callback = null)
    {
        return call_user_func(
            array_reduce(
                array_reverse($this->pipes),
                $this->createPipeCallback(),
                function ($input) use ($callback)
                {
                    if($callback === null)
                    {
                        return $input;
                    }

                    return call_user_func($callback, $input);
                }
            ),
            $input
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setPipes(array $pipes, $methodToCall = null)
    {
        $this->pipes = $pipes;
        $this->methodToCall = $methodToCall;
    }

    /**
     * Creates a callback for an individual pipe
     *
     * @return \Closure The callback
     * @throws PipelineException Thrown if there was a problem creating a pipe
     */
    private function createPipeCallback()
    {
        return function($pipes, $pipe)
        {
            return function($input) use ($pipes, $pipe)
            {
                if($pipe instanceof \Closure)
                {
                    return call_user_func($pipe, $input, $pipes);
                }
                else
                {
                    if($this->methodToCall === null)
                    {
                        throw new PipelineException("Method must not be null");
                    }

                    try
                    {
                        if(is_string($pipe))
                        {
                            $pipe = $this->container->makeShared($pipe);
                        }

                        if(!method_exists($pipe, $this->methodToCall))
                        {
                            throw new PipelineException(get_class($pipe) . "::{$this->methodToCall} does not exist");
                        }

                        return call_user_func_array(
                            [$pipe, $this->methodToCall],
                            [$input, $pipes]
                        );
                    }
                    catch(IoC\IoCException $ex)
                    {
                        throw new PipelineException("Failed to pipeline input: " . $ex->getMessage());
                    }
                }
            };
        };
    }
}