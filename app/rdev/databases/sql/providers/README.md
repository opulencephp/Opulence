# Type Mappers

## Table of Contents
1. [Introduction](#introduction)
2. [Basic Usage](#basic-usage)

## Introduction
A common task when reading and writing to a database is translating PHP values into database values and vice versa.  For example, you might store a date as a `DateTime` in PHP, but you need to translate this into a string with a `Y-m-d H:i:s` format before storing it in the database.  *Providers* contain the rules for each database provider (eg MySQL, PostgreSQL, etc) on how to translate PHP and database values.  Using a combination of *type mappers* and *providers*, you can translate PHP-to-database and database-to-PHP values.

## Basic Usage
For example, let's convert a `DateTime` to a SQL-ready value:

```php
use RDev\Databases\SQL\Providers;

$phpDateTime = new \DateTime("1987-07-24 12:34:56");

// Let's use the PostgreSQL provider
$provider = new Providers\PostgreSQL();
$typeMapper = new Providers\TypeMapper();
echo $typeMapper->toSQLTimestampWithTimeZone($phpDateTime, $provider); // "1987-07-24 12:34:56"
```
Alternatively, we can specify the provider in the type mapper's constructor so we don't have to constantly pass it into every type mapper method:
```php
use RDev\Databases\SQL\Providers;

$phpTime = new \DateTime("1987-07-24 12:34:56");

// Let's use the MySQL provider
$provider = new Providers\MySQL();
$typeMapper = new Providers\TypeMapper($provider);
echo $typeMapper->toSQLTimeWithTimeZone($phpTime); // "12:34:56"
```
Let's translate a PostgreSQL boolean to a PHP boolean:
```php
use RDev\Databases\SQL\Providers;

$postgreSQLFalse = 'f';
$provider = new Providers\PostgreSQL();
$typeMapper = new Providers\TypeMapper($provider);
echo $typeMapper->fromSQLBoolean($postgreSQLFalse) === false; // "1"
```