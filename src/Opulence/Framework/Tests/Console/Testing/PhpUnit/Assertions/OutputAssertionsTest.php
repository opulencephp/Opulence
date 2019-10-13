<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Tests\Console\Testing\PhpUnit\Assertions;

use Aphiria\Console\Output\StreamOutput;
use Aphiria\Console\StatusCodes;
use Opulence\Framework\Console\Testing\PhpUnit\Assertions\OutputAssertions;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Tests the output assertions
 */
class OutputAssertionsTest extends \PHPUnit\Framework\TestCase
{
    private OutputAssertions $assertions;
    /** @var StreamOutput|MockObject The output to use in tests */
    private StreamOutput $mockOutput;

    protected function setUp(): void
    {
        $this->assertions = new OutputAssertions();
        $this->mockOutput = $this->getMockBuilder(StreamOutput::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testAssertStatusCodeIsError(): void
    {
        $this->assertions->setOutput($this->mockOutput, StatusCodes::ERROR);
        $this->assertSame($this->assertions, $this->assertions->isError());
    }

    public function testAssertStatusCodeIsFatal(): void
    {
        $this->assertions->setOutput($this->mockOutput, StatusCodes::FATAL);
        $this->assertSame($this->assertions, $this->assertions->isFatal());
    }

    public function testAssertStatusCodeIsOK(): void
    {
        $this->assertions->setOutput($this->mockOutput, StatusCodes::OK);
        $this->assertSame($this->assertions, $this->assertions->isOk());
    }

    public function testAssertStatusCodeIsWarning(): void
    {
        $this->assertions->setOutput($this->mockOutput, StatusCodes::WARNING);
        $this->assertSame($this->assertions, $this->assertions->isWarning());
    }

    public function testAssertingStatusCodeEquals(): void
    {
        $this->assertions->setOutput($this->mockOutput, StatusCodes::OK);
        $this->assertSame($this->assertions, $this->assertions->statusCodeEquals(StatusCodes::OK));
    }
}
