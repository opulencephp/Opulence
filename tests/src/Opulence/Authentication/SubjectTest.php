<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication;

use Opulence\Authentication\Credentials\ICredential;

/**
 * Tests a subject
 */
class SubjectTest extends \PHPUnit_Framework_TestCase
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
     * Tests creating a subject with principals and credentials
     */
    public function testCreatingSubjectWithPrincipalsAndCredentials()
    {
        $principals = [$this->getMock(IPrincipal::class)];
        $credentials = [$this->getMock(ICredential::class)];
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
        $credential = $this->getMock(ICredential::class);
        $credential->method("getType")
            ->willReturn("foo");
        $this->subject->addCredential($credential);
        $this->assertSame($credential, $this->subject->getCredential("foo"));
        $this->assertEquals([$credential], $this->subject->getCredentials());
    }

    /**
     * Tests getting an added principal
     */
    public function testGettingAddedPrincipal()
    {
        /** @var IPrincipal|\PHPUnit_Framework_MockObject_MockObject $principal */
        $principal = $this->getMock(IPrincipal::class);
        $principal->method("getType")
            ->willReturn("foo");
        $this->subject->addPrincipal($principal);
        $this->assertSame($principal, $this->subject->getPrincipal("foo"));
        $this->assertEquals([$principal], $this->subject->getPrincipals());
    }

    /**
     * Tests getting the primary principal
     */
    public function testGettingPrimaryPrincipal()
    {
        /** @var IPrincipal|\PHPUnit_Framework_MockObject_MockObject $principal */
        $principal = $this->getMock(IPrincipal::class);
        $principal->method("getType")
            ->willReturn(PrincipalTypes::PRIMARY);
        $this->subject->addPrincipal($principal);
        $this->assertSame($principal, $this->subject->getPrimaryPrincipal());
        $this->assertSame($principal, $this->subject->getPrincipal(PrincipalTypes::PRIMARY));
        $this->assertEquals([$principal], $this->subject->getPrincipals());
    }

    /**
     * Tests null is returned with no credential
     */
    public function testNullReturnedWithNoCredential()
    {
        $this->assertNull($this->subject->getCredential("foo"));
    }

    /**
     * Tests null is returned with no principal
     */
    public function testNullReturnedWithNoPrincipal()
    {
        $this->assertNull($this->subject->getPrincipal("foo"));
        $this->assertNull($this->subject->getPrimaryPrincipal());
    }
}