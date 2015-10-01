<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks the Opulence Predis class for use in testing
 */
namespace Opulence\Tests\Redis\Mocks;

use Opulence\Redis\OpulencePredis as BaseOpulencePredis;
use Opulence\Redis\Server;
use Opulence\Redis\TypeMapper;

class OpulencePredis extends BaseOpulencePredis
{
    /**
     * @inheritdoc
     */
    public function __construct(Server $server, TypeMapper $typeMapper)
    {
        $this->server = $server;
        $this->typeMapper = $typeMapper;
    }

    /**
     * We don't want to close the connection because there wasn't one, so do nothing
     */
    public function __destruct()
    {
        // Do nothing
    }

    /**
     * @inheritdoc
     */
    public function select($database)
    {
        // Don't actually select the database in Redis
        $this->server->setDatabaseIndex($database);
    }
} 