<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authentication\Tests\Tokens\JsonWebTokens;

use DateTimeImmutable;
use InvalidArgumentException;
use Opulence\Authentication\Tokens\JsonWebTokens\JwtPayload;

/**
 * Tests the JWT payload
 */
class JwtPayloadTest extends \PHPUnit\Framework\TestCase
{
    private JwtPayload $payload;

    protected function setUp(): void
    {
        $this->payload = new JwtPayload();
    }

    public function testGettingAllValues(): void
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

    public function testGettingAudience(): void
    {
        $this->assertNull($this->payload->getAudience());
        $this->payload->setAudience('foo');
        $this->assertEquals('foo', $this->payload->getAudience());
    }

    public function testGettingEncodedString(): void
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

    public function testGettingId(): void
    {
        $this->assertNotEmpty($this->payload->get('jti'));
        $this->assertNotEmpty($this->payload->getId());
    }

    public function testGettingIssuedAt(): void
    {
        $this->assertNull($this->payload->getIssuedAt());
        $date = new DateTimeImmutable();
        $this->payload->setIssuedAt($date);
        $this->assertSame($date, $this->payload->getIssuedAt());
    }

    public function testGettingIssuer(): void
    {
        $this->assertNull($this->payload->getIssuer());
        $this->payload->setIssuer('foo');
        $this->assertEquals('foo', $this->payload->getIssuer());
    }

    public function testGettingSubject(): void
    {
        $this->assertNull($this->payload->getSubject());
        $this->payload->setSubject('foo');
        $this->assertEquals('foo', $this->payload->getSubject());
    }

    public function testGettingValidFrom(): void
    {
        $this->assertNull($this->payload->getValidFrom());
        $date = new DateTimeImmutable();
        $this->payload->setValidFrom($date);
        $this->assertSame($date, $this->payload->getValidFrom());
    }

    public function testGettingValidTo(): void
    {
        $this->assertNull($this->payload->getValidTo());
        $date = new DateTimeImmutable();
        $this->payload->setValidTo($date);
        $this->assertSame($date, $this->payload->getValidTo());
    }

    public function testGettingValue(): void
    {
        $this->assertNull($this->payload->get('foo'));
        $this->payload->add('foo', 'bar');
        $this->assertEquals('bar', $this->payload->get('foo'));
        $this->payload->add('foo', 'baz');
        $this->assertEquals('baz', $this->payload->get('foo'));
    }

    public function testIdChangesWithNewClaims(): void
    {
        $jti1 = $this->payload->getId();
        $this->payload->add('foo', 'bar');
        $jti2 = $this->payload->getId();
        $this->payload->add('baz', 'blah');
        $jti3 = $this->payload->getId();
        $this->assertNotEquals($jti1, $jti2);
        $this->assertNotEquals($jti2, $jti3);
    }

    public function testIdDoesNotChangeWithNewClaimsWhenManuallySet(): void
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

    public function testInvalidAudienceThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->payload->setAudience(new DateTimeImmutable());
    }

    public function testSettingId(): void
    {
        $this->payload->setId('foo');
        $this->assertEquals('foo', $this->payload->get('jti'));
        $this->assertEquals('foo', $this->payload->getId());
    }

    public function testSettingMultipleAudiences(): void
    {
        $this->payload->setAudience(['foo', 'bar']);
        $this->assertEquals(['foo', 'bar'], $this->payload->getAudience());
    }
}
