<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Authentication\Tests\Tokens\JsonWebTokens\Verification;

use Opulence\Authentication\Tokens\JsonWebTokens\Verification\VerificationContext;
use Opulence\Authentication\Tokens\Signatures\ISigner;

/**
 * Tests the verification context
 */
class VerificationContextTest extends \PHPUnit\Framework\TestCase
{
    /** @var VerificationContext The context to use in tests */
    private $context = null;
    /** @var ISigner|\PHPUnit_Framework_MockObject_MockObject The signer to use in tests */
    private $signer = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->signer = $this->createMock(ISigner::class);
        $this->context = new VerificationContext($this->signer);
    }

    /**
     * Test getting the default values
     */
    public function testGettingDefaultValues()
    {
        $this->assertEquals([], $this->context->getAudience());
        $this->assertNull($this->context->getIssuer());
        $this->assertSame($this->signer, $this->context->getSigner());
        $this->assertNull($this->context->getSubject());
    }

    /**
     * Tests setting an audience
     */
    public function testSettingAudience()
    {
        $this->context->setAudience(['foo']);
        $this->assertEquals(['foo'], $this->context->getAudience());
    }

    /**
     * Tests setting a issuer
     */
    public function testSettingIssuer()
    {
        $this->context->setIssuer('foo');
        $this->assertEquals('foo', $this->context->getIssuer());
    }

    /**
     * Tests setting a signer
     */
    public function testSettingSigner()
    {
        /** @var ISigner $signer */
        $signer = $this->createMock(ISigner::class);
        $this->context->setSigner($signer);
        $this->assertSame($signer, $this->context->getSigner());
    }

    /**
     * Tests setting a subject
     */
    public function testSettingSubject()
    {
        $this->context->setSubject('foo');
        $this->assertEquals('foo', $this->context->getSubject());
    }
}
