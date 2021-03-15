<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Authentication\Tokens\Signatures;

/**
 * Defines the interface for signers to implement
 */
interface ISigner
{
    /**
     * Gets the algorithm used to sign and verify tokens
     *
     * @return string The algorithm used
     */
    public function getAlgorithm() : string;

    /**
     * Signs a token
     *
     * @param string $data The data to sign
     * @return string the signature
     */
    public function sign(string $data) : string;

    /**
     * Verifies a signature
     *
     * @param string $data The data that is signed
     * @param string $signature The signature to validate
     * @return bool True if the signature is valid, otherwise false
     */
    public function verify(string $data, string $signature) : bool;
}
