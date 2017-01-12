<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Authentication\Tokens;

/**
 * Defines the interface for signed tokens to implement
 */
interface ISignedToken extends IUnsignedToken
{
    /**
     * Gets the signature of the token
     *
     * @return string The signature of the token
     */
    public function getSignature() : string;
}
