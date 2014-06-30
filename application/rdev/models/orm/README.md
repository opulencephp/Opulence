# Object-Relational Mapping
**RDev** utilizes the *repository pattern* to encapsulate data retrieval from storage.  *Repositories* have *DataMappers* which actually interact directly with storage, eg cache and/or a relational database.  Repositories use *units of work*, which act as transactions across multiple repositories.  The benefits of using units of work include:

1. Transactions across multiple repositories can be rolled back, giving you "all or nothing" functionality
2. Changes made to entities retrieved by repositories are automatically checked for changes and, if any are found, scheduled for updating when the unit of work is committed
3. Database writes are queued and executed all at once when the unit of work is committed, giving you better performance than executing writes throughout the lifetime of the application

## Unit of Work Change Tracking
Let's take a look at how units of work can manage entities retrieved through repositories:
```php
use RDev\Models\Databases\SQL;
use RDev\Models\ORM;
use RDev\Models\ORM\DataMappers;
use RDev\Models\ORM\Repositories;
use RDev\Models\Users;

// Assume $connection was set previously
$unitOfWork = new ORM\UnitOfWork($connection);
$dataMapper = new DataMappers\MyDataMapper();
$users = new Repositories\Repo("RDev\\Models\\Users\\User", $dataMapper, $unitOfWork);

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