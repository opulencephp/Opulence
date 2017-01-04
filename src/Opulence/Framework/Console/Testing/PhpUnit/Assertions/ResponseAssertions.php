<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Console\Testing\PhpUnit\Assertions;

use LogicException;
use Opulence\Console\Responses\StreamResponse;
use Opulence\Console\StatusCodes;
use PHPUnit\Framework\TestCase;

/**
 * Defines the console response assertions
 */
class ResponseAssertions extends TestCase
{
    /** @var StreamResponse The response stream */
    protected $response = null;
    /** @var int The status code */
    protected $statusCode = -1;

    /**
     * Asserts that the status code is an error
     *
     * @return self For method chaining
     */
    public function isError() : self
    {
        $this->statusCodeEquals(StatusCodes::ERROR);

        return $this;
    }

    /**
     * Asserts that the status code is fatal
     *
     * @return self For method chaining
     */
    public function isFatal() : self
    {
        $this->statusCodeEquals(StatusCodes::FATAL);

        return $this;
    }

    /**
     * Asserts that the status code is OK
     *
     * @return self For method chaining
     */
    public function isOK() : self
    {
        $this->statusCodeEquals(StatusCodes::OK);

        return $this;
    }

    /**
     * Asserts that the status code is a warning
     *
     * @return self For method chaining
     */
    public function isWarning() : self
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
    public function outputEquals(string $expected) : self
    {
        $this->checkResponseIsSet();
        $this->assertEquals($expected, $this->getOutput());

        return $this;
    }

    /**
     * Sets the response
     *
     * @param StreamResponse $response The response
     * @param int $statusCode The status code
     */
    public function setResponse(StreamResponse $response, int $statusCode)
    {
        $this->response = $response;
        $this->statusCode = $statusCode;
    }

    /**
     * Asserts that the status code equals an expected value
     *
     * @param int $expected The expected status code
     * @return self For method chaining
     */
    public function statusCodeEquals(int $expected) : self
    {
        $this->checkResponseIsSet();
        $this->assertEquals($expected, $this->statusCode);

        return $this;
    }

    /**
     * Checks if the response was set
     * Useful for making sure the response was set before making any assertions on it
     */
    private function checkResponseIsSet()
    {
        if ($this->response === null) {
            $this->fail("Must call call() before assertions");
        }
    }

    /**
     * Gets the output of the previous command
     *
     * @return string The output
     * @throws LogicException Thrown if the response is not set
     */
    private function getOutput() : string
    {
        if ($this->response === null) {
            throw new LogicException("Must call call() before assertions");
        }

        rewind($this->response->getStream());

        return stream_get_contents($this->response->getStream());
    }
}
