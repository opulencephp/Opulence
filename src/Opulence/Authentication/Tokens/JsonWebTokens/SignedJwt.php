<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Tokens\JsonWebTokens;

use InvalidArgumentException;
use Opulence\Authentication\Tokens\ISignedToken;

/**
 * Defines the signed JWT
 */
class SignedJwt extends UnsignedJwt implements ISignedToken
{
    /** @var string The signature */
    protected $signature = "";

    /**
     * @inheritdoc
     * @param string $signature The signature
     */
    public function __construct(JwtHeader $header, JwtPayload $payload, string $signature = "")
    {
        parent::__construct($header, $payload);

        $this->signature = $signature;
    }

    /**
     * Creates a signed JWT from a raw string
     *
     * @param string $token The token to create from
     * @return SignedJwt The signed JSON web token
     * @throws InvalidArgumentException Thrown if the token was not correctly formatted
     */
    public static function createFromString(string $token) : SignedJwt
    {
        $segments = explode(".", $token);

        if (count($segments) !== 3) {
            throw new InvalidArgumentException("Token did not contain 3 segments");
        }

        list($encodedHeader, $encodedPayload, $encodedSignature) = $segments;
        $decodedHeader = json_decode(self::base64UrlDecode($encodedHeader), true, 512, JSON_BIGINT_AS_STRING);
        $decodedPayload = json_decode(self::base64UrlDecode($encodedPayload), true, 512, JSON_BIGINT_AS_STRING);
        $signature = self::base64UrlDecode($encodedSignature);

        if ($decodedHeader === null) {
            throw new InvalidArgumentException("Invalid header");
        }

        if ($decodedPayload === null) {
            throw new InvalidArgumentException("Invalid payload");
        }

        if (!isset($decodedHeader["alg"])) {
            throw new InvalidArgumentException("No algorithm set in header");
        }

        $header = new JwtHeader($decodedHeader["alg"], $decodedHeader);
        $payload = new JwtPayload();

        if (is_array($decodedPayload)) {
            foreach ($decodedPayload as $name => $value) {
                $payload->add($name, $value);
            }
        }

        return new self($header, $payload, $signature);
    }

    /**
     * Encodes this token as a string
     *
     * @return string The encoded string
     */
    public function encode() : string
    {
        $segments = [
            $this->header->encode(),
            $this->payload->encode(),
            self::base64UrlEncode($this->signature)
        ];

        return implode(".", $segments);
    }

    /**
     * @inheritdoc
     */
    public function getSignature() : string
    {
        return $this->signature;
    }
}