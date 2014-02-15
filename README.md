rest-api
========

# Query Builders
Sometimes you need to programmatically generate/piece together SQL queries.  Rather than concatenating strings together, you can use **Query Builders** to do the heavy lifting.  Currently, MySQL and PostgreSQL are supported.  The following is an example of a simple PostgreSQL query using one.

```
<?php
namespace RamODev;
use RamODev\Databases\RDBMS\PostgreSQL\QueryBuilders;

require_once(PATH_TO_POSTGRESQL_QUERY_BUILDER_CLASS);

$queryBuilder = new QueryBuilders\QueryBuilder();
$selectQuery = $queryBuilder->select("id", "name", "email")
    ->from("users")
    ->where("active = :active");
```