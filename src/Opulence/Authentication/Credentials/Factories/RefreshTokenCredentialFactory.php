<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authentication\Credentials\Factories;

use Opulence\Authentication\Credentials\CredentialTypes;

/**
 * Defines the refresh token credential factory
 */
class RefreshTokenCredentialFactory extends JwtCredentialFactory
{
    /**
     * @inheritdoc
     */
    protected function getCredentialType(): string
    {
        return CredentialTypes::JWT_REFRESH_TOKEN;
    }
}
