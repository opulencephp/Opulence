# NoSQL Databases

## Table of Contents
1. [Introduction](#introduction)
2. [Redis](#redis)
  1. [Redis Config](#redis-config)
  2. [PHPRedis](#phpredis)
  3. [Predis](#predis)
3. [Memcached](#memcached)
  1. [Memcached Config](#memcached-config)
  2. [Basic Memcached Usage](#basic-memcached-usage)

## Introduction
**RDev** provides developers with tools to read and write data from NoSQL (Not Only SQL) databases, which offer horizontal scalability, performance, and flexibility.  Often, NoSQL databases are used as a cache, giving applications enormous boosts in performance and scalability.  RDev provides extensions of popular NoSQL libraries such as Redis and Memcached to help your website scale.

## Redis
Redis is an extremely popular, in-memory key-value cache with pub/sub capabilities.  Unlike Memcached, Redis can store more complex structures such as sets, sorted lists, and hashes.  For more information, [please visit its homepage](http://redis.io/).

#### Redis Config
Our Redis extensions take a configuration array or Redis `ServerConfig` object in their constructors.  They must have the following structure:
```php
[
    "servers" => [
        "master" => [
            "host" => HOST,
            "port" => PORT,
            "password" => AUTHENTICATION_PASSWORD, // Optional
            "databaseIndex" => INDEX_OF_DATABASE_ON_SERVER_TO_CONNECT_TO, // Optional
            "connectionTimeout" => NUMBER_OF_SECONDS_TO_WAIT_BEFORE_TIMEOUT // Optional
        ]
    ]
]
```
Alternatively, you may pass in a server object that extends `RDev\Models\Databases\NoSQL\Redis\Server`:
```php
[
    "servers" => [
        "master" => new MyServer()
    ]
]
```

#### PHPRedis
**PHPRedis** is a Redis client extension to PHP written in C, giving you raw performance without the overhead of PHP scripts.  `RDevPHPRedis` extends PHPRedis and gives you the added feature of *type mappers* (provides methods for casting to and from Redis data types) and compatibility with `Server` objects.  To use one, simply:
```php
use RDev\Models\Databases\NoSQL\Redis;

$config = [
    "servers" => [
        "master" => new Redis\MyServer()
    ]
];
$phpRedis = new Redis\RDevPHPRedis($config);
$phpRedis->set("foo", "bar");
echo $phpRedis->get("foo"); // "bar"
```

#### Predis
**Predis** is a popular Redis client PHP library with the ability to create customized Redis commands.  `RDevPredis` extends Predis and gives you the added feature of *type mappers* and compatibility with `Server` objects.  To use one, simply:
```php
use RDev\Models\Databases\NoSQL\Redis;

$config = [
    "servers" => [
        "master" => new Redis\MyServer()
    ]
];
$predis = new Redis\RDevPredis($config);
$predis->set("foo", "bar");
echo $redis->get("foo"); // "bar"
```

## Memcached
Memcached (pronounced "Mem-cash-dee") is a distributed memory cache with basic key-value store functionality.  Although it doesn't come with all the bells and whistles of Redis, it does offer faster speed, which is suitable for simple key-value data.  For more information, [please visit its homepage](http://www.memcached.org/).

#### Memcached Config
Our Redis extensions take a configuration array or Redis `ServerConfig` object in their constructors.  They must have the following structure:
```php
[
    "servers" => [
        [
            "host" => HOST,
            "port" => PORT,
            "weight" => WEIGHT_RELATIVE_TO_TOTAL_WEIGHT_OF_ALL_SERVERS // Optional
        ],
        [
            ... // Repeat for any additional servers
        ]
    ]
]
```
Alternatively, you may pass in server objects that extend `RDev\Models\Databases\NoSQL\Memcached\Server`:
```php
[
    "servers" => [
        new MyServer1(),
        new MyServer2()
    ]
]
```
#### Basic Memcached Usage
```php
use RDev\Models\Databases\NoSQL\Memcached;

$config = [
    "servers" => [
        [
            "host" => "127.0.0.1",
            "port" => 11211,
            "weight" => 100
        ]
    ]
];
$memcached = new Memcached\RDevMemcached($config);
$memcached->set("foo", "bar");
echo $memcached->get("foo"); // "bar"
```