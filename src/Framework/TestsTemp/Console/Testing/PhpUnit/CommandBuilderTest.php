<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Tests\Console\Testing\PhpUnit\Mocks;

use Opulence\Framework\Console\Testing\PhpUnit\CommandBuilder;
use Opulence\Framework\Console\Testing\PhpUnit\IntegrationTestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Tests the command builder
 */
class CommandBuilderTest extends \PHPUnit\Framework\TestCase
{
    /** @var IntegrationTestCase|MockObject The integration test to use in tests */
    private IntegrationTestCase $integrationTest;
    private CommandBuilder $commandBuilder;

    protected function setUp(): void
    {
        $this->integrationTest = $this->createMock(IntegrationTestCase::class);
        $this->integrationTest->expects($this->any())
            ->method('execute')
            ->willReturn($this->integrationTest);
        $this->commandBuilder = new CommandBuilder($this->integrationTest, 'foo');
    }

    public function testBuildingBasicCommand(): void
    {
        $this->integrationTest->expects($this->once())
            ->method('execute')
            ->with('foo', [], [], [], true);
        $this->assertSame($this->integrationTest, $this->commandBuilder->execute());
    }

    public function testBuildingCommandWithMultipleArguments(): void
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

    public function testBuildingCommandWithMultipleOptions(): void
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

    public function testBuildingCommandWithMultiplePromptAnswers(): void
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

    public function testBuildingCommandWithSingleArgument(): void
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

    public function testBuildingCommandWithSingleOption(): void
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

    public function testBuildingCommandWithSinglePromptAnswer(): void
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

    public function testBuildingCommandWithUnstyledOutput(): void
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
