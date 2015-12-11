<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Testing\PhpUnit\Http;

use Opulence\Tests\Framework\Testing\PhpUnit\Http\Mocks\ApplicationTestCase as MockTestCase;

/**
 * Tests the HTTP application tester
 */
class ApplicationTestCaseTest extends \PHPUnit_Framework_TestCase
{
    /** @var MockTestCase The HTTP application to use in tests */
    private $testCase = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->testCase = new MockTestCase();
        $this->testCase->setUp();
    }

    /**
     * Tests that verbs return request builders
     */
    public function testVerbsReturnRequestBuilders()
    {
        $this->assertInstanceOf(RequestBuilder::class, $this->testCase->delete());
        $this->assertInstanceOf(RequestBuilder::class, $this->testCase->get());
        $this->assertInstanceOf(RequestBuilder::class, $this->testCase->head());
        $this->assertInstanceOf(RequestBuilder::class, $this->testCase->options());
        $this->assertInstanceOf(RequestBuilder::class, $this->testCase->patch());
        $this->assertInstanceOf(RequestBuilder::class, $this->testCase->post());
        $this->assertInstanceOf(RequestBuilder::class, $this->testCase->put());
    }
}