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
    public function verify(SignedJwt $jwt, string &$error = null) : bool
    {
        $signature = $jwt->getSignature();

        if ($signature === "") {
            $error = "Signature cannot be empty";

            return false;
        }

        if ($jwt->getHeader()->getAlgorithm() !== $this->signer->getAlgorithm()) {
            $error = "Token's algorithm does not match signer's";

            return false;
        }

        if (!$this->signer->verify($jwt->getUnsignedValue(), $signature)) {
            $error = "Signature is invalid";

            return false;
        }

        return true;
    }
}