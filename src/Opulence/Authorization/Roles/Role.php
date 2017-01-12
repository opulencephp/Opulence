<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Authorization\Roles;

/**
 * Defines a role
 */
class Role
{
    /** @var int|string The database Id */
    protected $id = -1;
    /** @var string The name of the role */
    protected $name = '';

    /**
     * @param int|string $id The database Id
     * @param string $name The name of the role
     */
    public function __construct($id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param int|string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}
