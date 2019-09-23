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

/**
 * Defines the issuer verifier
 */
final class IssuerVerifier implements IVerifier
{
    /** @var string|null The issuer */
    private ?string $issuer;

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
    public function verify(SignedJwt $jwt, string &$error = null): bool
    {
        $issuer = $jwt->getPayload()->getIssuer();

        if ($issuer !== $this->issuer) {
            $error = JwtErrorTypes::ISSUER_INVALID;

            return false;
        }

        return true;
    }
}
