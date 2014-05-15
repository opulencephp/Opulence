<?php
use RDev\Application\Shared\Models\Databases\NoSQL\Redis;
use RDev\Application\TBA\Models\Databases\NoSQL\Redis\Servers;

require_once(__DIR__ . "/../application/shared/models/configs/PHP.php");

$server = new Servers\ElastiCache();
$redis = new Redis\RDevRedis($server);
$redis->flushAll();

echo "HI";