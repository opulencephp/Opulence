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

/**
 * Defines the audience verifier
 */
class AudienceVerifier implements IVerifier
{
    /** @var array The audience */
    private $audience = [];

    /**
     * @param string|array $audience The audience
     */
    public function __construct($audience = [])
    {
        if (!is_array($audience)) {
            $audience = [$audience];
        }

        $this->audience = $audience;
    }

    /**
     * @inheritdoc
     */
    public function verify(SignedJwt $jwt)
    {
        $audience = $jwt->getPayload()->getAudience();

        if (!is_array($audience)) {
            $audience = [$audience];
        }

        if (count($this->audience) === 0) {
            return;
        }

        if (count(array_intersect($audience, $this->audience)) == 0) {
            throw new VerificationException("Audience is invalid");
        }
    }
}