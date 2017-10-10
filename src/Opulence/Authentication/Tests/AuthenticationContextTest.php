<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Authentication\Tests;

use Opulence\Authentication\AuthenticationContext;
use Opulence\Authentication\AuthenticationStatusTypes;
use Opulence\Authentication\ISubject;

/**
 * Tests the authentication context
 */
class AuthenticationContextTest extends \PHPUnit\Framework\TestCase
{
    /** @var AuthenticationContext The context to use in tests */
    private $context = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->context = new AuthenticationContext();
    }

    /**
     * Tests checking if the user is authenticated
     */
    public function testCheckingIfAuthenticated()
    {
        $this->context->setStatus(AuthenticationStatusTypes::AUTHENTICATED);
        $this->assertTrue($this->context->isAuthenticated());
        $this->context->setStatus(AuthenticationStatusTypes::UNAUTHENTICATED);
        $this->assertFalse($this->context->isAuthenticated());
    }

    /**
     * Tests setting the status in the constructor
     */
    public function testSettingStatusInConstructor()
    {
        $context = new AuthenticationContext(null, 'foo');
        $this->assertEquals('foo', $context->getStatus());
    }

    /**
     * Tests setting the status in the setter
     */
    public function testSettingStatusInSetter()
    {
        $this->context->setStatus('foo');
        $this->assertEquals('foo', $this->context->getStatus());
    }

    /**
     * Tests setting the subject in the constructor
     */
    public function testSettingSubjectInConstructor()
    {
        /** @var ISubject $subject */
        $subject = $this->createMock(ISubject::class);
        $context = new AuthenticationContext($subject);
        $this->assertSame($subject, $context->getSubject());
    }

    /**
     * Tests setting the subject in the setter
     */
    public function testSettingSubjectInSetter()
    {
        /** @var ISubject $subject */
        $subject = $this->createMock(ISubject::class);
        $this->context->setSubject($subject);
        $this->assertSame($subject, $this->context->getSubject());
    }
}
