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

use Opulence\Authentication\Tokens\JsonWebTokens\JwtHeader;
use Opulence\Authentication\Tokens\JsonWebTokens\JwtPayload;
use Opulence\Authentication\Tokens\JsonWebTokens\UnsignedJwt;

/**
 * Tests the unsigned JWT
 */
class UnsignedJwtTest extends \PHPUnit\Framework\TestCase
{
    private UnsignedJwt $jwt;
    private JwtHeader $header;
    private JwtPayload $payload;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->header = new JwtHeader();
        $this->payload = new JwtPayload();
        $this->jwt = new UnsignedJwt($this->header, $this->payload);
    }

    /**
     * Tests getting the header
     */
    public function testGettingHeader(): void
    {
        $this->assertSame($this->header, $this->jwt->getHeader());
    }

    /**
     * Tests getting the payload
     */
    public function testGettingPayload(): void
    {
        $this->assertSame($this->payload, $this->jwt->getPayload());
    }

    /**
     * Tests getting the unsigned value
     */
    public function testGettingUnsignedValue(): void
    {
        $this->assertEquals(
            "{$this->jwt->getHeader()->encode()}.{$this->jwt->getPayload()->encode()}",
            $this->jwt->getUnsignedValue()
        );
    }

    /**
     * Tests getting the unsigned value with a "none" algorithm
     */
    public function testGettingUnsignedValueWithNoneAlgorithm(): void
    {
        $this->header->add('alg', 'none');
        $this->assertEquals(
            "{$this->jwt->getHeader()->encode()}.{$this->jwt->getPayload()->encode()}.",
            $this->jwt->getUnsignedValue()
        );
    }
}
