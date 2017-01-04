<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Tokens\Signatures;

use InvalidArgumentException;
use RuntimeException;

/**
 * Defines the RSA SSA PKCS signer
 */
class RsaSsaPkcsSigner implements ISigner
{
    /** @var string The algorithm to use */
    private $algorithm = Algorithms::SHA256;
    /** @var string|resource The public key */
    private $publicKey = null;
    /** @var string|resource The private key */
    private $privateKey = null;

    /**
     * @param string $algorithm The algorithm to use
     * @param string|resource $publicKey The public key
     * @param string|resource $privateKey The private key
     */
    public function __construct(string $algorithm, $publicKey, $privateKey)
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
     * @inheritdoc
     */
    public function sign(string $data) : string
    {
        $signature = "";

        if (!\openssl_sign(
            $data,
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
     * @inheritdoc
     */
    public function verify(string $data, string $signature) : bool
    {
        if ($signature === "") {
            return false;
        }

        return \openssl_verify(
            $data,
            $signature,
            $this->publicKey,
            $this->getOpenSslAlgorithm($this->algorithm)
        ) === 1;
    }

    /**
     * Gets the OpenSSL Id for a algorithm
     *
     * @param string $algorithm The algorithm whose OpenSSL Id we want
     * @return int The PHP Id for the algorithm
     * @throws InvalidArgumentException Thrown if the algorithm is not an OpenSSL algorithm
     */
    private function getOpenSslAlgorithm(string $algorithm) : int
    {
        switch ($algorithm) {
            case Algorithms::RSA_SHA256:
                return OPENSSL_ALGO_SHA256;
            case Algorithms::RSA_SHA384:
                return OPENSSL_ALGO_SHA384;
            case Algorithms::RSA_SHA512:
                return OPENSSL_ALGO_SHA512;
            default:
                throw new InvalidArgumentException("Algorithm \"$algorithm\" is not an OpenSSL algorithm");
        }
    }
}
