<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Console\Testing\PhpUnit\Assertions;

use Aphiria\Console\Output\StreamOutput;
use Aphiria\Console\StatusCodes;
use LogicException;
use PHPUnit\Framework\TestCase;

/**
 * Defines the console output assertions
 */
class OutputAssertions extends TestCase
{
    /** @var StreamOutput The output stream */
    protected StreamOutput $output;
    /** @var int The status code */
    protected int $statusCode = -1;

    /**
     * Asserts that the status code is an error
     *
     * @return self For method chaining
     */
    public function isError(): self
    {
        $this->statusCodeEquals(StatusCodes::ERROR);

        return $this;
    }

    /**
     * Asserts that the status code is fatal
     *
     * @return self For method chaining
     */
    public function isFatal(): self
    {
        $this->statusCodeEquals(StatusCodes::FATAL);

        return $this;
    }

    /**
     * Asserts that the status code is OK
     *
     * @return self For method chaining
     */
    public function isOk(): self
    {
        $this->statusCodeEquals(StatusCodes::OK);

        return $this;
    }

    /**
     * Asserts that the status code is a warning
     *
     * @return self For method chaining
     */
    public function isWarning(): self
    {
        $this->statusCodeEquals(StatusCodes::WARNING);

        return $this;
    }

    /**
     * Asserts that the output is an expected value
     *
     * @param string $expected The expected output
     * @return self For method chaining
     */
    public function outputEquals(string $expected): self
    {
        $this->checkOutputIsSet();
        $this->assertEquals($expected, $this->getOutput());

        return $this;
    }

    /**
     * Sets the output
     *
     * @param StreamOutput $output The output
     * @param int $statusCode The status code
     */
    public function setOutput(StreamOutput $output, int $statusCode): void
    {
        $this->output = $output;
        $this->statusCode = $statusCode;
    }

    /**
     * Asserts that the status code equals an expected value
     *
     * @param int $expected The expected status code
     * @return self For method chaining
     */
    public function statusCodeEquals(int $expected): self
    {
        $this->checkOutputIsSet();
        $this->assertEquals($expected, $this->statusCode);

        return $this;
    }

    /**
     * Checks if the output was set
     * Useful for making sure the output was set before making any assertions on it
     */
    private function checkOutputIsSet(): void
    {
        if ($this->output === null) {
            $this->fail('Must call call() before assertions');
        }
    }

    /**
     * Gets the output of the previous command
     *
     * @return string The output
     * @throws LogicException Thrown if the output is not set
     */
    private function getOutput(): string
    {
        if ($this->output === null) {
            throw new LogicException('Must call call() before assertions');
        }

        rewind($this->output->getOutputStream());

        return stream_get_contents($this->output->getOutputStream());
    }
}
