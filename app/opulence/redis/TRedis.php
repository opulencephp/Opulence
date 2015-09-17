<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the trait used by Redis classes
 */
namespace Opulence\Redis;

trait TRedis
{
    /** @var Server The server we're connecting to */
    protected $server = null;
    /** @var TypeMapper The type mapper to use for converting data to/from Redis */
    protected $typeMapper = null;

    /**
     * @inheritdoc
     */
    public function deleteKeyPatterns($keyPatterns)
    {
        if(is_string($keyPatterns))
        {
            $keyPatterns = [$keyPatterns];
        }

        // Loops through our key patterns, gets all keys that match them, then deletes each of them
        $lua = "local keyPatterns = {'" . implode("','", $keyPatterns) . "'}
            for i, keyPattern in ipairs(keyPatterns) do
                for j, key in ipairs(redis.call('keys', keyPattern)) do
                    redis.call('del', key)
                end
            end";
        $this->eval($lua);

        return $this->getLastError() === null;
    }

    /**
     * @inheritdoc
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * @inheritdoc
     */
    public function getTypeMapper()
    {
        return $this->typeMapper;
    }
}