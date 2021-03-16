<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Authentication\Tokens\Signatures;

use InvalidArgumentException;

/**
 * Defines the HMAC signer
 */
class HmacSigner implements ISigner
{
    /** @var string The algorithm to use */
    private $algorithm = Algorithms::SHA256;
    /** @var string|resource The public key */
    private $publicKey = null;

    /**
     * @param string $algorithm The algorithm to use
     * @param string|resource $publicKey The public key
     */
    public function __construct(string $algorithm, $publicKey)
    {
        $this->algorithm = $algorithm;
        $this->publicKey = $publicKey;
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
        return \hash_hmac(
            $this->getHashAlgorithm($this->algorithm),
            $data,
            $this->publicKey,
            true
        );
    }

    /**
     * @inheritdoc
     */
    public function verify(string $data, string $signature) : bool
    {
        if ($signature === '') {
            return false;
        }

        return \hash_equals(
            $signature,
            \hash_hmac(
                $this->getHashAlgorithm($this->algorithm),
                $data,
                $this->publicKey,
                true
            )
        );
    }

    /**
     * Gets the hash algorithm for an algorithm
     *
     * @param string $algorithm The algorithm whose hash algorithm we want
     * @return string The algorithm to use in a hash function
     * @throws InvalidArgumentException Thrown if the algorithm is not an OpenSSL algorithm
     */
    private function getHashAlgorithm(string $algorithm) : string
    {
        switch ($algorithm) {
            case Algorithms::SHA256:
                return 'sha256';
            case Algorithms::SHA384:
                return 'sha384';
            case Algorithms::SHA512:
                return 'sha512';
            default:
                throw new InvalidArgumentException("Algorithm \"$algorithm\" is not a hash algorithm");
        }
    }
}
