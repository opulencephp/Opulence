<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
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
     * Base 64 decodes data for use in URLs
     *
     * @param string $data The data to decode
     * @return string The base 64 decoded data that's safe for URLs
     * @link http://php.net/manual/en/function.base64-encode.php#103849
     */
    protected static function base64UrlDecode(string $data) : string
    {
        return base64_decode(str_pad(strtr($data, "-_", "+/"), strlen($data) % 4, "=", STR_PAD_RIGHT));
    }

    /**
     * Base 64 encodes data for use in URLs
     *
     * @param string $data The data to encode
     * @return string The base 64 encoded data that's safe for URLs
     * @link http://php.net/manual/en/function.base64-encode.php#103849
     */
    protected static function base64UrlEncode(string $data) : string
    {
        return rtrim(strtr(base64_encode($data), "+/", "-_"), "=");
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
        return "{$this->header->encode()}.{$this->payload->encode()}";
    }
}