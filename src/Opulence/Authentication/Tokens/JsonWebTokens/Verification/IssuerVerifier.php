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

/**
 * Defines the issuer verifier
 */
class IssuerVerifier implements IVerifier
{
    /** @var string|null The issuer */
    private $issuer = null;

    /**
     * @param string|null $issuer The issuer
     */
    public function __construct(string $issuer = null)
    {
        $this->issuer = $issuer;
    }

    /**
     * @inheritdoc
     */
    public function verify(Jwt $jwt)
    {
        $issuer = $jwt->getPayload()->getIssuer();

        if ($issuer !== $this->issuer) {
            throw new VerificationException("Issuer is invalid");
        }
    }
}