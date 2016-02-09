<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Users;

use Opulence\Authentication\IAuthenticatable;

/**
 * Defines a basic user
 */
class User implements IAuthenticatable
{
    /** @var int|string The database Id of the user */
    protected $id = -1;
    /** @var string The hashed password of the user */
    protected $hashedPassword = "";

    /**
     * @param int|string $id The database Id of this user
     * @param string $hashedPassword The hashed password of this user
     */
    public function __construct($id, string $hashedPassword)
    {
        $this->setId($id);
        $this->setHashedPassword($hashedPassword);
    }

    /**
     * @inheritdoc
     */
    public function getHashedPassword() : string
    {
        return $this->hashedPassword;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function setHashedPassword(string $hashedPassword)
    {
        $this->hashedPassword = $hashedPassword;
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        $this->id = $id;
    }
} 