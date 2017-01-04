<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Tokens\Signatures\Factories;

use InvalidArgumentException;
use Opulence\Authentication\Tokens\Signatures\Algorithms;
use Opulence\Authentication\Tokens\Signatures\HmacSigner;
use Opulence\Authentication\Tokens\Signatures\ISigner;
use Opulence\Authentication\Tokens\Signatures\RsaSsaPkcsSigner;

/**
 * Defines a signer factory
 */
class SignerFactory
{
    /**
     * Creates a signer with the input algorithm
     *
     * @param string $algorithm The algorithm to use (one of the Algorithms constants)
     * @param string|resource $publicKey The public key to sign data with
     * @param string|resource|null $privateKey The private key to sign data with (required for asymmetric algorithms)
     * @return ISigner The signer
     * @throws InvalidArgumentException Thrown if the keys are not in a valid format
     */
    public function createSigner(string $algorithm, $publicKey, $privateKey = null) : ISigner
    {
        if (!is_string($publicKey) && !is_resource($publicKey)) {
            throw new InvalidArgumentException("Public key must either be a string or a resource");
        }

        if ($this->algorithmIsSymmetric($algorithm)) {
            return new HmacSigner($algorithm, $publicKey);
        } else {
            if (!is_string($privateKey) && !is_resource($privateKey)) {
                throw new InvalidArgumentException("Must specify private key for asymmetric algorithms");
            }

            return new RsaSsaPkcsSigner($algorithm, $publicKey, $privateKey);
        }
    }

    /**
     * Gets whether or not an algorithm is symmetric
     *
     * @param string $algorithm The algorithm to check
     * @return bool True if the algorithm is symmetric, otherwise false
     */
    private function algorithmIsSymmetric(string $algorithm) : bool
    {
        return in_array($algorithm, [Algorithms::SHA256, Algorithms::SHA384, Algorithms::SHA512]);
    }
}
