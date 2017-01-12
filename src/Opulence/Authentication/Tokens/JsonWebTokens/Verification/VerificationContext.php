<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Authentication\Tokens\JsonWebTokens\Verification;

use Opulence\Authentication\Tokens\Signatures\ISigner;

/**
 * Defines the verification context
 */
class VerificationContext
{
    /** @var array The audience */
    private $audience = [];
    /** @var string|null The issuer */
    private $issuer = null;
    /** @var ISigner The signer */
    private $signer = null;
    /** @var string|null The subject */
    private $subject = null;

    /**
     * @param ISigner $signer The signer
     */
    public function __construct(ISigner $signer)
    {
        $this->setSigner($signer);
    }

    /**
     * @return array
     */
    public function getAudience() : array
    {
        return $this->audience;
    }

    /**
     * @return string|null
     */
    public function getIssuer()
    {
        return $this->issuer;
    }

    /**
     * @return ISigner
     */
    public function getSigner() : ISigner
    {
        return $this->signer;
    }

    /**
     * @return string|null
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param array $audience
     */
    public function setAudience(array $audience)
    {
        $this->audience = $audience;
    }

    /**
     * @param string $issuer
     */
    public function setIssuer(string $issuer)
    {
        $this->issuer = $issuer;
    }

    /**
     * @param ISigner $signer
     */
    public function setSigner(ISigner $signer)
    {
        $this->signer = $signer;
    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject)
    {
        $this->subject = $subject;
    }
}
