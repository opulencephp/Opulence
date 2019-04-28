<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Cryptography\Encryption\Keys;

/**
 * Defines a cryptographic password
 */
class Password extends Secret
{
    /**
     * @param string $value The secret password
     */
    public function __construct(string $value)
    {
        parent::__construct(SecretTypes::PASSWORD, $value);
    }
}
