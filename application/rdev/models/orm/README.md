# Object-Relational Mapping
**RDev** utilizes the *repository pattern* to encapsulate data retrieval from storage.  *Repositories* have *DataMappers* which actually interact directly with storage, eg cache and/or a relational database.  Using a *unit of work*, all changes made to entities retrieved by repositories are tracked and automatically marked for update once the transaction is ready to commit.  Similarly, entities added/deleted from a repository are marked for insertion/deletion in the unit of work.  By executing all the write queries at once as opposed to throughout the lifetime of the application, you're guaranteed good performance.

## Unit of Work Change Tracking
If you pull an entity from a repository and make changes to it, wouldn't it be nice to not have to explicitly write it back to the database?  Well, you can do that with the *unit of work*.  Let's take a look:
```php
use RDev\Models\Databases\SQL;
use RDev\Models\ORM;
use RDev\Models\ORM\DataMappers;
use RDev\Models\ORM\Repositories;
use RDev\Models\Users;

// Assume $connection was set previously
$unitOfWork = new ORM\UnitOfWork($connection);
$dataMapper = new DataMappers\MyDataMapper();
// Specify the name of the class that this repository will store
$users = new Repositories\Repo("RDev\\Models\\Users\\User", $dataMapper, $unitOfWork);

// Let's say we know that there's a user with Id of 123 and username of "foo" in the repository
$someUser = $users->getById(123);
echo $someUser->getUsername(); // "foo"
// Let's change his username
$someUser->setUsername("bar");

// Once we're done with our unit of work, just let it know you're ready to commit
// It'll automatically know what has changed
$unitOfWork->commit();

// To prove that this really worked, let's print the name of the user now
echo $users->getById(123)->getUsername(); // "bar"
```