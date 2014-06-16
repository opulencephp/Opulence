# About
These tools are PHP wrappers for relational database (RDBMS) and NoSQL database interaction.  Specifically, connecting to and querying MySQL, PostgreSQL, and Redis databases has been simplified and made more customizable.  Sometimes you need to programmatically generate/piece together SQL queries.  Rather than concatenating strings together, you can use **Query Builders** to do the heavy lifting.  The following is an example of a simple PostgreSQL query using one:

```
<?php
use RDev\Models\Databases\SQL\QueryBuilders\PostgreSQL;

$queryBuilder = new PostgreSQL\QueryBuilder();
$selectLongTimeUsersQuery = $queryBuilder->select("id", "name", "email")
    ->from("users")
    ->where("datejoined < :dateJoined")
    ->addNamedPlaceholderValue("dateJoined" => "2010-01-01");

echo $selectLongTimeUsersQuery->getSQL();
// "SELECT id, name, email FROM users WHERE datejoined < :dateJoined"
echo var_dump($selectLongTimeUsersQuery->getParameters());
// array("dateJoined" => "2010-01-01")
```
# License
This software is licensed under the MIT license.  Please read the LICENSE for more information.
# Requirements
* PHP 5.5 or higher with OpenSSL enabled
* PDO with MySQL and PGSQL drivers
* PHPRedis
* PHPUnit