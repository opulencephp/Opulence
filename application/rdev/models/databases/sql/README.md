# Connection Pools
Connection pools help you manage your database connections by doing all the dirty work for you.  You can use an assortment of PHP drivers to connect to multiple types of server configurations.  For example, if you have a single database server in your stack, you can use a SingleServerConnectionPool.  If you have a master/slave(s) setup, you can use a MasterSlaveConnectionPool.  Here's an example of how to instaniate a master/slave setup using the PostgreSQL PDO driver:

```php
use RDev\Models\Databases\SQL;

$config = [
    "driver" => "pdo_postgresql",
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

Now that the connection pool is setup, let's use it.  Say we want to perform a read from the database.  We can either let the connection pool figure out which server to read from (slaves are preferred) or we can specify which server to use manually.

```php
$connection = $connectionPool->getReadConnection();
// Or
$connection = $connectionPool->getReadConnection(MY_CUSTOM_SERVER);

foreach($connection->query("SELECT name FROM users") as $row)
{
    print $row["name"];
}
```
To write to the database, simply:
```php
$connection = $connectionPool->getWriteConnection();
// Or
$connection = $connectionPool->getWriteConnection(MY_CUSTOM_SERVER);

$connection->query("UPDATE USERS set email = 'foo@bar.com'");
```