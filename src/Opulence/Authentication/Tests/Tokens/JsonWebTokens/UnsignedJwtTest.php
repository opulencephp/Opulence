<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Authentication\Tests\Tokens\JsonWebTokens;

use Opulence\Authentication\Tokens\JsonWebTokens\JwtHeader;
use Opulence\Authentication\Tokens\JsonWebTokens\JwtPayload;
use Opulence\Authentication\Tokens\JsonWebTokens\UnsignedJwt;

/**
 * Tests the unsigned JWT
 */
class UnsignedJwtTest extends \PHPUnit\Framework\TestCase
{
    /** @var UnsignedJwt The JWT to use in tests */
    private $jwt = null;
    /** @var JwtHeader The header to use in tests */
    private $header = null;
    /** @var JwtPayload The payload to use in tests */
    private $payload = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->header = new JwtHeader();
        $this->payload = new JwtPayload();
        $this->jwt = new UnsignedJwt($this->header, $this->payload);
    }

    /**
     * Tests getting the header
     */
    public function testGettingHeader()
    {
        $this->assertSame($this->header, $this->jwt->getHeader());
    }

    /**
     * Tests getting the payload
     */
    public function testGettingPayload()
    {
        $this->assertSame($this->payload, $this->jwt->getPayload());
    }

    /**
     * Tests getting the unsigned value
     */
    public function testGettingUnsignedValue()
    {
        $this->assertEquals(
            "{$this->jwt->getHeader()->encode()}.{$this->jwt->getPayload()->encode()}",
            $this->jwt->getUnsignedValue()
        );
    }

    /**
     * Tests getting the unsigned value with a "none" algorithm
     */
    public function testGettingUnsignedValueWithNoneAlgorithm()
    {
        $this->header->add('alg', 'none');
        $this->assertEquals(
            "{$this->jwt->getHeader()->encode()}.{$this->jwt->getPayload()->encode()}.",
            $this->jwt->getUnsignedValue()
        );
    }
}
