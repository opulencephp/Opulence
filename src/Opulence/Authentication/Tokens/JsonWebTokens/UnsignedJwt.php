<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Authentication\Tokens\JsonWebTokens;

use Opulence\Authentication\Tokens\IUnsignedToken;

/**
 * Defines an unsigned JWT
 */
class UnsignedJwt implements IUnsignedToken
{
    /** @var JwtHeader The header */
    protected $header = null;
    /** @var JwtPayload The payload */
    protected $payload = null;

    /**
     * @param JwtHeader $header The header
     * @param JwtPayload $payload The payload
     */
    public function __construct(JwtHeader $header, JwtPayload $payload)
    {
        $this->header = $header;
        $this->payload = $payload;
    }

    /**
     * @return JwtHeader
     */
    public function getHeader() : JwtHeader
    {
        return $this->header;
    }

    /**
     * @return JwtPayload
     */
    public function getPayload() : JwtPayload
    {
        return $this->payload;
    }

    /**
     * @inheritdoc
     */
    public function getUnsignedValue() : string
    {
        $unsignedValue = "{$this->header->encode()}.{$this->payload->encode()}";

        if ($this->header->getAlgorithm() === 'none') {
            $unsignedValue .= '.';
        }

        return $unsignedValue;
    }
}
