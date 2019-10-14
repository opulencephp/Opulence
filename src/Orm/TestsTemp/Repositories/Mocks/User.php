<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Orm\TestsTemp\Repositories\Mocks;

/**
 * Mocks a user object for use in testing
 */
class User
{
    /** @var int The user Id */
    private int $id;
    /** @var int The Id of an imaginary aggregate root (eg parent) of this user */
    private ?int $aggregateRootId = null;
    /** @var int The Id of a second imaginary aggregate root of this user */
    private ?int $secondAggregateRootId = null;
    /** @var string The username */
    private string $username;

    /**
     * @param int $id The user Id
     * @param string $username The username
     */
    public function __construct($id, $username)
    {
        $this->id = $id;
        $this->username = $username;
    }

    /**
     * @return int
     */
    public function getAggregateRootId(): int
    {
        return $this->aggregateRootId;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getSecondAggregateRootId(): int
    {
        return $this->secondAggregateRootId;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param int $aggregateRootId
     */
    public function setAggregateRootId(int $aggregateRootId): void
    {
        $this->aggregateRootId = $aggregateRootId;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param int $secondAggregateRootId
     */
    public function setSecondAggregateRootId(int $secondAggregateRootId): void
    {
        $this->secondAggregateRootId = $secondAggregateRootId;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }
}
