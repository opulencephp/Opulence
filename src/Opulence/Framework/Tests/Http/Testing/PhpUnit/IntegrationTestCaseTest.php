<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Tests\Http\Testing\PhpUnit;

use Opulence\Framework\Http\Testing\PhpUnit\RequestBuilder;
use Opulence\Framework\Tests\Http\Testing\PhpUnit\Mocks\IntegrationTestCase as MockIntegrationTestCase;

/**
 * Tests the HTTP integration test
 */
class IntegrationTestCaseTest extends \PHPUnit\Framework\TestCase
{
    /** @var MockIntegrationTestCase The HTTP integration test to use in tests */
    private $integrationTest;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->integrationTest = new MockIntegrationTestCase();
        $this->integrationTest->setUp();
    }

    /**
     * Tests that verbs return request builders
     */
    public function testVerbsReturnRequestBuilders(): void
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
