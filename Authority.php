<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authorization;

use Opulence\Authorization\Privileges\IPrivilegeRegistry;

/**
 * Defines the authority
 */
class Authority implements IAuthority
{
    /** @var IAuthorizable The current user */
    private $user = null;
    /** @var IPrivilegeRegistry The privilege registry */
    private $privilegeRegistry = null;

    /**
     * @param IAuthorizable $user The current user
     * @param IPrivilegeRegistry $privilegeRegistry The privilege registry
     */
    public function __construct(IAuthorizable $user, IPrivilegeRegistry $privilegeRegistry)
    {
        $this->user = $user;
        $this->privilegeRegistry = $privilegeRegistry;
    }

    /**
     * @inheritdoc
     */
    public function can(string $privilege, ...$arguments) : bool
    {
        return call_user_func($this->privilegeRegistry->getCallback($privilege), $this->user, ...$arguments);
    }
}