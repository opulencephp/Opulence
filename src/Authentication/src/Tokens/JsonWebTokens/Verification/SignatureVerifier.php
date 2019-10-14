<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authentication\Tokens\JsonWebTokens\Verification;

use Opulence\Authentication\Tokens\JsonWebTokens\SignedJwt;
use Opulence\Authentication\Tokens\Signatures\ISigner;

/**
 * Defines the signature verifier
 */
final class SignatureVerifier implements IVerifier
{
    /** @var ISigner The signer to use to verify the signatures */
    private ISigner $signer;

    /**
     * @param ISigner $signer The signer to use to verify the signatures
     */
    public function __construct(ISigner $signer)
    {
        $this->signer = $signer;
    }

    /**
     * @inheritdoc
     */
    public function verify(SignedJwt $jwt, string &$error = null): bool
    {
        $signature = $jwt->getSignature();

        if ($signature === '') {
            $error = JwtErrorTypes::SIGNATURE_INCORRECT;

            return false;
        }

        if ($jwt->getHeader()->getAlgorithm() !== $this->signer->getAlgorithm()) {
            $error = JwtErrorTypes::SIGNATURE_ALGORITHM_MISMATCH;

            return false;
        }

        if (!$this->signer->verify($jwt->getUnsignedValue(), $signature)) {
            $error = JwtErrorTypes::SIGNATURE_INCORRECT;

            return false;
        }

        return true;
    }
}
