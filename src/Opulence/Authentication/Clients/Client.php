<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authentication\Clients;

/**
 * Defines the interface for clients to implement
 */
class Client implements IClient
{
    /** @var int|string The client Id */
    private $id;
    /** @var string The client name */
    private $name;
    /** @var string The client secret */
    private $secret;

    /**
     * @param int|string $id The client Id
     * @param string $name The client name
     * @param string $secret The client secret
     */
    public function __construct($id, string $name, string $secret = '')
    {
        $this->id = $id;
        $this->name = $name;
        $this->secret = $secret;
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getSecret(): string
    {
        return $this->secret;
    }

    /**
     * @inheritdoc
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @inheritdoc
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
