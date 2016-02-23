<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Tokens\JsonWebTokens\Signature;

use InvalidArgumentException;
use Opulence\Authentication\Tokens\JsonWebTokens\Jwt;
use RuntimeException;

/**
 * Defines the JWS signer
 */
class Jws implements ISigner
{
    /** @var string The algorithm to use */
    private $algorithm = JwsAlgorithms::SHA256;
    /** @var string|resource The public key */
    private $publicKey = null;
    /** @var string|resource|null The private key */
    private $privateKey = null;

    /**
     * @param string $algorithm The algorithm to use (we ignore JWTs' algorithms for security's sake)
     * @param string|resource $publicKey The public key
     * @param string|resource|null $privateKey The private key
     */
    public function __construct(string $algorithm, $publicKey, $privateKey = null)
    {
        $this->algorithm = $algorithm;
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
    }

    /**
     * @inheritdoc
     */
    public function getAlgorithm() : string
    {
        return $this->algorithm;
    }

    /**
     * @inheritDoc
     */
    public function getUnsignedValue(Jwt $jwt) : string
    {
        return "{$jwt->getHeader()->encode()}.{$jwt->getPayload()->encode()}";
    }

    /**
     * @inheritdoc
     */
    public function sign(Jwt $jwt)
    {
        if ($this->isAsymmetricAlgorithm($this->algorithm)) {
            $signature = $this->getAsymmetricSignature($jwt);
        } else {
            $signature = $this->getSymmetricSignature($jwt);
        }

        $jwt->setSignature($signature);
    }

    /**
     * @inheritdoc
     */
    public function verify(Jwt $jwt) : bool
    {
        if ($jwt->getSignature() === "") {
            return false;
        }

        if ($this->isAsymmetricAlgorithm($this->algorithm)) {
            return $this->isAsymmetricSignatureValid($jwt);
        } else {
            return $this->isSymmetricSignatureValid($jwt);
        }
    }

    /**
     * Gets the signature for an RSA algorithm
     *
     * @param Jwt $jwt The token whose signature we want
     * @return string The signature
     */
    private function getAsymmetricSignature(Jwt $jwt) : string
    {
        $signature = "";

        if (!openssl_sign(
            $this->getUnsignedValue($jwt),
            $signature,
            $this->privateKey,
            $this->getOpenSslAlgorithm($this->algorithm)
        )
        ) {
            throw new RuntimeException("Failed to sign data");
        }

        return $signature;
    }

    /**
     * Gets the hash algorithm for a JWT algorithm
     *
     * @param string $jwtAlgorithm The JWT algorithm whose hash algorithm we want
     * @return string The algorithm to use in a hash function
     * @throws InvalidArgumentException Thrown if the algorithm is not an OpenSSL algorithm
     */
    private function getHashAlgorithm(string $jwtAlgorithm) : string
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
    private function getOpenSslAlgorithm(string $jwtAlgorithm) : int
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
     * Gets the signature for a symmetric algorithm
     *
     * @param Jwt $jwt The token whose signature we want
     * @return string The signature
     */
    private function getSymmetricSignature(Jwt $jwt) : string
    {
        return hash_hmac(
            $this->getHashAlgorithm($this->algorithm),
            $this->getUnsignedValue($jwt),
            $this->publicKey,
            true
        );
    }

    /**
     * Checks if an algorithm is an RSA algorithm
     *
     * @param string $jwtAlgorithm The algorithm to look at
     * @return bool True if the algorithm is an RSA algorithm, otherwise false
     */
    private function isAsymmetricAlgorithm(string $jwtAlgorithm) : bool
    {
        return in_array($jwtAlgorithm, ["RS256", "RS384", "RS512"]);
    }

    /**
     * Gets whether or not a token is correctly signed with an asymmetric algorithm
     *
     * @param Jwt $jwt The token to validate
     * @return bool True if the signature is valid, otherwise false
     */
    private function isAsymmetricSignatureValid(Jwt $jwt) : bool
    {
        return openssl_verify(
            $this->getUnsignedValue($jwt),
            $jwt->getSignature(),
            $this->publicKey,
            $this->getOpenSslAlgorithm($this->algorithm)
        ) === 1;
    }

    /**
     * Gets whether or not a token is correctly signed with a symmetric algorithm
     *
     * @param Jwt $jwt The token to validate
     * @return bool True if the signature is valid, otherwise false
     */
    private function isSymmetricSignatureValid(Jwt $jwt) : bool
    {
        return hash_equals(
            $jwt->getSignature(),
            hash_hmac(
                $this->getHashAlgorithm($this->algorithm),
                $this->getUnsignedValue($jwt),
                $this->publicKey,
                true
            )
        );
    }
}