<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Framework\Authentication\Roles\Orm;

use Opulence\Authentication\Roles\Orm\IRoleRepository as IAuthenticationRoleRepository;
use Opulence\Authorization\Roles\Orm\IRoleRepository as IAuthorizationRoleRepository;

/**
 * Defines an Authentication role repository that uses the Authorization library's role repository
 */
interface IRoleRepository extends IAuthenticationRoleRepository, IAuthorizationRoleRepository
{
    // Don't do anything
}
