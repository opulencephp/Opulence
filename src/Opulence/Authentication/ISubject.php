<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authentication;

use Opulence\Authentication\Credentials\ICredential;

/**
 * Defines the interface for subjects to implement
 */
interface ISubject
{
    /**
     * Adds a credential
     *
     * @param ICredential $credential The credential to add
     */
    public function addCredential(ICredential $credential): void;

    /**
     * Adds a principal
     *
     * @param IPrincipal $principal The principal to add
     */
    public function addPrincipal(IPrincipal $principal): void;

    /**
     * Gets the credential with the input type
     *
     * @param string $type The credential type
     * @return ICredential|null The credential, if there was one, otherwise null
     */
    public function getCredential(string $type): ?ICredential;

    /**
     * Gets the list of credentials this subject has
     *
     * @return array The list of credentials
     */
    public function getCredentials(): array;

    /**
     * Gets the primary principal
     *
     * @return IPrincipal|null The primary principal, if there was one, otherwise null
     */
    public function getPrimaryPrincipal(): ?IPrincipal;

    /**
     * Gets the principal with the input type
     *
     * @param string $type The principal type
     * @return IPrincipal|null The principal, if there was one, otherwise null
     */
    public function getPrincipal(string $type): ?IPrincipal;

    /**
     * Gets the list of all principals
     *
     * @return IPrincipal[] The list of all principals
     */
    public function getPrincipals(): array;

    /**
     * Gets the list of role names
     *
     * @return array The list of role names
     */
    public function getRoles(): array;

    /**
     * Gets whether or not a subject has a role
     *
     * @param string $roleName The role to check for
     * @return bool True if the subject has a role, otherwise false
     */
    public function hasRole(string $roleName): bool;
}
