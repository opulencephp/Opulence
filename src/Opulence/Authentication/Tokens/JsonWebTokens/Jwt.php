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
use RuntimeException;

/**
 * Defines a JSON web token
 */
class Jwt
{
    /** @var JwtHeader The header */
    private $header = null;
    /** @var JwtPayload The payload */
    private $payload = null;

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
     * Creates a JWT from a raw string
     *
     * @param string $token The token to create from
     * @param string $key The key used to sign the token
     * @param bool $verify Whether or not to verify the token is correct
     * @return Jwt The JSON web token
     * @throws InvalidArgumentException Thrown if the token was not correctly formatted
     * @throws SignatureVerificationException Thrown if the signature could not be verified
     */
    public static function createFromString(string $token, string $key, bool $verify = true) : Jwt
    {
        $segments = explode(".", $token);

        if (count($segments) !== 3) {
            throw new InvalidArgumentException("Token did not contain 3 segments");
        }

        list($encodedHeader, $encodedPayload, $encodedSignature) = $segments;
        $headerArray = json_decode(self::base64UrlDecode($encodedHeader), true, 512, JSON_BIGINT_AS_STRING);
        $payloadArray = json_decode(self::base64UrlDecode($encodedPayload), true, 512, JSON_BIGINT_AS_STRING);
        $signature = self::base64UrlDecode($encodedSignature);

        if ($headerArray === null) {
            throw new InvalidArgumentException("Invalid header");
        }

        if ($payloadArray === null) {
            throw new InvalidArgumentException("Invalid payload");
        }

        if (!isset($headerArray["alg"])) {
            throw new InvalidArgumentException("No algorithm set in header");
        }

        if ($verify) {
            if (empty($key)) {
                throw new InvalidArgumentException("Key must not be empty");
            }

            if (!self::verifyPayload($payloadArray, $error)) {
                throw new SignatureVerificationException($error);
            }

            if (!self::verifySignature("$encodedHeader.$encodedPayload", $signature, $key, $headerArray["alg"])) {
                throw new SignatureVerificationException("Signature is invalid");
            }
        }

        $header = new JwtHeader($headerArray["alg"], $headerArray);
        $payload = new JwtPayload();

        foreach ($payloadArray as $name => $value) {
            $payload->add($name, $value);
        }

        return new Jwt($header, $payload);
    }

    /**
     * Base 64 decodes data for use in URLs
     *
     * @param string $data The data to decode
     * @return string The base 64 decoded data that's safe for URLs
     * @link http://php.net/manual/en/function.base64-encode.php#103849
     */
    private static function base64UrlDecode(string $data) : string
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
    private static function base64UrlEncode(string $data) : string
    {
        return rtrim(strtr(base64_encode($data), "+/", "-_"), "=");
    }

    /**
     * Gets the hash algorithm for a JWT algorithm
     *
     * @param string $jwtAlgorithm The JWT algorithm whose hash algorithm we want
     * @return string The algorithm to use in a hash function
     * @throws InvalidArgumentException Thrown if the algorithm is not an OpenSSL algorithm
     */
    private static function getHashAlgorithm(string $jwtAlgorithm) : string
    {
        switch ($jwtAlgorithm) {
            case "HS256":
                return "sha256";
            case "HS384":
                return "sha384";
            case "HS512":
                return "sha512";
            default:
                throw new InvalidArgumentException("Algorithm \"$jwtAlgorithm\" is not a hash algorithm");
        }
    }

    /**
     * Gets the OpenSSL Id for a JWT algorithm
     *
     * @param string $jwtAlgorithm The JWT algorithm whose OpenSSL Id we want
     * @return int The PHP Id for the algorithm
     * @throws InvalidArgumentException Thrown if the algorithm is not an OpenSSL algorithm
     */
    private static function getOpenSslAlgorithm(string $jwtAlgorithm) : int
    {
        switch ($jwtAlgorithm) {
            case "RS256":
                return OPENSSL_ALGO_SHA256;
            case "RS384":
                return OPENSSL_ALGO_SHA384;
            case "RS512":
                return OPENSSL_ALGO_SHA512;
            default:
                throw new InvalidArgumentException("Algorithm \"$jwtAlgorithm\" is not an OpenSSL algorithm");
        }
    }

    /**
     * Checks if an algorithm is an RSA algorithm
     *
     * @param string $jwtAlgorithm The algorithm to look at
     * @return bool True if the algorithm is an RSA algorithm, otherwise false
     */
    private static function isRsaAlgorithm(string $jwtAlgorithm) : bool
    {
        return in_array($jwtAlgorithm, ["RS256", "RS384", "RS512"]);
    }

    /**
     * Verifies the payload
     *
     * @param array|string $payload The payload to verify
     * @param string|null $error The error message
     * @return bool True if the signature is valid, otherwise false
     */
    private static function verifyPayload($payload, string &$error = null) : bool
    {
        if (!is_array($payload)) {
            return true;
        }

        // Handle the not-before time
        if (isset($payload["nbf"])) {
            if ($payload["nbf"] > time()) {
                $error = "Token cannot be used before {$payload["nbf"]}";

                return false;
            }
        }

        // Handle the expiration time
        if (isset($payload["exp"])) {
            if ($payload["exp"] < time()) {
                $error = "Token expired at {$payload["exp"]}";

                return false;
            }
        }

        return true;
    }

    /**
     * Verifies the signature
     *
     * @param string $data The data to verify
     * @param string $signature The signature used
     * @param string|resource $key The key to use (resource for RSA algorithms)
     * @param string $algorithm The algorithm used
     * @return bool True if the signature is valid, otherwise false
     */
    private static function verifySignature(string $data, string $signature, $key, string $algorithm) : bool
    {
        if (self::isRsaAlgorithm($algorithm)) {
            return openssl_verify($data, $signature, $key, self::getOpenSslAlgorithm($algorithm));
        } else {
            return hash_equals($signature, hash_hmac(self::getHashAlgorithm($algorithm), $data, $key, true));
        }
    }

    /**
     * Encodes this token as a string
     *
     * @param string|resource $key The key to sign the token with, or the private key if using RSA algorithm
     * @return string The encoded string
     */
    public function encode($key) : string
    {
        $segments = [
            $this->header->encode(),
            $this->payload->encode()
        ];
        $signature = $this->sign(implode(".", $segments), $key);
        $segments[] = self::base64UrlEncode($signature);

        return implode(".", $segments);
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
     * Signs the data
     *
     * @param string $data The data to sign
     * @param string|resource $key The key to sign the data with (resource if RSA algorithm)
     * @return string The signature
     * @throws InvalidArgumentException Thrown if the algorithm was invalid
     * @throws RuntimeException Thrown if there was an error signing the data
     */
    private function sign(string $data, $key) : string
    {
        // If this is an RSA algorithm
        if (self::isRsaAlgorithm($this->header->getAlgorithm())) {
            $signature = "";

            if (!openssl_sign($data, $signature, $key, self::getOpenSslAlgorithm($this->header->getAlgorithm()))) {
                throw new RuntimeException("Failed to sign data");
            }

            return $signature;
        } else {
            return hash_hmac(self::getHashAlgorithm($this->header->getAlgorithm()), $data, $key, true);
        }
    }
}