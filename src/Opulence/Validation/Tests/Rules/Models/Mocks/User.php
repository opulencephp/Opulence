<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Validation\Tests\Rules\Models\Mocks;

/**
 * Mocks a user for use in testing
 */
class User
{
    /** @var int The user's database Id */
    private int $id;
    /** @var string The user's name */
    private string $name;
    /** @var string The user's email */
    private string $email;

    /**
     * @param int $id The user's database Id
     * @param string $name The user's name
     * @param string $email The user's email
     */
    public function __construct(int $id, string $name, string $email)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
