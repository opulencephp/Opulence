<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Authentication\Tests;

use Opulence\Authentication\Credentials\ICredential;
use Opulence\Authentication\IPrincipal;
use Opulence\Authentication\PrincipalTypes;
use Opulence\Authentication\Subject;

/**
 * Tests a subject
 */
class SubjectTest extends \PHPUnit\Framework\TestCase
{
    /** @var Subject The subject to use in tests */
    private $subject = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->subject = new Subject();
    }

    /**
     * Tests checking roles
     */
    public function testCheckingRoles()
    {
        /** @var IPrincipal|\PHPUnit_Framework_MockObject_MockObject $principal */
        $principal = $this->createMock(IPrincipal::class);
        $principal->expects($this->any())
            ->method('getRoles')
            ->willReturn(['foo']);
        $this->subject->addPrincipal($principal);
        $this->assertTrue($this->subject->hasRole('foo'));
        $this->assertFalse($this->subject->hasRole('bar'));
    }

    /**
     * Tests creating a subject with principals and credentials
     */
    public function testCreatingSubjectWithPrincipalsAndCredentials()
    {
        $principals = [$this->createMock(IPrincipal::class)];
        $credentials = [$this->createMock(ICredential::class)];
        $subject = new Subject($principals, $credentials);
        $this->assertEquals($principals, $subject->getPrincipals());
        $this->assertEquals($credentials, $subject->getCredentials());
    }

    /**
     * Tests an empty array is returned with no credentials
     */
    public function testEmptyArrayReturnedWithNoCredentials()
    {
        $this->assertEquals([], $this->subject->getCredentials());
    }

    /**
     * Tests an empty array is returned with no principals
     */
    public function testEmptyArrayReturnedWithNoPrincipals()
    {
        $this->assertEquals([], $this->subject->getPrincipals());
    }

    /**
     * Tests getting an added credential
     */
    public function testGettingAddedCredential()
    {
        /** @var ICredential|\PHPUnit_Framework_MockObject_MockObject $credential */
        $credential = $this->createMock(ICredential::class);
        $credential->method('getType')
            ->willReturn('foo');
        $this->subject->addCredential($credential);
        $this->assertSame($credential, $this->subject->getCredential('foo'));
        $this->assertEquals([$credential], $this->subject->getCredentials());
    }

    /**
     * Tests getting an added principal
     */
    public function testGettingAddedPrincipal()
    {
        /** @var IPrincipal|\PHPUnit_Framework_MockObject_MockObject $principal */
        $principal = $this->createMock(IPrincipal::class);
        $principal->method('getType')
            ->willReturn('foo');
        $this->subject->addPrincipal($principal);
        $this->assertSame($principal, $this->subject->getPrincipal('foo'));
        $this->assertEquals([$principal], $this->subject->getPrincipals());
    }

    /**
     * Tests getting the primary principal
     */
    public function testGettingPrimaryPrincipal()
    {
        /** @var IPrincipal|\PHPUnit_Framework_MockObject_MockObject $principal */
        $principal = $this->createMock(IPrincipal::class);
        $principal->method('getType')
            ->willReturn(PrincipalTypes::PRIMARY);
        $this->subject->addPrincipal($principal);
        $this->assertSame($principal, $this->subject->getPrimaryPrincipal());
        $this->assertSame($principal, $this->subject->getPrincipal(PrincipalTypes::PRIMARY));
        $this->assertEquals([$principal], $this->subject->getPrincipals());
    }

    /**
     * Tests getting roles
     */
    public function testGettingRoles()
    {
        /** @var IPrincipal|\PHPUnit_Framework_MockObject_MockObject $principal1 */
        $principal1 = $this->createMock(IPrincipal::class);
        $principal1->expects($this->any())
            ->method('getRoles')
            ->willReturn(['foo']);
        $principal1->expects($this->any())
            ->method('getType')
            ->willReturn('one');
        /** @var IPrincipal|\PHPUnit_Framework_MockObject_MockObject $principal2 */
        $principal2 = $this->createMock(IPrincipal::class);
        $principal2->expects($this->any())
            ->method('getRoles')
            ->willReturn(['bar']);
        $principal2->expects($this->any())
            ->method('getType')
            ->willReturn('two');
        $this->subject->addPrincipal($principal1);
        $this->subject->addPrincipal($principal2);
        $this->assertEquals(['foo', 'bar'], $this->subject->getRoles());
    }

    /**
     * Tests null is returned with no credential
     */
    public function testNullReturnedWithNoCredential()
    {
        $this->assertNull($this->subject->getCredential('foo'));
    }

    /**
     * Tests null is returned with no principal
     */
    public function testNullReturnedWithNoPrincipal()
    {
        $this->assertNull($this->subject->getPrincipal('foo'));
        $this->assertNull($this->subject->getPrimaryPrincipal());
    }
}
