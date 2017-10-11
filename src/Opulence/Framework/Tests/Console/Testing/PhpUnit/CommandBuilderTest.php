<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Framework\Tests\Console\Testing\PhpUnit\Mocks;

use Opulence\Framework\Console\Testing\PhpUnit\CommandBuilder;
use Opulence\Framework\Console\Testing\PhpUnit\IntegrationTestCase;

/**
 * Tests the command builder
 */
class CommandBuilderTest extends \PHPUnit\Framework\TestCase
{
    /** @var IntegrationTestCase|\PHPUnit_Framework_MockObject_MockObject The integration test to use in tests */
    private $integrationTest = null;
    /** @var CommandBuilder The command builder to use in tests */
    private $commandBuilder = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->integrationTest = $this->createMock(IntegrationTestCase::class);
        $this->integrationTest->expects($this->any())
            ->method('execute')
            ->willReturn($this->integrationTest);
        $this->commandBuilder = new CommandBuilder($this->integrationTest, 'foo');
    }

    /**
     * Tests building a basic command
     */
    public function testBuildingBasicCommand()
    {
        $this->integrationTest->expects($this->once())
            ->method('execute')
            ->with('foo', [], [], [], true);
        $this->assertSame($this->integrationTest, $this->commandBuilder->execute());
    }

    /**
     * Tests building a command with multiple arguments
     */
    public function testBuildingCommandWithMultipleArguments()
    {
        $this->integrationTest->expects($this->once())
            ->method('execute')
            ->with('foo', ['bar'], [], [], true);
        $this->assertSame(
            $this->integrationTest,
            $this->commandBuilder->withArguments(['bar'])
                ->execute()
        );
    }

    /**
     * Tests building a command with multiple options
     */
    public function testBuildingCommandWithMultipleOptions()
    {
        $this->integrationTest->expects($this->once())
            ->method('execute')
            ->with('foo', [], ['-y'], [], true);
        $this->assertSame(
            $this->integrationTest,
            $this->commandBuilder->withOptions(['-y'])
                ->execute()
        );
    }

    /**
     * Tests building a command with multiple prompt answers
     */
    public function testBuildingCommandWithMultiplePromptAnswers()
    {
        $this->integrationTest->expects($this->once())
            ->method('execute')
            ->with('foo', [], [], ['bar'], true);
        $this->assertSame(
            $this->integrationTest,
            $this->commandBuilder->withAnswers(['bar'])
                ->execute()
        );
    }

    /**
     * Tests building a command with a single argument
     */
    public function testBuildingCommandWithSingleArgument()
    {
        $this->integrationTest->expects($this->once())
            ->method('execute')
            ->with('foo', ['bar'], [], [], true);
        $this->assertSame(
            $this->integrationTest,
            $this->commandBuilder->withArguments('bar')
                ->execute()
        );
    }

    /**
     * Tests building a command with a single option
     */
    public function testBuildingCommandWithSingleOption()
    {
        $this->integrationTest->expects($this->once())
            ->method('execute')
            ->with('foo', [], ['-y'], [], true);
        $this->assertSame(
            $this->integrationTest,
            $this->commandBuilder->withOptions('-y')
                ->execute()
        );
    }

    /**
     * Tests building a command with a single prompt answer
     */
    public function testBuildingCommandWithSinglePromptAnswer()
    {
        $this->integrationTest->expects($this->once())
            ->method('execute')
            ->with('foo', [], [], ['bar'], true);
        $this->assertSame(
            $this->integrationTest,
            $this->commandBuilder->withAnswers('bar')
                ->execute()
        );
    }

    /**
     * Tests building a command with an unstyled response
     */
    public function testBuildingCommandWithUnstyledResponse()
    {
        $this->integrationTest->expects($this->once())
            ->method('execute')
            ->with('foo', [], [], [], false);
        $this->assertSame(
            $this->integrationTest,
            $this->commandBuilder->withStyle(false)
                ->execute()
        );
    }
}
