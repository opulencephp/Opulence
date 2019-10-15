<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authentication\Tests\Tokens\JsonWebTokens\Verification;

use Opulence\Authentication\Tokens\JsonWebTokens\Verification\VerificationContext;
use Opulence\Authentication\Tokens\Signatures\ISigner;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Tests the verification context
 */
class VerificationContextTest extends TestCase
{
    private VerificationContext $context;
    /** @var ISigner|MockObject The signer to use in tests */
    private ISigner $signer;

    protected function setUp(): void
    {
        $this->signer = $this->createMock(ISigner::class);
        $this->context = new VerificationContext($this->signer);
    }

    public function testGettingDefaultValues(): void
    {
        $this->assertEquals([], $this->context->getAudience());
        $this->assertNull($this->context->getIssuer());
        $this->assertSame($this->signer, $this->context->getSigner());
        $this->assertNull($this->context->getSubject());
    }

    public function testSettingAudience(): void
    {
        $this->context->setAudience(['foo']);
        $this->assertEquals(['foo'], $this->context->getAudience());
    }

    public function testSettingIssuer(): void
    {
        $this->context->setIssuer('foo');
        $this->assertEquals('foo', $this->context->getIssuer());
    }

    public function testSettingSigner(): void
    {
        /** @var ISigner $signer */
        $signer = $this->createMock(ISigner::class);
        $this->context->setSigner($signer);
        $this->assertSame($signer, $this->context->getSigner());
    }

    public function testSettingSubject(): void
    {
        $this->context->setSubject('foo');
        $this->assertEquals('foo', $this->context->getSubject());
    }
}
