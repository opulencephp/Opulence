<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Authorization\Bootstrappers;

use Aphiria\DependencyInjection\Bootstrappers\Bootstrapper;
use Aphiria\DependencyInjection\IContainer;
use Opulence\Authorization\Authority;
use Opulence\Authorization\IAuthority;
use Opulence\Authorization\Permissions\IPermissionRegistry;
use Opulence\Authorization\Permissions\PermissionRegistry;
use Opulence\Authorization\Roles\IRoles;
use Opulence\Authorization\Roles\Orm\IRoleMembershipRepository;
use Opulence\Authorization\Roles\Orm\IRoleRepository;
use Opulence\Authorization\Roles\Roles;

/**
 * Defines the authorization bootstrapper
 */
abstract class AuthorizationBootstrapper extends Bootstrapper
{
    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container): void
    {
        $container->bindInstance(IAuthority::class, $this->getAuthority($container));
    }

    /**
     * Gets the role membership repository
     *
     * @param IContainer $container The IoC container
     * @return IRoleMembershipRepository The role membership repository
     */
    abstract protected function getRoleMembershipRepository(IContainer $container): IRoleMembershipRepository;

    /**
     * Gets the role repository
     *
     * @param IContainer $container The IoC container
     * @return IRoleRepository The role repository
     */
    abstract protected function getRoleRepository(IContainer $container): IRoleRepository;

    /**
     * Gets the authority
     *
     * @param IContainer $container The IoC container
     * @return IAuthority The authority
     */
    protected function getAuthority(IContainer $container): IAuthority
    {
        $permissionRegistry = $this->getPermissionRegistry($container);
        $container->bindInstance(IPermissionRegistry::class, $permissionRegistry);
        $container->bindInstance(IRoles::class, $this->getRoles($container));

        return new Authority(-1, [], $permissionRegistry);
    }

    /**
     * Gets the permission registry
     *
     * @param IContainer $container The IoC container
     * @return IPermissionRegistry The permission registry
     */
    protected function getPermissionRegistry(IContainer $container): IPermissionRegistry
    {
        return new PermissionRegistry();
    }

    /**
     * Gets the roles
     *
     * @param IContainer $container The IoC container
     * @return IRoles The roles
     */
    protected function getRoles(IContainer $container): IRoles
    {
        $roleRepository = $this->getRoleRepository($container);
        $roleMembershipRepository = $this->getRoleMembershipRepository($container);
        $container->bindInstance(IRoleRepository::class, $roleRepository);
        $container->bindInstance(IRoleMembershipRepository::class, $roleMembershipRepository);

        return new Roles($roleRepository, $roleMembershipRepository);
    }
}
