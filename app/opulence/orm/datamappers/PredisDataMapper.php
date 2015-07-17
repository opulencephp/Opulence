<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the Cache data mapper implemented by the Predis library
 */
namespace Opulence\ORM\DataMappers;
use Opulence\Redis\OpulencePredis;

abstract class PredisDataMapper extends RedisDataMapper
{
    /** @var OpulencePredis The Redis cache to use for queries */
    protected $redis = null;

    /**
     * {@inheritdoc}
     */
    protected function getSetMembersFromRedis($key)
    {
        return $this->redis->smembers($key);
    }

    /**
     * {@inheritdoc}
     */
    protected function getSortedSetMembersFromRedis($key)
    {
        return $this->redis->zrange($key, 0, -1);
    }

    /**
     * {@inheritdoc}
     */
    protected function getValueFromRedis($key)
    {
        return $this->redis->get($key);
    }
} 