<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Tests\Validation\Rules\Models\Mocks;

/**
 * Mocks a user for use in testing
 */
class User
{
    /** @var int The user's database Id */
    private $id = -1;
    /** @var string The user's name */
    private $name = "";
    /** @var string The user's email */
    private $email = "";

    /**
     * @param int $id The user's database Id
     * @param string $name The user's name
     * @param string $email The user's email
     */
    public function __construct($id, $name, $email)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}