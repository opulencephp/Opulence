# Object-Relational Mapping

## Table of Contents
1. [Introduction](#introduction)
2. [Repositories](#repositories)
3. [DataMappers](#datamappers)
4. [Unit of Work](#unit-of-work)
  1. [Entity Registry](#entity-registry)
5. [Aggregate Roots](#aggregate-roots)
6. [Automatic Caching](#automatic-caching)

## Introduction
**RDev** utilizes the *repository pattern* to encapsulate data retrieval from storage.  *Repositories* have *data mappers* which actually interact directly with storage, eg cache and/or a relational database.  Repositories use *units of work*, which act as transactions across multiple repositories.  The benefits of using units of work include:

1. Transactions across multiple repositories can be rolled back, giving you "all or nothing" functionality
2. Changes made to entities retrieved by repositories are automatically checked for changes and, if any are found, scheduled for updating when the unit of work is committed
3. Database writes are queued and executed all at once when the unit of work is committed, giving you better performance than executing writes throughout the lifetime of the application
4. Querying for the same object will always give you the same, single instance of that object

Unlike other popular PHP frameworks, RDev does not force you to extend ORM classes in order to make them storable.  The only interface they must implement is `RDev\ORM\IEntity`, which simply requires `getId()` and `setId()`.

## Repositories
*Repositories* act as collections of entities.  They include methods for adding, deleting, and retrieving entities.  The actual retrieval from storage is done through *data mappers* contained in the repository.  Note that there are no methods like `update()` or `save()`.  These actions take place in the *data mapper* and are scheduled by the *unit of work* contained by the repository.  [Read here](#datamappers) for more information on DataMappers or [here](#unit-of-work) for more information on units of work.

> **Note:** In `get*()` repository methods, do not call the data mapper directly.  Instead, call `getFromDataMapper()`, which will handle managing entities in the unit of work.

## DataMappers
*Data mappers* act as the go-between for repositories and storage.  By abstracting this interaction away from repositories, you can swap your method of storage without affecting the repositories' interfaces.  There are currently 3 types of DataMappers, but you can certainly add your own by implementing `RDev\ORM\DataMappers\IDataMapper`:

1. `SQLDataMapper`
  * Uses an SQL database as its method of storage
  * Allows you to write your own SQL queries to read and write to the database
2. `RedisDataMapper`
  * Uses Redis as its method of storage
    * Can use PHPRedis or Predis as the Redis client library
  * Allows you to write your own methods to read and write data to Redis
3. `CachedSQLDataMapper`
  * Uses an *SQLDataMapper* as its primary storage and an `ICacheDataMapper` to read and write from cache
  * Drastically reduces the number of SQL queries and improves performance through heavy caching
  * `RedisCachedSQLDataMapper` and `MemcachedCachedSQLDataMapper` extend `CachedSQLDataMapper` to give you Redis- and Memcached-backed data mappers, respectively

## Unit of Work
*Units of work* act as transactions across multiple repositories.  They also schedule entity updates/insertions/deletions in the DataMappers. Let's take a look at how units of work can manage entities retrieved through repositories:
```php
use RDev\Databases\SQL;
use RDev\ORM;
use RDev\ORM\DataMappers;
use RDev\ORM\Repositories;
use RDev\Users;

// Assume $connection was set previously
$unitOfWork = new ORM\UnitOfWork($connection, new ORM\EntityRegistry());
$dataMapper = new DataMappers\MyDataMapper();
$users = new Repositories\Repo("RDev\\Users\\User", $dataMapper, $unitOfWork);

// Let's say we know that there's a user with Id of 123 and username of "foo" in the repository
$someUser = $users->getById(123);
echo $someUser->getUsername(); // "foo"

// Let's change his username
$someUser->setUsername("bar");
// Once we're done with our unit of work, just let it know you're ready to commit
// It'll automatically know what has changed and save those changes back to storage
$unitOfWork->commit();

// To prove that this really worked, let's print the name of the user now
echo $users->getById(123)->getUsername(); // "bar"
```

#### Entity Registry
Entities that are scheduled for insertion/deletion/update are managed by an `EntityRegistry`.  The `EntityRegistry` is also responsible for tracking any changes made to the entities it manages.  By default, it uses reflection, which for some classes might be slow.  To speed up the comparison between two objects to see if they're identical, you can use `registerComparisonFunction()`:
```php
use RDev\ORM;

// Assume $connection was set previously
// Also assume the user object was already instantiated
$entityRegistry = new ORM\EntityRegistry();
$unitOfWork = new ORM\UnitOfWork($connection, $entityRegistry); 
$className = $entityRegistry->getClassName($user);
$entityRegistry->manageEntity($user);
$user->setUsername("newUsername");
// Let's pretend that all we care about in checking if two user objects are identical is comparing their usernames
// Register a comparison function that takes two user objects and returns whether or not the usernames matched
$entityRegistry->registerComparisonFunction($className, function($a, $b)
{
    return $a->getUsername() == $b->getUsername();
});
// On commit, the entity registry will run the comparison function, and it will determine that the $user's
// username has changed.  So, it will be scheduled for update and committed
$unitOfWork->commit();
```
> **Note:** PHP's `clone` feature performs a shallow clone.  In other words, it only clones the object, but not any objects contained in that object.  If your object contains another object and you'd like to take advantage of automatic change tracking, you must write a `__clone()` method for that class to clone any objects it contains.  Otherwise, the automatic change tracking will not pick up on changes made to the objects contained in other objects.

## Aggregate Roots
Let's say that when creating a user you also create a password object.  This password object has a reference to the user object's Id.  In this case, the user is what we call an *aggregate root* because without it, the password wouldn't exist.  It'd be perfectly reasonable to insert both of them in the same unit of work.  However, if you did this, you might be asking yourself "How do I get the Id of the user before storing the password?"  The answer is `registerAggregateRootChild()`:
```php
// Let's assume the unit of work has already been setup and that the user and password objects are created
// Order here matters: aggregate roots should be added before their children
$unitOfWork->scheduleForInsertion($user);
$unitOfWork->scheduleForInsertion($password);
// Pass in the aggregate root, the child, and the function that sets the aggregate root Id in the child
// The first argument of the function you pass in should be the aggregate root, and the second should be the child
$unitOfWork->registerAggregateRootChild($user, $password, function($user, $password)
{
    // This will be executed after the user is inserted but before the password is inserted
    $password->setUserId($user->getId());
});
$unitOfWork->commit();
echo $password->getUserId() == $user->getId(); // "1"
```

> **Note:** Aggregate root functions are executed for entities scheduled for insertion and update.

## Automatic Caching
By extending the `CachedSQLDataMapper`, you can take advantage of automatic caching of entities for faster performance.  Entities are searched in cache before defaulting to an SQL database, and they are added to cache on misses.  Writes to cache are automatically queued whenever writing to a `CachedSQLDataMapper`.  To keep cache in sync with SQL, the writes are only performed once a unit of work commits successfully.