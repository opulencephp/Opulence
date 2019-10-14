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
 * Defines the list of cryptographic secret types
 */
final class SecretTypes
{
    /** A cryptographic key */
    public const KEY = 'key';
    /** A cryptographic password */
    public const PASSWORD = 'password';
}
