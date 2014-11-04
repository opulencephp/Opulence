# Query Builders

## Table of Contents
1. [Introduction](#introduction)
2. [Basic Usage](#basic-usage)
3. [Database-Specific Query Builders](#database-specific-query-builders)

## Introduction
Sometimes you need to programmatically generate/piece together SQL queries.  Rather than concatenating strings together, you can use **Query Builders** to do the heavy lifting.  

##Basic Usage
The following is an example of a simple PostgreSQL query using a query builder:

```php
use RDev\Databases\SQL\QueryBuilders\PostgreSQL;

$queryBuilder = new PostgreSQL\QueryBuilder();
$selectLongTimeUsersQuery = $queryBuilder->select("id", "name", "email")
    ->from("users")
    ->where("datejoined < :dateJoined")
    ->addNamedPlaceholderValue("dateJoined", "2010-01-01");

echo $selectLongTimeUsersQuery->getSQL(); // "SELECT id, name, email FROM users WHERE datejoined < :dateJoined"
echo var_dump($selectLongTimeUsersQuery->getParameters()); // array("dateJoined" => "2010-01-01")
```

## Database-Specific Query Builders
MySQL and PostgreSQL have their own query builders, which implement features that are unique to each database.  For example, the MySQL query builder supports a *LIMIT* clause:
```php
use RDev\Databases\SQL\QueryBuilders\MySQL;

$queryBuilder = new MySQL\QueryBuilder();
$deleteQuery = $queryBuilder->delete("users")
    ->where("name = 'dave'")
    ->limit(1);
    
echo $deleteQuery->getSQL(); // "DELETE FROM users WHERE name = 'dave' LIMIT 1"
```

Similarly, PostgreSQL's *UPDATE* and *INSERT* query builders support a *RETURNING* clause:
```php
use RDev\Databases\SQL\QueryBuilders\PostgreSQL;

$queryBuilder = new PostgreSQL\QueryBuilder();
$updateQuery = $queryBuilder->update("users", "", ["name" => "david"]);
    ->returning("id")
    ->addReturning("name");

echo $updateQuery->getSQL(); // "UPDATE users SET name = ? RETURNING id, name"
```
And
```php
use RDev\Databases\SQL\QueryBuilders\PostgreSQL;

$queryBuilder = new PostgreSQL\QueryBuilder();
$insertQuery = $queryBuilder->insert("users", "", ["name" => "david"]);
    ->returning("id")
    ->addReturning("name");

echo $insertQuery->getSQL(); // "INSERT INTO users (name) VALUES (?) RETURNING id, name"
```