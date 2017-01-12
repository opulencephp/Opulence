<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Authentication;

use Opulence\Authentication\Credentials\ICredential;

/**
 * Defines an authentication subject
 */
class Subject implements ISubject
{
    /** @var IPrincipal[] The list of principals */
    protected $principals = [];
    /** @var ICredential[] The list of credentials */
    protected $credentials = [];

    /**
     * @param array $principals The list of principals
     * @param array $credentials The list of credentials
     */
    public function __construct(array $principals = [], array $credentials = [])
    {
        foreach ($principals as $principal) {
            $this->addPrincipal($principal);
        }

        foreach ($credentials as $credential) {
            $this->addCredential($credential);
        }
    }

    /**
     * @inheritdoc
     */
    public function addCredential(ICredential $credential)
    {
        $this->credentials[$credential->getType()] = $credential;
    }

    /**
     * @inheritdoc
     */
    public function addPrincipal(IPrincipal $principal)
    {
        $this->principals[$principal->getType()] = $principal;
    }

    /**
     * @inheritdoc
     */
    public function getCredential(string $type)
    {
        if (!isset($this->credentials[$type])) {
            return null;
        }

        return $this->credentials[$type];
    }

    /**
     * @inheritdoc
     */
    public function getCredentials() : array
    {
        return array_values($this->credentials);
    }

    /**
     * @inheritdoc
     */
    public function getPrimaryPrincipal()
    {
        if (!isset($this->principals[PrincipalTypes::PRIMARY])) {
            return null;
        }

        return $this->principals[PrincipalTypes::PRIMARY];
    }

    /**
     * @inheritdoc
     */
    public function getPrincipal(string $type)
    {
        if (!isset($this->principals[$type])) {
            return null;
        }

        return $this->principals[$type];
    }

    /**
     * @inheritdoc
     */
    public function getPrincipals() : array
    {
        return array_values($this->principals);
    }

    /**
     * @inheritdoc
     */
    public function getRoles() : array
    {
        $roles = [];

        foreach ($this->principals as $type => $principal) {
            $roles = array_merge($roles, $principal->getRoles());
        }

        return $roles;
    }

    /**
     * @inheritdoc
     */
    public function hasRole(string $roleName) : bool
    {
        return in_array($roleName, $this->getRoles());
    }
}
