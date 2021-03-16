<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Authentication\Tests\Tokens\JsonWebTokens;

use DateTimeImmutable;
use InvalidArgumentException;
use Opulence\Authentication\Tokens\JsonWebTokens\JwtPayload;

/**
 * Tests the JWT payload
 */
class JwtPayloadTest extends \PHPUnit\Framework\TestCase
{
    /** @var JwtPayload The payload to use in tests */
    private $payload = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->payload = new JwtPayload();
    }

    /**
     * Tests getting all values
     */
    public function testGettingAllValues()
    {
        $claims = [
            'iss' => null,
            'sub' => null,
            'aud' => null,
            'exp' => null,
            'nbf' => null,
            'iat' => null,
            'jti' => $this->payload->getId()
        ];
        $this->assertEquals($claims, $this->payload->getAll());
        $this->payload->setIssuer('foo');
        $claims['jti'] = $this->payload->getId();
        $claims['iss'] = 'foo';
        $this->assertEquals($claims, $this->payload->getAll());
        $this->payload->setSubject('bar');
        $claims['jti'] = $this->payload->getId();
        $claims['sub'] = 'bar';
        $this->assertEquals($claims, $this->payload->getAll());
        $this->payload->setAudience('baz');
        $claims['jti'] = $this->payload->getId();
        $claims['aud'] = 'baz';
        $this->assertEquals($claims, $this->payload->getAll());
        $validTo = new DateTimeImmutable();
        $this->payload->setValidTo($validTo);
        $claims['jti'] = $this->payload->getId();
        $claims['exp'] = $validTo->getTimestamp();
        $this->assertEquals($claims, $this->payload->getAll());
        $validFrom = new DateTimeImmutable();
        $this->payload->setValidFrom($validFrom);
        $claims['jti'] = $this->payload->getId();
        $claims['nbf'] = $validFrom->getTimestamp();
        $this->assertEquals($claims, $this->payload->getAll());
        $issuedAt = new DateTimeImmutable();
        $this->payload->setIssuedAt($issuedAt);
        $claims['jti'] = $this->payload->getId();
        $claims['iat'] = $issuedAt->getTimestamp();
        $this->assertEquals($claims, $this->payload->getAll());
        $this->payload->setId('blah');
        $claims['jti'] = 'blah';
        $this->assertEquals($claims, $this->payload->getAll());
        $this->payload->add('name', 'dave');
        $claims['name'] = 'dave';
        $this->assertEquals($claims, $this->payload->getAll());
    }

    /**
     * Tests getting the audience
     */
    public function testGettingAudience()
    {
        $this->assertNull($this->payload->getAudience());
        $this->payload->setAudience('foo');
        $this->assertEquals('foo', $this->payload->getAudience());
    }

    /**
     * Tests getting the encoded string
     */
    public function testGettingEncodedString()
    {
        $this->payload->setIssuer('foo');
        $claims = [
            'iss' => 'foo',
            'sub' => null,
            'aud' => null,
            'exp' => null,
            'nbf' => null,
            'iat' => null,
            'jti' => $this->payload->getId()
        ];
        $this->assertEquals(
            rtrim(strtr(base64_encode(json_encode($claims)), '+/', '-_'), '='),
            $this->payload->encode()
        );
        $this->payload->add('bar', 'baz');
        $claims['jti'] = $this->payload->getId();
        $claims['bar'] = 'baz';
        $this->assertEquals(
            rtrim(strtr(base64_encode(json_encode($claims)), '+/', '-_'), '='),
            $this->payload->encode()
        );
    }

    /**
     * Tests getting the Id
     */
    public function testGettingId()
    {
        $this->assertNotEmpty($this->payload->get('jti'));
        $this->assertNotEmpty($this->payload->getId());
    }

    /**
     * Tests getting the issued at
     */
    public function testGettingIssuedAt()
    {
        $this->assertNull($this->payload->getIssuedAt());
        $date = new DateTimeImmutable();
        $this->payload->setIssuedAt($date);
        $this->assertSame($date, $this->payload->getIssuedAt());
    }

    /**
     * Tests getting the issuer
     */
    public function testGettingIssuer()
    {
        $this->assertNull($this->payload->getIssuer());
        $this->payload->setIssuer('foo');
        $this->assertEquals('foo', $this->payload->getIssuer());
    }

    /**
     * Tests getting the subject
     */
    public function testGettingSubject()
    {
        $this->assertNull($this->payload->getSubject());
        $this->payload->setSubject('foo');
        $this->assertEquals('foo', $this->payload->getSubject());
    }

    /**
     * Tests getting the valid from
     */
    public function testGettingValidFrom()
    {
        $this->assertNull($this->payload->getValidFrom());
        $date = new DateTimeImmutable();
        $this->payload->setValidFrom($date);
        $this->assertSame($date, $this->payload->getValidFrom());
    }

    /**
     * Tests getting the valid to
     */
    public function testGettingValidTo()
    {
        $this->assertNull($this->payload->getValidTo());
        $date = new DateTimeImmutable();
        $this->payload->setValidTo($date);
        $this->assertSame($date, $this->payload->getValidTo());
    }

    /**
     * Tests getting the value for an extra claim
     */
    public function testGettingValue()
    {
        $this->assertNull($this->payload->get('foo'));
        $this->payload->add('foo', 'bar');
        $this->assertEquals('bar', $this->payload->get('foo'));
        $this->payload->add('foo', 'baz');
        $this->assertEquals('baz', $this->payload->get('foo'));
    }

    /**
     * Tests that the Id changes with new claims
     */
    public function testIdChangesWithNewClaims()
    {
        $jti1 = $this->payload->getId();
        $this->payload->add('foo', 'bar');
        $jti2 = $this->payload->getId();
        $this->payload->add('baz', 'blah');
        $jti3 = $this->payload->getId();
        $this->assertNotEquals($jti1, $jti2);
        $this->assertNotEquals($jti2, $jti3);
    }

    /**
     * Tests that the Id does not change with new claims when manually set
     */
    public function testIdDoesNotChangeWithNewClaimsWhenManuallySet()
    {
        $this->payload->setId('theJti');
        $jti1 = $this->payload->getId();
        $this->payload->add('foo', 'bar');
        $jti2 = $this->payload->getId();
        $this->payload->add('baz', 'blah');
        $jti3 = $this->payload->getId();
        $this->assertEquals('theJti', $this->payload->getId());
        $this->assertEquals('theJti', $this->payload->get('jti'));
        $this->assertEquals($jti1, $jti2);
        $this->assertEquals($jti2, $jti3);
    }

    /**
     * Tests setting the audience with an invalid type throws an exception
     */
    public function testInvalidAudienceThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->payload->setAudience(new DateTimeImmutable());
    }

    /**
     * Tests setting the Id
     */
    public function testSettingId()
    {
        $this->payload->setId('foo');
        $this->assertEquals('foo', $this->payload->get('jti'));
        $this->assertEquals('foo', $this->payload->getId());
    }

    /**
     * Tests setting multiple audiences
     */
    public function testSettingMultipleAudiences()
    {
        $this->payload->setAudience(['foo', 'bar']);
        $this->assertEquals(['foo', 'bar'], $this->payload->getAudience());
    }
}
