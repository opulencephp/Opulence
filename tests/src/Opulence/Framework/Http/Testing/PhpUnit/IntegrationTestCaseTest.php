<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Framework\Http\Testing\PhpUnit;

use Opulence\Tests\Framework\Http\Testing\PhpUnit\Mocks\IntegrationTestCase as MockIntegrationTestCase;

/**
 * Tests the HTTP integration test
 */
class IntegrationTestCaseTest extends \PHPUnit\Framework\TestCase
{
    /** @var MockIntegrationTestCase The HTTP integration test to use in tests */
    private $integrationTest = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->integrationTest = new MockIntegrationTestCase();
        $this->integrationTest->setUp();
    }

    /**
     * Tests that verbs return request builders
     */
    public function testVerbsReturnRequestBuilders()
    {
        $this->assertInstanceOf(RequestBuilder::class, $this->integrationTest->delete());
        $this->assertInstanceOf(RequestBuilder::class, $this->integrationTest->get());
        $this->assertInstanceOf(RequestBuilder::class, $this->integrationTest->head());
        $this->assertInstanceOf(RequestBuilder::class, $this->integrationTest->options());
        $this->assertInstanceOf(RequestBuilder::class, $this->integrationTest->patch());
        $this->assertInstanceOf(RequestBuilder::class, $this->integrationTest->post());
        $this->assertInstanceOf(RequestBuilder::class, $this->integrationTest->put());
    }
}
