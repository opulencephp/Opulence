<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for Redis classes to implement
 */
namespace Opulence\Redis;

interface IRedis
{
    /**
     * Deletes all the keys that match the input patterns
     * If you know the specific key(s) to delete, call Redis' delete command instead because this is relatively slow
     *
     * @param array|string The key pattern or list of key patterns to delete
     * @return bool True if successful, otherwise false
     */
    public function deleteKeyPatterns($keyPatterns);

    /**
     * Gets the server connected to by this Redis instance
     *
     * @return Server The server used by the Redis instance
     */
    public function getServer();

    /**
     * Gets the type mapper used by this Redis instance
     *
     * @return TypeMapper The type mapper used by this Redis instance
     */
    public function getTypeMapper();
} 