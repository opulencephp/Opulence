<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Testing\PhpUnit\Console\Assertions;

use LogicException;
use Opulence\Console\Responses\StreamResponse;
use Opulence\Console\StatusCodes;
use PHPUnit_Framework_TestCase;

/**
 * Defines the console response assertions
 */
class ResponseAssertions extends PHPUnit_Framework_TestCase
{
    /** @var StreamResponse The response stream */
    protected $response = null;
    /** @var int The status code */
    protected $statusCode = -1;

    /**
     * Gets the output of the previous command
     *
     * @return string The output
     * @throws LogicException Thrown if the response is not set
     */
    public function getOutput()
    {
        if ($this->response === null) {
            throw new LogicException("Must call call() before assertions");
        }

        rewind($this->response->getStream());

        return stream_get_contents($this->response->getStream());
    }

    /**
     * Asserts that the status code is an error
     *
     * @return $this For method chaining
     */
    public function isError()
    {
        $this->statusCodeEquals(StatusCodes::ERROR);

        return $this;
    }

    /**
     * Asserts that the status code is fatal
     *
     * @return $this For method chaining
     */
    public function isFatal()
    {
        $this->statusCodeEquals(StatusCodes::FATAL);

        return $this;
    }

    /**
     * Asserts that the status code is OK
     *
     * @return $this For method chaining
     */
    public function isOK()
    {
        $this->statusCodeEquals(StatusCodes::OK);

        return $this;
    }

    /**
     * Asserts that the status code is a warning
     *
     * @return $this For method chaining
     */
    public function isWarning()
    {
        $this->statusCodeEquals(StatusCodes::WARNING);

        return $this;
    }

    /**
     * Asserts that the output is an expected value
     *
     * @param string $expected The expected output
     * @return $this For method chaining
     */
    public function outputEquals($expected)
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
    public function setResponse(StreamResponse $response, $statusCode)
    {
        $this->response = $response;
        $this->statusCode = $statusCode;
    }

    /**
     * Asserts that the status code equals an expected value
     *
     * @param int $expected The expected status code
     * @return $this For method chaining
     */
    public function statusCodeEquals($expected)
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
}