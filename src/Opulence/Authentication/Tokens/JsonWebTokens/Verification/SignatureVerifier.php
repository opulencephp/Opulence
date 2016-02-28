<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Tokens\JsonWebTokens\Verification;

use Opulence\Authentication\Tokens\JsonWebTokens\SignedJwt;
use Opulence\Authentication\Tokens\Signatures\ISigner;

/**
 * Defines the signature verifier
 */
class SignatureVerifier implements IVerifier
{
    /** @var ISigner The signer to use to verify the signatures */
    private $signer = null;

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
    public function verify(SignedJwt $jwt)
    {
        $signature = $jwt->getSignature();

        if ($signature === "") {
            throw new VerificationException("Signature cannot be empty");
        }

        if ($jwt->getHeader()->getAlgorithm() !== $this->signer->getAlgorithm()) {
            throw new VerificationException("Token's algorithm does not match signer's");
        }

        if (!$this->signer->verify($jwt->getUnsignedValue(), $signature)) {
            throw new VerificationException("Signature is invalid");
        }
    }
}