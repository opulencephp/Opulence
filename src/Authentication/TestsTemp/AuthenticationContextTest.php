<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authentication\TestsTemp;

use Opulence\Authentication\AuthenticationContext;
use Opulence\Authentication\AuthenticationStatusTypes;
use Opulence\Authentication\ISubject;

/**
 * Tests the authentication context
 */
class AuthenticationContextTest extends \PHPUnit\Framework\TestCase
{
    private AuthenticationContext $context;

    protected function setUp(): void
    {
        $this->context = new AuthenticationContext();
    }

    public function testCheckingIfAuthenticated(): void
    {
        $this->context->setStatus(AuthenticationStatusTypes::AUTHENTICATED);
        $this->assertTrue($this->context->isAuthenticated());
        $this->context->setStatus(AuthenticationStatusTypes::UNAUTHENTICATED);
        $this->assertFalse($this->context->isAuthenticated());
    }

    public function testSettingStatusInConstructor(): void
    {
        $context = new AuthenticationContext(null, 'foo');
        $this->assertEquals('foo', $context->getStatus());
    }

    public function testSettingStatusInSetter(): void
    {
        $this->context->setStatus('foo');
        $this->assertEquals('foo', $this->context->getStatus());
    }

    public function testSettingSubjectInConstructor(): void
    {
        /** @var ISubject $subject */
        $subject = $this->createMock(ISubject::class);
        $context = new AuthenticationContext($subject);
        $this->assertSame($subject, $context->getSubject());
    }

    public function testSettingSubjectInSetter(): void
    {
        /** @var ISubject $subject */
        $subject = $this->createMock(ISubject::class);
        $this->context->setSubject($subject);
        $this->assertSame($subject, $this->context->getSubject());
    }
}
