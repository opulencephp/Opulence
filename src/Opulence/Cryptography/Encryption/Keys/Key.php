<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Cryptography\Encryption\Keys;

/**
 * Defines a cryptographic key
 */
class Key extends Secret
{
    /**
     * @param string $value The secret key
     */
    public function __construct(string $value)
    {
        parent::__construct(SecretTypes::KEY, $value);
    }
}
