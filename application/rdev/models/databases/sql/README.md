# Connection Pools
Connection pools help you manage your database connections by doing all the dirty work for you.  You can use an assortment of PHP drivers to connect to multiple types of server configurations.  For example, if you have a single database server in your stack, you can use a **SingleServerConnectionPool**.  If you have a master/slave(s) setup, you can use a **MasterSlaveConnectionPool**.  

## Table of Contents
1. [Creating a Connection Pool](#creating-a-connection-pool)
2. [Single-Server Connection Pool](#single-server-connection-pool)
3. [Master-Slave Connection Pool](#master-slave-connection-pool)

## Creating a Connection Pool
Connection pools are instantiated with a configuration array.  All connection pools must have the following keys:
* "driver"
  * The value must be either:
    1. The name of the driver per the **ConnectionPool class** driver list
    2. An object that implements the **IDriver** interface
    3. The fully-qualified name of a class that implements the **IDriver** interface (useful for passing in custom drivers)
* "servers"
  * The value must be an array of server settings.  Although the implementation of this array is up to the concrete class that implements **ConnectionPool**, all must have at least have a "master" key.  The value must be one of the following formats:
    1. An array of data containing keys of "host", "username", "password", and "databaseName", which should of course be mapped to the appropriate values.
      * You can optionally specify values for "charset" and "port"
    2. An object that extends the Server class
    
The following keys are options:
* "driverOptions"
  * Settings that help setup a driver connection, eg "unix_socket" for MySQL Unix sockets
* "connectionOptions"
  * The driver-specific connection settings, eg `\PDO::ATTR_PERSISTENT => true`
  
## Single-Server Connection Pool
Single-server connection pools are useful for single-database server stacks, eg not master-slave setups.

Here's an example of how to setup a single-server connection pool with a PostgreSQL PDO driver:
```php
use RDev\Models\Databases\SQL;

$config = [
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
$connectionPool = new SQL\SingleServerConnectionPool($config);
```
Alternatively, we could have specified instances of a driver and/or a server:
```php
use RDev\Models\Databases\SQL;
use RDev\Models\Databases\SQL\PDO\PostgreSQL;

$driver = new PostgreSQL\Driver();
$server = new SQL\Server();
$server->setHost("127.0.0.1");
$server->setUsername("foo");
$server->setPassword("bar");
$server->setDatabaseName("mydb");
$config = [
    "driver" => $driver,
    "servers" => [
        "master" => $server
    ]
];
$connectionPool = new SQL\SingleServerConnectionPool($config);
```
To read from the database, simply use the connection returned by `$connectionPool->getReadConnection();`.  Similarly, `$connectionPool->getWriteConnection()` will return a connection to use for write queries.  These two methods take care of figuring out which server to connect to.  If you want to specify a server to connect to, you can pass it in as a parameter to either of these methods.

## Master-Slave Connection Pool
Master-slave connection pools are useful for setups that include a master and at least one slave server.  The configuration array for a master-slave connection pool accepts an additional entry under "servers" - "slaves", which must map to an array of server data that is identical to the master server settings from above.

Here's an example of how to instantiate a master/slave setup using the MySQL PDO driver:

```php
use RDev\Models\Databases\SQL;

$config = [
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
$connectionPool = new SQL\MasterSlaveConnectionPool($config);
```