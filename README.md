# About
These tools are PHP wrappers for relational database (RDBMS) and NoSQL database interaction.  Specifically, connecting to and querying MySQL, PostgreSQL, and Redis databases has been simplified and made more customizable.  Sometimes you need to programmatically generate/piece together SQL queries.  Rather than concatenating strings together, you can use **Query Builders** to do the heavy lifting.  The following is an example of a simple PostgreSQL query using one:

```
<?php
namespace RamODev;
use RamODev\Databases\SQL\PostgreSQL\QueryBuilders;

$queryBuilder = new QueryBuilders\QueryBuilder();
$selectQuery = $queryBuilder->select("id", "name", "email")
    ->from("users")
    ->where("active = :active")
    ->addNamedPlaceholderValue("active" => "t");

echo $selectQuery->getSQL();
// "SELECT id, name, email FROM users WHERE active = :active"
echo var_dump($selectQuery->getParameters());
// array("active" => "t")
```
# License
This software is licensed under the MIT license.  Please read the LICENSE for more information.
# Requirements
* PHP 5.4 or higher
* PDO with MySQL and PGSQL drivers
* PHPRedis