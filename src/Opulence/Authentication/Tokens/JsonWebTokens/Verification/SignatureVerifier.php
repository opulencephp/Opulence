<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Tokens\JsonWebTokens\Verification;

use Opulence\Authentication\Tokens\JsonWebTokens\Jwt;
use Opulence\Authentication\Tokens\JsonWebTokens\Signature\ISigner;

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
    public function verify(Jwt $jwt)
    {
        if ($jwt->getSignature() === "") {
            throw new VerificationException("Signature cannot be empty");
        }

        if ($jwt->getHeader()->getAlgorithm() !== $this->signer->getAlgorithm()) {
            throw new VerificationException("Token's algorithm does not match signer's");
        }

        if (!$this->signer->verify($jwt)) {
            throw new VerificationException("Signature is invalid");
        }
    }
}