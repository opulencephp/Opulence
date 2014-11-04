# Relational Databases

## Table of Contents
1. [Introduction](#introduction)
2. [Creating a Connection Pool](#creating-a-connection-pool)
3. [Single-Server Connection Pool](#single-server-connection-pool)
  1. [PHP Array Config with PostgreSQL PDO](#php-array-config-with-postgresql-pdo)
  1. [ConnectionPoolConfig with PostgreSQL PDO](#connectionpoolconfig-with-postgresql-pdo)
  2. [PHP Array Config with Driver and Server Objects](#php-array-config-with-driver-and-server-objects)
4. [Master-Slave Connection Pool](#master-slave-connection-pool)
  1. [PHP Array Config with MySQL PDO Driver](#php-array-config-with-mysql-pdo-driver)
5. [Read/Write Connections](#readwrite-connections)

## Introduction
Relational databases store information about data and how it's related to other data.  **RDev** provides classes and methods for connecting to relational databases and querying them for data.  Connection pools help you manage your database connections by doing all the dirty work for you.  You can use an assortment of PHP drivers to connect to multiple types of server configurations.  For example, if you have a single database server in your stack, you can use a `SingleServerConnectionPool`.  If you have a master/slave(s) setup, you can use a `MasterSlaveConnectionPool`.

## Creating a Connection Pool
Connection pools can be instantiated directly or with the help of a `ConnectionPoolConfig` and `SingleServerConnectionPoolFactory` ([learn more about configs](/application/rdev/configs)).  The config must have the following keys:
* "driver"
  * The value must be either:
    1. The name of the driver per the `ConnectionPool class` driver list
    2. An object that implements the `IDriver` interface
    3. The fully-qualified name of a class that implements the `IDriver` interface (useful for passing in custom drivers)
* "servers"
  * "master"
    * Either the name or an instance of a server class OR an array with the following keys:
      * "host" => The server host
      * "username" => The server username
      * "password" => The server password
      * "databaseName" => The database name
      * You can optionally specify values for "charset" and "port"
    
The following keys are options:
* "driverOptions"
  * Settings that help setup a driver connection, eg "unix_socket" for MySQL Unix sockets
* "connectionOptions"
  * The driver-specific connection settings, eg `\PDO::ATTR_PERSISTENT => true`
  
## Single-Server Connection Pool
Single-server connection pools are useful for single-database server stacks, eg not master-slave setups.

#### PHP Array Config with PostgreSQL PDO
```php
use RDev\Databases\SQL\Configs;
use RDev\Databases\SQL\Factories;

$configArray = [
    "driver" => "pdo_pgsql",
    "servers" => [
        "master" => [
            "host" => "127.0.0.1",
            "username" => "foo",
            "password" => "bar",
            "databaseName" => "mydb"
        ]
    ]
];
$config = new Configs\ConnectionPoolConfig($configArray);
$factory = new Factories\SingleServerConnectionPoolFactory();
$connectionPool = $factory->createFromConfig($config);
```

#### ConnectionPoolConfig with PostgreSQL PDO
```php
use RDev\Databases\SQL\Configs;
use RDev\Databases\SQL\Factories;

$configArray = [
    "driver" => "pdo_pgsql",
    "servers" => [
        "master" => [
            "host" => "127.0.0.1",
            "username" => "foo",
            "password" => "bar",
            "databaseName" => "mydb"
        ]
    ]
];
$config = new Configs\ConnectionPoolConfig($configArray);
$factory = new Factories\SingleServerConnectionPoolFactory();
$connectionPool = $factory->createFromConfig($config);
```

#### PHP Array Config with Driver and Server Objects
```php
use RDev\Databases\SQL\Configs;
use RDev\Databases\SQL\Factories;
use RDev\Databases\SQL\PDO\PostgreSQL;

$driver = new PostgreSQL\Driver();
$server = new SQL\Server();
$server->setHost("127.0.0.1");
$server->setUsername("foo");
$server->setPassword("bar");
$server->setDatabaseName("mydb");
$configArray = [
    "driver" => $driver,
    "servers" => [
        "master" => $server
    ]
];
$config = new Configs\ConnectionPoolConfig($configArray);
$factory = new Factories\SingleServerConnectionPoolFactory();
$connectionPool = $factory->createFromConfig($config);
```

## Master-Slave Connection Pool
Master-slave connection pools are useful for setups that include a master and at least one slave server.  The configuration array for a master-slave connection pool accepts an additional entry under "servers" - "slaves", which must map to an array of server data that is identical to the master server settings from above.

#### PHP Array Config with MySQL PDO Driver
```php
use RDev\Databases\SQL\Configs;
use RDev\Databases\SQL\Factories;

$configArray = [
    "driver" => "pdo_mysql",
    "servers" => [
        "master" => [
            "host" => "127.0.0.1",
            "username" => "foo",
            "password" => "bar",
            "databaseName" => "mydb"
        ],
        "slaves" => [
            [
                "host" => "192.128.0.1",
                "username" => "foo",
                "password" => "bar",
                "databaseName" => "mydb"
            ],
            [
                "host" => "192.128.0.2",
                "username" => "foo",
                "password" => "bar",
                "databaseName" => "mydb"
            ]
        ]
    ]
];
$config = new Configs\MasterSlaveConnectionPoolConfig($configArray);
$factory = new Factories\MasterSlaveConnectionPoolFactory();
$connectionPool = $factory->createFromConfig($config);
```

## Read/Write Connections
To read from the database, simply use the connection returned by `$connectionPool->getReadConnection()`.  Similarly, `$connectionPool->getWriteConnection()` will return a connection to use for write queries.  These two methods take care of figuring out which server to connect to.  If you want to specify a server to connect to, you can pass it in as a parameter to either of these methods.