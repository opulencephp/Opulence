<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the pipeline
 */
namespace RDev\Pipelines;
use RDev\IoC;
use RDev\Tests\Pipelines\Mocks;

class PipelineTest extends \PHPUnit_Framework_TestCase
{
    /** @var IoC\Container The dependency injection container to use in tests */
    private $container = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->container = new IoC\Container();
    }

    /**
     * Tests class pipes with callback
     */
    public function testClassPipesWithCallback()
    {
        $pipes = ["RDev\\Tests\\Pipelines\\Mocks\\Pipe1", "RDev\\Tests\\Pipelines\\Mocks\\Pipe2"];
        $callback = function($input)
        {
            return $input . "3";
        };
        $pipeline = new Pipeline($this->container, $pipes, "run");
        $this->assertEquals("input123", $pipeline->send("input", $callback));
    }

    /**
     * Tests class then closure then object pipes
     */
    public function testClassThenClosureThenObjectPipes()
    {
        $pipes = [
            "RDev\\Tests\\Pipelines\\Mocks\\Pipe1",
            function($input, $next)
            {
                return $next($input . "3");
            },
            new Mocks\Pipe2()
        ];
        $pipeline = new Pipeline($this->container, $pipes, "run");
        $this->assertEquals("input132", $pipeline->send("input"));
    }

    /**
     * Tests closure then class pipes
     */
    public function testClosureThenClassPipes()
    {
        $pipes = [
            function($input, $next)
            {
                return $next($input . "1");
            },
            "RDev\\Tests\\Pipelines\\Mocks\\Pipe2"
        ];
        $pipeline = new Pipeline($this->container, $pipes, "run");
        $this->assertEquals("input12", $pipeline->send("input"));
    }

    /**
     * Tests closures with callback
     */
    public function testClosuresWithCallback()
    {
        $pipes = [
            function($input, $next)
            {
                return $next($input . "1");
            },
            function($input, $next)
            {
                return $next($input . "2");
            },
        ];
        $callback = function($input)
        {
            return $input . "3";
        };
        $pipeline = new Pipeline($this->container, $pipes);
        $this->assertEquals("input123", $pipeline->send("input", $callback));
    }

    /**
     * Tests that IoC exceptions are converted
     */
    public function testIoCExceptionsAreConverted()
    {
        $this->setExpectedException("RDev\\Pipelines\\PipelineException");
        $pipes = ["DoesNotExist"];
        $pipeline = new Pipeline($this->container, $pipes, "foo");
        $pipeline->send("input");
    }

    /**
     * Tests multiple class pipes
     */
    public function testMultipleClassPipes()
    {
        $pipes = ["RDev\\Tests\\Pipelines\\Mocks\\Pipe1", "RDev\\Tests\\Pipelines\\Mocks\\Pipe2"];
        $pipeline = new Pipeline($this->container, $pipes, "run");
        $this->assertEquals("input12", $pipeline->send("input"));
    }

    /**
     * Tests multiple closure pipes
     */
    public function testMultipleClosurePipes()
    {
        $pipes = [
            function($input, $next)
            {
                return $next($input . "1");
            },
            function($input, $next)
            {
                return $next($input . "2");
            },
        ];
        $pipeline = new Pipeline($this->container, $pipes);
        $this->assertEquals("input12", $pipeline->send("input"));
    }

    /**
     * Tests multiple object pipes
     */
    public function testMultipleObjectPipes()
    {
        $pipes = [new Mocks\Pipe1(), new Mocks\Pipe2()];
        $pipeline = new Pipeline($this->container, $pipes, "run");
        $this->assertEquals("input12", $pipeline->send("input"));
    }

    /**
     * Tests not setting a method to call
     */
    public function testNotSettingMethodToCall()
    {
        $this->setExpectedException("RDev\\Pipelines\\PipelineException");
        $pipes = ["RDev\\Tests\\Pipelines\\Mocks\\Pipe1"];
        $pipeline = new Pipeline($this->container, $pipes);
        $pipeline->send("input");
    }

    /**
     * Tests object pipes with callback
     */
    public function testObjectPipesWithCallback()
    {
        $pipes = [new Mocks\Pipe1(), new Mocks\Pipe2()];
        $callback = function($input)
        {
            return $input . "3";
        };
        $pipeline = new Pipeline($this->container, $pipes, "run");
        $this->assertEquals("input123", $pipeline->send("input", $callback));
    }

    /**
     * Tests a pipe that does not call next
     */
    public function testPipeThatDoesNotCallNext()
    {
        $pipes = [
            function($input, $next)
            {
                return $input . "1";
            },
            function($input, $next)
            {
                return $next($input . "2");
            }
        ];
        $pipeline = new Pipeline($this->container, $pipes);
        $this->assertEquals("input1", $pipeline->send("input"));
    }

    /**
     * Tests a pipe that does not call next but has callback
     */
    public function testPipeThatDoesNotCallNextButHasCallback()
    {
        $pipes = [
            function($input, $next)
            {
                return $input . "1";
            },
            function($input, $next)
            {
                return $next($input . "2");
            }
        ];
        $callback = function($input)
        {
            return $input . "3";
        };
        $pipeline = new Pipeline($this->container, $pipes);
        $this->assertEquals("input1", $pipeline->send("input", $callback));
    }

    /**
     * Tests a single class pipe
     */
    public function testSingleClassPipe()
    {
        $pipes = ["RDev\\Tests\\Pipelines\\Mocks\\Pipe1"];
        $pipeline = new Pipeline($this->container, $pipes, "run");
        $this->assertEquals("input1", $pipeline->send("input"));
    }

    /**
     * Tests a single closure pipe
     */
    public function testSingleClosurePipe()
    {
        $pipes = [
            function($input, $next)
            {
                return $next($input . "1");
            }
        ];
        $pipeline = new Pipeline($this->container, $pipes);
        $this->assertEquals("input1", $pipeline->send("input"));
    }

    /**
     * Tests a single object pipe
     */
    public function testSingleObjectPipe()
    {
        $pipes = [new Mocks\Pipe1()];
        $pipeline = new Pipeline($this->container, $pipes, "run");
        $this->assertEquals("input1", $pipeline->send("input"));
    }
}