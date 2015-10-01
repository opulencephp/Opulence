<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the pipeline
 */
namespace Opulence\Pipelines;

use Opulence\IoC\Container;
use Opulence\Tests\Pipelines\Mocks\Stage1;
use Opulence\Tests\Pipelines\Mocks\Stage2;

class PipelineTest extends \PHPUnit_Framework_TestCase
{
    /** @var Container The dependency injection container to use in tests */
    private $container = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->container = new Container();
    }

    /**
     * Tests class stages with callback
     */
    public function testClassStagesWithCallback()
    {
        $stages = [Stage1::class, Stage2::class];
        $callback = function ($input)
        {
            return $input . "3";
        };
        $pipeline = new Pipeline($this->container, $stages, "run");
        $this->assertEquals("input123", $pipeline->send("input", $callback));
    }

    /**
     * Tests class then closure then object stages
     */
    public function testClassThenClosureThenObjectStages()
    {
        $stages = [
            Stage1::class,
            function ($input, $next)
            {
                return $next($input . "3");
            },
            new Stage2()
        ];
        $pipeline = new Pipeline($this->container, $stages, "run");
        $this->assertEquals("input132", $pipeline->send("input"));
    }

    /**
     * Tests closure then class stages
     */
    public function testClosureThenClassStages()
    {
        $stages = [
            function ($input, $next)
            {
                return $next($input . "1");
            },
            Stage2::class
        ];
        $pipeline = new Pipeline($this->container, $stages, "run");
        $this->assertEquals("input12", $pipeline->send("input"));
    }

    /**
     * Tests closures with callback
     */
    public function testClosuresWithCallback()
    {
        $stages = [
            function ($input, $next)
            {
                return $next($input . "1");
            },
            function ($input, $next)
            {
                return $next($input . "2");
            },
        ];
        $callback = function ($input)
        {
            return $input . "3";
        };
        $pipeline = new Pipeline($this->container, $stages);
        $this->assertEquals("input123", $pipeline->send("input", $callback));
    }

    /**
     * Tests that IoC exceptions are converted
     */
    public function testIoCExceptionsAreConverted()
    {
        $this->setExpectedException(PipelineException::class);
        $stages = ["DoesNotExist"];
        $pipeline = new Pipeline($this->container, $stages, "foo");
        $pipeline->send("input");
    }

    /**
     * Tests multiple class stages
     */
    public function testMultipleClassStages()
    {
        $stages = [Stage1::class, Stage2::class];
        $pipeline = new Pipeline($this->container, $stages, "run");
        $this->assertEquals("input12", $pipeline->send("input"));
    }

    /**
     * Tests multiple closure stages
     */
    public function testMultipleClosureStages()
    {
        $stages = [
            function ($input, $next)
            {
                return $next($input . "1");
            },
            function ($input, $next)
            {
                return $next($input . "2");
            },
        ];
        $pipeline = new Pipeline($this->container, $stages);
        $this->assertEquals("input12", $pipeline->send("input"));
    }

    /**
     * Tests multiple object stages
     */
    public function testMultipleObjectStages()
    {
        $stages = [new Stage1(), new Stage2()];
        $pipeline = new Pipeline($this->container, $stages, "run");
        $this->assertEquals("input12", $pipeline->send("input"));
    }

    /**
     * Tests not setting a method to call
     */
    public function testNotSettingMethodToCall()
    {
        $this->setExpectedException(PipelineException::class);
        $stages = [Stage1::class];
        $pipeline = new Pipeline($this->container, $stages);
        $pipeline->send("input");
    }

    /**
     * Tests object stages with callback
     */
    public function testObjectStagesWithCallback()
    {
        $stages = [new Stage1(), new Stage2()];
        $callback = function ($input)
        {
            return $input . "3";
        };
        $pipeline = new Pipeline($this->container, $stages, "run");
        $this->assertEquals("input123", $pipeline->send("input", $callback));
    }

    /**
     * Tests a single class pipe
     */
    public function testSingleClassPipe()
    {
        $stages = [Stage1::class];
        $pipeline = new Pipeline($this->container, $stages, "run");
        $this->assertEquals("input1", $pipeline->send("input"));
    }

    /**
     * Tests a single closure pipe
     */
    public function testSingleClosurePipe()
    {
        $stages = [
            function ($input, $next)
            {
                return $next($input . "1");
            }
        ];
        $pipeline = new Pipeline($this->container, $stages);
        $this->assertEquals("input1", $pipeline->send("input"));
    }

    /**
     * Tests a single object pipe
     */
    public function testSingleObjectPipe()
    {
        $stages = [new Stage1()];
        $pipeline = new Pipeline($this->container, $stages, "run");
        $this->assertEquals("input1", $pipeline->send("input"));
    }

    /**
     * Tests a pipe that does not call next
     */
    public function testStageThatDoesNotCallNext()
    {
        $stages = [
            function ($input, $next)
            {
                return $input . "1";
            },
            function ($input, $next)
            {
                return $next($input . "2");
            }
        ];
        $pipeline = new Pipeline($this->container, $stages);
        $this->assertEquals("input1", $pipeline->send("input"));
    }

    /**
     * Tests a pipe that does not call next but has callback
     */
    public function testStageThatDoesNotCallNextButHasCallback()
    {
        $stages = [
            function ($input, $next)
            {
                return $input . "1";
            },
            function ($input, $next)
            {
                return $next($input . "2");
            }
        ];
        $callback = function ($input)
        {
            return $input . "3";
        };
        $pipeline = new Pipeline($this->container, $stages);
        $this->assertEquals("input1", $pipeline->send("input", $callback));
    }
}