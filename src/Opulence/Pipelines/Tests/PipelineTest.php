<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Pipelines\Tests;

use Opulence\Pipelines\Pipeline;
use Opulence\Pipelines\PipelineException;
use Opulence\Pipelines\Tests\Mocks\Stage1;
use Opulence\Pipelines\Tests\Mocks\Stage2;

/**
 * Tests the pipeline
 */
class PipelineTest extends \PHPUnit\Framework\TestCase
{
    /** @var Pipeline The pipeline to use in tests */
    private $pipeline;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->pipeline = new Pipeline();
    }

    /**
     * Tests closures with callback
     */
    public function testClosuresWithCallback(): void
    {
        $stages = [
            function ($input, $next) {
                return $next($input . '1');
            },
            function ($input, $next) {
                return $next($input . '2');
            },
        ];
        $callback = function ($input) {
            return $input . '3';
        };
        $this->pipeline->send('input')
            ->through($stages)
            ->then($callback);
        $this->assertEquals('input123', $this->pipeline->execute());
    }

    /**
     * Tests that the methods are chainable
     */
    public function testMethodsAreChainable(): void
    {
        $this->assertSame($this->pipeline, $this->pipeline->send('foo'));
        $this->assertSame($this->pipeline, $this->pipeline->through([]));
        $this->assertSame($this->pipeline, $this->pipeline->then(function () {
        }));
    }

    /**
     * Tests multiple closure stages
     */
    public function testMultipleClosureStages(): void
    {
        $stages = [
            function ($input, $next) {
                return $next($input . '1');
            },
            function ($input, $next) {
                return $next($input . '2');
            },
        ];
        $this->pipeline->send('input')
            ->through($stages);
        $this->assertEquals('input12', $this->pipeline->execute());
    }

    /**
     * Tests multiple object stages
     */
    public function testMultipleObjectStages(): void
    {
        $stages = [new Stage1(), new Stage2()];
        $this->pipeline->send('input')
            ->through($stages, 'run');
        $this->assertEquals('input12', $this->pipeline->execute());
    }

    /**
     * Tests not setting a method to call
     */
    public function testNotSettingMethodToCall(): void
    {
        $this->expectException(PipelineException::class);
        $stages = [new Stage1()];
        $this->pipeline->send('input')
            ->through($stages)
            ->execute();
    }

    /**
     * Tests object stages with callback
     */
    public function testObjectStagesWithCallback(): void
    {
        $stages = [new Stage1(), new Stage2()];
        $callback = function ($input) {
            return $input . '3';
        };
        $this->pipeline->send('input')
            ->through($stages, 'run')
            ->then($callback);
        $this->assertEquals('input123', $this->pipeline->execute());
    }

    /**
     * Tests a single closure pipe
     */
    public function testSingleClosurePipe(): void
    {
        $stages = [
            function ($input, $next) {
                return $next($input . '1');
            }
        ];
        $this->pipeline->send('input')
            ->through($stages);
        $this->assertEquals('input1', $this->pipeline->execute());
    }

    /**
     * Tests a single object pipe
     */
    public function testSingleObjectPipe(): void
    {
        $stages = [new Stage1()];
        $this->pipeline->send('input')
            ->through($stages, 'run');
        $this->assertEquals('input1', $this->pipeline->execute());
    }

    /**
     * Tests a pipe that does not call next
     */
    public function testStageThatDoesNotCallNext(): void
    {
        $stages = [
            function ($input, $next) {
                return $input . '1';
            },
            function ($input, $next) {
                return $next($input . '2');
            }
        ];
        $this->pipeline->send('input')
            ->through($stages);
        $this->assertEquals('input1', $this->pipeline->execute());
    }

    /**
     * Tests a pipe that does not call next but has callback
     */
    public function testStageThatDoesNotCallNextButHasCallback(): void
    {
        $stages = [
            function ($input, $next) {
                return $input . '1';
            },
            function ($input, $next) {
                return $next($input . '2');
            }
        ];
        $callback = function ($input) {
            return $input . '3';
        };
        $this->pipeline->send('input')
            ->through($stages, 'run')
            ->then($callback);
        $this->assertEquals('input1', $this->pipeline->execute());
    }

    /**
     * Tests closure then object stages
     */
    public function testThenClosureThenObjectStages(): void
    {
        $stages = [
            function ($input, $next) {
                return $next($input . '3');
            },
            new Stage2()
        ];
        $this->pipeline->send('input')
            ->through($stages, 'run');
        $this->assertEquals('input32', $this->pipeline->execute());
    }
}
