<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authentication\Clients\Orm;

use Opulence\Authentication\Clients\IClient;

/**
 * Defines the interface for client repositories to implement
 */
interface IClientRepository
{
    /**
     * Adds a client
     *
     * @param IClient $client The client to add
     */
    public function add(IClient $client): void;

    /**
     * Deletes a client
     *
     * @param IClient $client The client to delete
     */
    public function delete(IClient $client): void;

    /**
     * Gets the client with the input Id
     *
     * @param int|string $id The Id to get by
     * @return IClient The client with the input Id
     */
    public function getById($id);
}
