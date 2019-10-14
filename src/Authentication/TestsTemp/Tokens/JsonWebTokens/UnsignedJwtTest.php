<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authentication\TestsTemp\Tokens\JsonWebTokens;

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

    protected function setUp(): void
    {
        $this->header = new JwtHeader();
        $this->payload = new JwtPayload();
        $this->jwt = new UnsignedJwt($this->header, $this->payload);
    }

    public function testGettingHeader(): void
    {
        $this->assertSame($this->header, $this->jwt->getHeader());
    }

    public function testGettingPayload(): void
    {
        $this->assertSame($this->payload, $this->jwt->getPayload());
    }

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
