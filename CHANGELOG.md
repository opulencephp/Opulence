<h2>v1.1.12 (2020-1-15)</h2>

<h3>Validation</h3>

* Can now indicate that fields with an empty value should still be validated by calling `Rules::validateEmpty()`

<h2>v1.1.11 (2019-12-29)</h2>

<h3>Query Builders</h3>

* Added `Expression` to support SQL expressions in INSERT and UPDATE values

<h2>v1.1.10 (2019-12-14)</h2>

<h3>General</h3>

* Fixed #13 by adding support for PHP 7.4

<h2>v1.1.9 (2019-11-18)</h2>

<h3>General</h3>

* Fixed various PHPDoc typos and CS

<h3>Authorization</h3>

* Fixed bug that caused `Roles::getRolesForSubject()` to return `RoleMembership`s instead of `Role`s.

<h3>Databases</h3>

* Fixed #112 and added `php apex migrations:fix` command to fix the migrations DB schema

<h2>v1.1.8 (2019-10-24)</h2>

<h3>Databases</h3>

* Updated migrations to roll back transactions on failure

<h2>v1.1.7 (2019-03-03)</h2>

<h3>IOC</h3>

* Added `IBootstrapperRegistry::registerBootstrapper()`
* Deprecated `IBootstrapperRegistry::registerEagerBootstrapper()` and `IBootstrapperRegistry::registerLazyBootstrapper()`

<h2>v1.1.6 (2019-02-16)</h2>

<h3>HTTP</h3>

* Fixed bug that prevented CSRF checks from working when using `_method` to manually set the request method

<h2>v1.1.5 (2018-03-13)</h2>

<h3>Databases</h3>

* Fixed migrations, which were running in reverse order

<h3>ORM</h3>

* Added support for IDs that are objects

<h3>Validation</h3>

* Fixed bug that prevented you from using custom rules twice

<h2>v1.1.4 (2017-22-23)</h2>

<h3>Databases</h3>

* Fixed bugs that caused DB migrations to not work with MySQL
* Migration bootstrapper now reads from an environment var to determine the database driver to use

<h2>v1.1.3 (2017-12-22)</h2>

<h3>Framework</h3>

* Fixed #92, which caused `php apex app:runlocally` to not work on Ubuntu due to the file path to the router not being correct

<h3>IO</h3>

* Namespaced all sensitive native PHP functions for better security

<h2>v1.1.2 (2017-11-18)</h2>

<h3>IO</h3>

* Added `Opulence\IO\Streams\MultiStream`

<h2>v1.1.1 (2017-10-23)</h2>

<h3>General</h3>

* Fixed build scripts

<h3>IO</h3>

* Fixed the package name (was `opulence/files`, now is `opulence/io`)

<h2>v1.1.0 (2017-10-22)</h2>

<h3>General</h3>

* Opulence now requires at least PHP 7.1
* Updated PHPUnit to 6.2
* Moved all test directories into a `Test` directory within each library

<h3>Applications</h3>

* Deprecated all classes in this library (support will be dropped in Opulence 2.0)

<h3>Collections</h3>

* Added the collection library

<h3>Database</h3>

* Added database migration support

<h3>Files</h3>

* All classes have been deprecated in the `Opulence\Files` namespace and moved to the `Opulence\IO` namespace

<h3>Framework</h3>

* Added `php apex make:migration`
* Added `php apex migrations:up`
* Added `php apex migrations:down`

<h3>IO</h3>

* Added stream support

<h3>IoC</h3>

* Deprecated `Bootstrapper::run()` and `shutDown()`

<h2>v1.0.10 (2017-04-15)</h2>

<h3>Framework</h3>
* Added more PHPDoc to some template files

<h3>HTTP</h3>

* Fixed #86 (bug with adding response headers)

<h2>v1.0.9 (2017-01-28)</h2>

<h3>Console</h3>
* Fixed options so that their default values are used if no value is specified

<h3>Framework</h3>
* Added `php apex app:runlocally` command to make it easier to run your application locally

<h2>v1.1.0 (2017-?-?)</h2>

<h3>General</h3>
* Bumped minimum PHP version to PHP 7.1

<h3>Deprecations</h3>
* Version parameter in `Opulence\Applications\Application::construct()`
* Version parameter in `Opulence\Console\Kernel::construct()`
* Version parameter in `Opulence\Console\Commands\AboutCommand::construct()`
* `Opulence\Ioc\Bootstrappers\Bootstrapper::run()` and `shutDown()`
* `Opulence\Orm\IUnitOfWork::setConnection()`

<h2>v1.0.8 (2017-01-26)</h2>

<h3>Framework</h3>
* Added ability to generate an empty controller via `php apex make:controller`

<h3>ORM</h3>
* Fixed `UnitOfWork` and `CachedSqlDataMapper` to execute scheduled actions (eg scheduled inserts/deletions/updates) in the same order they're scheduled, rather than by grouping them

<h2>v1.0.7 (2017-01-21)</h2>

<h3>IoC</h3>
* Annotated exceptions

<h2>v1.0.6 (2017-01-19)</h2>

<h3>Console</h3>
* Fixed various CS issues in console `make*` templates

<h3>IoC</h3>
* `Opulence\Ioc\Bootstrappers\Bootstrapper::__construct()` no longer final

<h3>ORM</h3>
* Added type-hinting to data mapper parameters and return values

<h2>v1.0.5 (2017-01-16)</h2>

<h3>Console</h3>
* Fixed #52 (improperly iterating over string when it contains multibyte chars)
* Fixed #62 (`php apex app:rename` was allowing bad namespaces)

<h3>Framework</h3>
* Fixed #55 (`Authority` was being instantiated with incorrect parameters)

<h3>IoC</h3>
* Fixed #54 (no longer assigning resolved parameters to array by reference)
* Added micro-optimizations for performance improvements

<h3>ORM</h3>
* Fixed #45 (reuse results of reflection across Id getter and setter)

<h3>Views</h3>
* Fixed bug that caused `.gitignore` files to be deleted by view cache's `flush()` methods

<h2>v1.0.4 (2017-01-06)</h2>

<h3>General</h3>
* Fixed CS issues

<h3>IoC</h3>
* Greatly simplified logic inside `Opulence\IoC\Container`
* Added `Opulence\IoC\InstanceBinding`
* Fixed factory bindings so that they're called directly rather than through `$container->callClosure()`

<h3>Views</h3>
* Added optional `$checkVars` parameter to `Opulence\Views\Caching\ICache::get()`, `has()`, and `set()`
  * Previously, Opulence was incorrectly caching views by including the variables' values, which made it relatively useless when used in the Fortune transpiler
  * Now, this parameter defaults to `false`.  To enable caching a view by its variables` values, simply set it to `true` in the various cache methods.

<h2>v1.0.3 (2017-01-02)</h2>

<h3>General</h3>
* Updated copyright years

<h3>Framework</h3>
* Fixed PHPUnit to be 5.6.* in `opulence/framework` (it was errantly still requiring 5.4.*)
* Updated the various `Assertions` classes to extend the namespaced `PHPUnit\Framework\TestCase` class rather than the soon-to-be-outdated `PHPUnit_Framework_TestCase`

<h3>IoC</h3>
* Fixed #38 (`hasBinding()` was not falling back to universal bindings when checking a targeted binding)

<h2>v1.0.2 (2016-12-16)</h2>

<h3>Console</h3>
* Fixed bug that occurred with `null` short option names

<h2>v1.0.1 (2016-12-11)</h2>

<h3>HTTP</h3>
* Fixed incorrect `use` statement in `Opulence\Http\Requests\Request`

<h3>QueryBuilders</h3>
* Changed `Opulence\QueryBuilders\Conditions\NotBetweenCondition` to not inherit from `BetweenCondition`
* Changed `Opulence\QueryBuilders\Conditions\NotInCondition` to not inherit from `InCondition`

<h3>Routing</h3>
* Simplified route parsing (#35)

<h3>Validation</h3>
* Changed `Opulence\Validation\Rules\NotInRule` to not inherit from `InRule`

<h2>v1.0.0 (2016-12-05)</h2>

<h3>General</h3>
* Released v1.0.0

<h2>v1.0.0-rc4 (2016-12-02)</h2>

<h3>General</h3>
* Fixed broken unit tests in Windows due to different newline characters

<h3>Environments</h3>
* Fixed `Opulence\Environments\Environment::setVar()` so that it does not overwrite existing environment variables

<h3>Views</h3>
* Added `Opulence\Views\Caching\ArrayCache` for testing purposes

<h2>v1.0.0-rc3 (2016-11-24)</h2>

<h3>General</h3>
* Bumped PHPUnit version to 5.6

<h3>IoC</h3>
* Closed #30 (removing bootstrappers causing you to not be able to clear framework cache)

<h2>v1.0.0-rc2 (2016-11-09)</h2>

<h3>Console</h3>
* Added better guidance to console prompts that require a yes/no answer

<h3>HTTP</h3>
* Fixed #25 (bug with how `Request::getHost()` is calculated)

<h2>v1.0.0-rc1 (2016-10-15)</h2>

<h3>Deprecated</h3>
* Removed all deprecated classes from the framework, including the entire `Bootstrappers` library

<h3>Events</h3>
* Removed `Opulence\Events\Event` and `Opulence\Events\IEvent`
* Renamed `Opulence\Events\Dispatchers\EventDispatcher` to `SynchronousEventDispatcher`
* Changed `Opulence\Events\Dispatchers\IEventDispatcher` to have only a single method:  `dispatch($eventName, $event)`
  * `$event` is no longer restricted to only being an `IEvent` - it can be a POPO
  * Events' propagation can no longer be stopped, which is for the best, architecturally
  * This will make it much easier to add asynchronous event handling in the future with things like Azure Service Bus

<h2>v1.0.0-beta7 (2016-09-22)</h2>

<h3>General</h3>
* Better optimized PSR-4 autoloading (issue #19)
* Removed many instances of `call_user_func` and `call_user_func_array` for readability (issue #20)

<h3>Deprecated</h3>
* The Environment library was slimmed down, and some recently-made-irrelevant classes were marked as deprecated:
  * `Opulence\Environments\Hosts\HostName`
  * `Opulence\Environments\Hosts\HostRegex`
  * `Opulence\Environments\Hosts\IHost`
  * `Opulence\Environments\Resolvers\EnvironmentResolver`
  * `Opulence\Environments\Resolvers\IEnvironmentResolver`

<h3>Environments</h3>
* Made all `Opulence\Environments\Environment` methods static
* Removed `Opulence\Environments\Environment::getName()` and `setName()`
  * Instead, simply read the variable that's holding that data, eg `Environment::getVar("ENV_NAME")`

<h3>Sessions</h3>
* Improved speed of session garbage collection (issue #21)

<h2>v1.0.0-beta6 (2016-08-29)</h2>
This release consolidated bootstrappers into the `Ioc` library.  This makes them more usable to people only using the `Ioc` library.  It also removes some necessary hackiness for application configuration.  All of Opulence's bootstrappers have been updated to use the new `Ioc` bootstrappers.

<h3>Bootstrappers</h3>
* The entire library has been deprecated in favor of `Opulence\Ioc\Bootstrappers`

<h3>Framework</h3>
* Added `Opulence\Framework\Configuration\Config` as a container to hold config data to be read in your bootstrappers

<h3>Ioc</h3>
* Added the following:
  * `Opulence\Ioc\Bootstrappers\Caching\FileCache`
  * `Opulence\Ioc\Bootstrappers\Caching\ICache`
  * `Opulence\Ioc\Bootstrappers\Dispatchers\BootstrapperDispatcher`
  * `Opulence\Ioc\Bootstrappers\Dispatchers\IBootstrapperDispatcher`
  * `Opulence\Ioc\Bootstrappers\Factories\BootstrapperRegistryFactory`
  * `Opulence\Ioc\Bootstrappers\Factories\CachedBootstrapperRegistryFactory`
  * `Opulence\Ioc\Bootstrappers\Factories\IBootstrapperRegistryFactory`
  * `Opulence\Ioc\Bootstrappers\Bootstrapper`
  * `Opulence\Ioc\Bootstrappers\BootstrapperRegistry`
  * `Opulence\Ioc\Bootstrappers\BootstrapperResolver`
  * `Opulence\Ioc\Bootstrappers\IBootstrapperRegistry`
  * `Opulence\Ioc\Bootstrappers\IBootstrapperResolver`
  * `Opulence\Ioc\Bootstrappers\ILazyBootstrapper`
* Removed `Bootstrapper::initialize()` functionality because it doesn't serve a purpose anymore

<h3>Query Builders</h3>
* Added conditions to `where()`, `andWhere()`, `orWhere()`, `having()`, `andHaving()`, and `orHaving()` methods on various queries (issue #9)

<h2>v1.0.0-beta5 (2016-07-05)</h2>

<h3>Deprecated</h3>
If Opulence ever moves to a standard HTTP request/response implementation, the `Routing` library shouldn't have any more dependencies on the Opulence `Http` library.  However, it still would have one because middleware previously existed in the `Http` library.  So, to potentially future-proof Opulence in the case that it adopts standardized request/response objects (not that we plan on doing so yet), the following classes are being deprecated:
  * Deprecated `Opulence\Http\Middleware\IMiddleware` in favor of `Opulence\Routing\Middleware\IMiddleware`
  * Deprecated `Opulence\Http\Middleware\MiddlewareParameters` in favor of `Opulence\Routing\Middleware\MiddlewareParameters`
  * Deprecated `Opulence\Http\Middleware\ParameterizedMiddleware` in favor of `Opulence\Routing\Middleware\ParameterizedMiddleware`

<h2>v1.0.0-beta4 (2016-06-30)</h2>

<h3>Backwards Incompatibilities</h3>
* `Opulence\Cryptography\Encryption\Encrypter` no longer accepts a string as the encryption key (issue #13)
  * An `Opulence\Cryptography\Encryption\Keys\Secret` is now passed in (`Key` and `Password` both extend `Secret`)
  * Now, a key derivation function is run on it to generate encryption and authentication keys from the secret
* Increased encryption key length stored in `ENCRYPTION_KEY` environment variable from 16 bytes to 32 bytes
  * To fix this, rerun `php apex encryption:generatekey` to create a new, suitably-long encryption key

<h3>Deprecated</h3>
Deprecated names of various dispatcher classes in favor of more descriptive `{Model}Dispatcher` class names:

* Deprecated `Opulence\Applications\Tasks\Dispatchers\IDispatcher` and `Dispatcher` in favor of `ITaskDispatcher` and `TaskDispatcher`
* Deprecated `Opulence\Bootstrappers\Dispatchers\IDispatcher` and `Dispatcher` in favor of `IBootstrapperDispatcher` and `BootstrapperDispatcher`
* Deprecated `Opulence\Events\Dispatchers\IDispatcher` and `Dispatcher` in favor of `IEventDispatcher` and `EventDispatcher`
* Deprecated `Opulence\Framework\Events\Bootstrappers\DispatcherBootstrapper` in favor of `EventDispatcherBootstrapper`
* Deprecated `Opulence\Routing\Dispatchers\IDispatcher` and `Dispatcher` in favor of `IRouteDispatcher` and `RouteDispatcher`

<h3>General</h3>
* Forced only native PHP functions in the global namespace to be used for security-related classes

<h3>Cryptography</h3>
* `Opulence\Cryptography\Encryption\Encrypter` no longer accepts a string as the encryption key (issue #13)
  * An `Opulence\Cryptography\Encryption\Keys\Secret` is now passed in (`Key` and `Password` both extend `Secret`)
  * Now, a key derivation function is run on it to generate encryption and authentication keys from the secret
  * Added `Opulence\Cryptography\Encryption\Keys\IKeyDeriver` and `Pbkdf2KeyDeriver`
  * Added `Opulence\Cryptography\Encryption\Keys\Secret`, `Key`, and `Password`
* Changed default cipher from `AES-128-CBC` to `AES-256-CTR`
* Locked down cipher selection to `AES` ciphers in `CBC` or `CTR` modes
* Updated `Opulence\Cryptography\Encryption\Encrypter` to use `random_bytes()` rather than `openssl_random_pseudo_bytes()` (issue #12)
* Increased encryption key length stored in `ENCRYPTION_KEY` from 16 bytes to 32 bytes

<h3>Routing</h3>
* Added `Opulence\Routing\Dispatchers\IMiddlewarePipeline` and `MiddlewarePipeline` to the `RouteDispatcher` constructor
* Removed required dependency on `Pipelines` library

<h2>v1.0.0-beta3 (2016-06-22)</h2>

<h3>Backwards Incompatibilities</h3>
* Passing by reference has been removed from all repositories and data mappers because it is not needed anymore.  The following methods have been updated to now pass by value:
  * `Opulence\Authentication\Clients\Orm\IClientRepository::add()`, `IClientRepository::delete()`
  * `Opulence\Authentication\Credentials\Orm\IJwtRepository::add()`, `IJwtRepository::delete()`
  * `Opulence\Authentication\Tokens\Orm\IJwtRepository::add()`, `IJwtRepository::delete()`
  * `Opulence\Authentication\Users\Orm\IUserRepository::add()`, `IUserRepository::delete()`
  * `Opulence\Authorization\Roles\Orm\IRoleMembershipRepository::add()`, `IRoleMembershipRepository::delete()`
  * `Opulence\Authorization\Roles\Orm\IRoleRepository::add()`, `IRoleRepository::delete()`
  * `Opulence\Orm\ChangeTracking\IChangeTracker::startTracking()`, `ChangeTracker::startTracking()`
  * `Opulence\Orm\DataMappers\IDataMapper::add()`, `IDataMapper::delete()`, `IDataMapper::update()`
  * `Opulence\Orm\DataMappers\CachedSqlDataMapper::add()`, `CachedSqlDataMapper::delete()`, `CachedSqlDataMapper::update()`
  * `Opulence\Orm\Repositories\IRepository::add()`, `IRepository::delete()`
  * `Opulence\Orm\Repositories\Repository::add()`, `Repository::delete()`

<h3>Deprecated</h3>
* `Opulence\Cryptography\Utilities\Strings`

<h3>Authentication</h3>
* Check the backwards incompatibilities for methods that no longer pass by reference

<h3>Authorization</h3>
* Check the backwards incompatibilities for methods that no longer pass by reference

<h3>Console</h3>
* Fixed bug that escaped all slashes in arguments when we actually wanted them for everything except escaped quotes (issue #4)

<h3>Environments</h3>
* Made the name optional in `Opulence\Environments\Environment::__construct()`

<h3>Framework</h3>
* Fixed `Opulence\Composer\Executable` so that it always returns a string, even when `shell_exec()` returns null
* Added ability to make changing the directory optional when renaming the application (issue #5)
* Added `Opulence\Framework\Views\Caching\GenericCache` for `Opulence\Caching\ICacheBridge` support
* Removed `Opulence\Cryptography\Utilities\Strings` dependency from `Opulence\Framework\Console\Commands\UuidGenerationCommand`

<h3>ORM</h3>
* Check the backwards incompatibilities for methods that no longer pass by reference

<h2>v1.0.0-beta2 (2016-06-16)</h2>

<h3>Applications</h3>
* Made the version number be overridable

<h3>Redis</h3>
* Incremented PHPRedis version from 2.2.7 to 3.0.* for PHP 7 support

<h3>Tests</h3>
* Incremented PHPUnit version from 5.2 to 5.4

<h2>v1.0.0-beta1 (2016-06-15)</h2>

<h3>General</h3>
* First beta

<h2>v1.0.0-alpha36</h2>

<h3>Backwards Incompatibilities</h3>
* Rewrote directory structure under `Opulence\Framework` to support eventual splitting of its subdirectories into their own repositories
  * This will make is possible to, for example, use a library's bootstrapper without having to download all the other bootstrappers
* Moved `Opulence\Framework\Bootstrappers\Authentication\AuthenticationBootstrapper` to `Opulence\Framework\Authentication\Bootstrappers\AuthenticationBootstrapper`
* Moved `Opulence\Framework\Bootstrappers\Authorization\AuthorizationBootstrapper` to `Opulence\Framework\Authorization\Bootstrappers\AuthorizationBootstrapper`
* Moved `Opulence\Framework\Bootstrappers\Console\Commands\CommandsBootstrapper` to `Opulence\Framework\Console\Bootstrappers\CommandsBootstrapper`
* Moved `Opulence\Framework\Bootstrappers\Console\Composer\ComposerBootstrapper` to `Opulence\Framework\Composer\Bootstrappers\ComposerBootstrapper`
* Moved `Opulence\Framework\Bootstrappers\Console\Requests\RequestsBootstrapper` to `Opulence\Framework\Console\Bootstrappers\RequestBootstrapper` (note the drop in the "s" in "RequestBootstrapper")
* Moved `Opulence\Framework\Bootstrappers\Cryptography\CryptographyBootstrapper` to `Opulence\Framework\Cryptography\Bootstrappers\CryptographyBootstrapper`
* Moved `Opulence\Framework\Bootstrappers\Events\DispatcherBootstrapper` to `Opulence\Framework\Events\Bootstrappers\DispatcherBootstrapper`
* Moved `Opulence\Framework\Bootstrappers\Http\Requests\RequestBootstrapper` to `Opulence\Framework\Http\Bootstrappers\RequestBootstrapper`
* Moved `Opulence\Framework\Bootstrappers\Http\Routing\RouterBootstrapper` to `Opulence\Framework\Routing\Bootstrappers\RouterBootstrapper`
* Moved `Opulence\Framework\Bootstrappers\Http\Sessions\SessionBootstrapper` to `Opulence\Framework\Sessions\Bootstrappers\SessionBootstrapper`
* Moved `Opulence\Framework\Bootstrappers\Http\Views\ViewBootstrapper` to `Opulence\Framework\Views\Bootstrappers\ViewBootstrapper`
* Moved `Opulence\Framework\Bootstrappers\Http\Views\ViewFunctionsBootstrapper` to `Opulence\Framework\Views\Bootstrappers\ViewFunctionsBootstrapper`
* Moved `Opulence\Framework\Bootstrappers\Validation\ValidatorBootstrapper` to `Opulence\Framework\Validation\Bootstrappers\ValidatorBootstrapper`
* Moved `Opulence\Framework\Http\Middleware\Authenticate` to `Opulence\Framework\Authentication\Http\Middleware\Authenticate`
* Moved `Opulence\Framework\Http\Middleware\Session` to `Opulence\Framework\Sessions\Http\Middleware\Session`
* Moved `Opulence\Framework\Testing\PhpUnit\Console\Assertions\ResponseAssertions` to `Opulence\Framework\Console\Testing\PhpUnit\Assertions\ResponseAssertions`
* Moved `Opulence\Framework\Testing\PhpUnit\Console\CommandBuilder` to `Opulence\Framework\Console\Testing\PhpUnit\CommandBuilder`
* Moved `Opulence\Framework\Testing\PhpUnit\Console\IntegrationTestCase` to `Opulence\Framework\Console\Testing\PhpUnit\IntegrationTestCase`
* Moved `Opulence\Framework\Testing\PhpUnit\Http\Assertions\ResponseAssertions` to `Opulence\Framework\Http\Testing\PhpUnit\Assertions\ResponseAssertions`
* Moved `Opulence\Framework\Testing\PhpUnit\Http\Assertions\ViewAssertions` to `Opulence\Framework\Http\Testing\PhpUnit\Assertions\ViewAssertions`
* Moved `Opulence\Framework\Testing\PhpUnit\Http\IntegrationTestCase` to `Opulence\Framework\Http\Testing\PhpUnit\IntegrationTestCase`
* Moved `Opulence\Framework\Testing\PhpUnit\Http\RequestBuilder` to `Opulence\Framework\Http\Testing\PhpUnit\RequestBuilder`

<h3>Framework</h3>
* Fixed `Make*` commands to use latest directory structure in skeleton project
* Fixed bug that prevented `UuidGenerationCommand` from being included

<h2>v1.0.0-alpha35</h2>

<h3>General</h3>
* Fixed Composer PHP versions in individual libraries
* Updated to SuperClosure v2.2.0

<h3>Backwards Incompatibilities</h3>
* Removed ability to "make" classes via an IoC container in `Opulence\Pipelines\Pipeline`
* Removed `IContainer` from `Pipeline::__construct()`
* A `Router` object is now required in the `Router::group()` callback, eg `$router->group($config, function (Router $router) {...})`
* Completely re-architected the IoC container (see below)

<h3>Applications</h3>
* Changed `Opulence\Applications\Application::start()` and `shutdown()` to accept optional `callable` rather than `Closure`

<h3>Authentication</h3>
* Removed `Opulence\Authentication\EntityTypes`
* Added JWT support
* Removed `Opulence\Authentication\Tokens\IToken` and `Token`
* Added `Opulence\Authentication\IAuthenticator`

<h3>Authorization</h3>
* Added `Opulence\Authorization` library

<h3>Bootstrappers</h3>
* Updated to use the new IoC container (see below)

<h3>Cryptography</h3>
* Removed `Strings` from `Opulence\Cryptography\Encryption\Encrypter::__construct()`
* Removed `Strings` from `Opulence\Cryptography\Hashing\Hasher::__construct()`
* Added `Opulence\Cryptography\Utilities\Strings::generateUuidV4()`

<h3>Databases</h3>
* Renamed `Opulence\Databases\Providers\Types\Factories\TypeMapperFactory::create()` to `createTypeMapper()`

<h3>Framework</h3>
* Added `Opulence\Framework\Bootstrappers\Authentication\AuthenticationBootstrapper`
* Added `Opulence\Framework\Bootstrappers\Authorization\AuthorizationBootstrapper`
* Updated `Opulence\Framework\Bootstrappers\Cryptography\CryptographyBootstrapper` to not pass `Strings` to `getEncrypter()` nor `getHasher()`
* Added `Opulence\Framework\Console\Commands\UuidGenerationCommand`
* Fixed various command templates to include PHP 7 return types
* Updated `Opulence\Framework\Bootstrappers\Http\Routing\RouterBootstrapper` to inject an `Opulence\Routing\Dispatchers\ContainerDependencyResolver` into the router dispatcher
* Removed `Opulence\Cryptography\Utilities\Strings` dependency from `Opulence\Framework\Console\Commands\EncryptionKeyGenerationCommand`
* Removed `Opulence\Cryptography\Utilities\Strings` dependency from `Opulence\Framework\Http\CsrfTokenChecker`

<h3>HTTP</h3>
* Added parameterized middleware support
* Fixed bug that did not attempt to check the current request method's parameter collection when calling `Opulence\Http\Requests\Request::getInput()`
* Fixed bug that returned `null` when getting the previous URL and none is set, nor is the referrer header set

<h3>IoC</h3>
* Completely rewrote IoC container to specify binding scope when you bind to the container rather than when you resolve something from it
  * For example, before, to get a singleton, you'd call `$container->bind($interface, $singletonClass)`, and then `$container->makeShared($interface)`
  * Now, you call `$container->bindSingleton($interface, $singletonClass)`, and then `$container->resolve($interface)` to resolve an instance
* To bind a factory that will return the instance to bind, use `$container->bindFactory($interface, $factory)` method
* To bind an instance of an object, use `$container->bindInstance($interface, $instance)`
* To bind a prototype (non-singleton) class, use `$container->bindPrototype($interface, $prototypeClass)`
  * Useful for interfaces previously resolve using `$container->makeNew($interface)`
* To bind a singleton (shared) class, use `$container->bindSingleton($interface, $singletonClass)`
  * Useful for interfaces previously resolved using `$container->makeShared($interface)`
* To specify a targeted binding, use `$container->for($targetClass, $callback)->bindSingleton($interface, $singletonClass)`
  * The callback should contain all bindings and resolutions that are targeted
  * The only methods that are targetable are `bindFactory()`, `bindInstance()`, `bindPrototype()`, `bindSingleton()`, `hasBinding()`, `resolve()`, and `unbind()`
* To specify primitive values, do so when you bind to the container, eg `$container->bindSingleton($interface, $singletonClass, $arrayOfPrimitives)`
  * You no longer specify primitives when resolving dependencies - only when you bind them
* To call a closure, use `$container->callClosure($closure, $arrayOfPrimitives)`
* To call a method, use `$container->callMethod($instance, $methodName, $arrayOfPrimitives)`
* To check if an interface has a binding, use `$container->hasBinding($interface)`
* To unbind an interface, use `$container->unbind($interface)`

<h3>Memcached</h3>
* Renamed `Opulence\Memcached\Types\Factories\TypeMapperFactory::create()` to `createTypeMapper()`

<h3>ORM</h3>
* Added `Opulence\Orm\Ids\Generators\UuidV4Generator`

<h3>Redis</h3>
* Renamed `Opulence\Redis\Types\Factories\TypeMapperFactory::create()` to `createTypeMapper()`

<h3>Routing</h3>
* `Router::group()` now requires a `Router` object as a parameter, eg `$router->group($config, function (Router $router) {...})`
* Updated to use a new interface `Opulence\Routing\Dispatchers\IDependencyResolver` to resolve interfaces (removed dependency on IoC container)
* Added `Opulence\Routing\Dispatchers\ContainerDependencyResolver` that uses Opulence's container library so it is easy to get up and running
* Fixed bug that prevented parameterized middleware from being added to a route

<h3>Sessions</h3>
* Removed `Opulence\Cryptography\Utilities\Strings` dependency in `Opulence\Sessions\Ids\Generators\IdGenerator`
* Added `Opulence\Sessions\Handlers\ISessionEncrypter`, `SessionEncrypter`, and `SessionEncryptionException`
* Removed required dependency on Cryptography library to encrypt/decrypt session data and changed it to use above classes

<h3>Validation</h3>
* Renamed `Opulence\Validation\Rules\Errors\ErrorTemplateRegistry::get()` to `getErrorTemplate()`
* Renamed `Opulence\Validation\Rules\Errors\ErrorTemplateRegistry::has()` to `hasErrorTemplate()`
* Renamed `Opulence\Validation\Rules\RuleExtensionRegistry::get()` to `getRule()`
* Renamed `Opulence\Validation\Rules\RuleExtensionRegistry::has()` to `hasRule()`

<h3>Views</h3>
* Renamed `Opulence\Views\Compilers\ICompilerRegistry::get()` to `getCompiler()`
* Renamed `Opulence\Views\Factories\IViewFactory::create()` to `createView()`
* Renamed `Opulence\Views\Factories\IViewFactory::has()` to `hasView()`

<h2>v1.0.0-alpha34</h2>

<h3>Routing</h3>
* Fixed double-compile bug for controller responses

<h2>v1.0.0-alpha33</h2>

<h3>General</h3>
* Dropped support for ALL PHP 5.\* versions
* Added scalar type hints and return types
* Adopted all other PHP 7 techniques that required hacks in PHP 5

<h2>v1.0.0-alpha32</h2>

<h3>Authentication</h3>
* Added `Opulence\Authentication\AuthenticationStatusTypes`
* Added user Id to `Opulence\Authentication\Tokens\IToken` and `Token`
* Changed `DateTime` instances to `DateTimeImmutable` in `Opulence\Authentication\Tokens\IToken` and `Token`

<h3>Build</h3>
* Renamed `build/commit.sh` to `build/git.sh`
* Added checking out and merging of pull requests

<h3>Databases</h3>
* Changed time/date related fields in type mappers to accept `DateTimeInterface` rather than `DateTime` to allow for `DateTimeImmutable` arguments

<h3>Debug</h3>
* Added support for different response formats depending on the request type

<h3>HTTP</h3>
* Added ability to stream responses via `Opulence\Http\Responses\StreamResponse`
* Fixed bug that prevented `Request::getInput()` from searching query parameters on post requests when no post parameter matched the input name
* Added support for trusted proxies
* Removed all `Request::METHOD_*` constants, moved them to `Opulence\Http\Requests\RequestMethods` class, and dropped `METHOD_` from constants' names
* Added `Opulence\Http\Requests\RequestHeaders`
* Added `Opulence\Http\Requests::getPort()`
* Renamed `Opulence\Http\Requests::getIPAddress()` to `getClientIPAddress()`

<h3>Memcached</h3>
* Changed time/date related fields in type mappers to accept `DateTimeInterface` rather than `DateTime` to allow for `DateTimeImmutable` arguments

<h3>Pipelines</h3>
* Rewrote to include a fluent syntax, eg `(new Pipeline($container))->send("foo")->through($stages)->then($callback)->execute()`

<h3>Query Builders</h3>
* Added ability to create select query without a table (useful for stored procedures)

<h3>Redis</h3>
* Changed time/date related fields in type mappers to accept `DateTimeInterface` rather than `DateTime` to allow for `DateTimeImmutable` arguments

<h3>Users</h3>
* Moved `Opulence\Users\*` to `Opulence\Authentication\Users\*`
* Made all `DateTime` objects instances of `DateTimeImmutable`
* Removed `Opulence\Users\Factories\UserFactory`
* Removed `Opulence\Users\GuestUser`

<h3>Validation</h3>
* Added `Opulence\Validation` library
* Added `Opulence\Framework\Bootstrappers\Validation\ValidatorBootstrapper`

<h2>v1.0.0-alpha31</h2>

<h3>Testing</h3>
* Renamed `Opulence\Framework\Testing\PhpUnit\ApplicationTestCase` to `IntegrationTestCase`
* Renamed `Opulence\Framework\Testing\PhpUnit\Console\ApplicationTestCase` to `IntegrationTestCase`
* Renamed `Opulence\Framework\Testing\PhpUnit\Http\ApplicationTestCase` to `IntegrationTestCase`

<h2>v1.0.0-alpha30</h2>

<h3>Testing</h3>
* Renamed `Opulence\Framework\Testing\PhpUnit\Console\ApplicationTestCase::call()` to `execute()`
* Added `Opulence\Framework\Testing\PhpUnit\Console\CommandBuilder`

<h2>v1.0.0-alpha29</h2>

<h3>Bootstrappers</h3>
* Removed `Opulence\Framework\Bootstrappers\Php\PhpBootstrapper`

<h3>Console</h3>
* Added `app:up` and `app:down` console commands

<h3>Debug</h3>
* Added special template for 503 exceptions

<h3>HTTP</h3>
* Added `HTTP_ACCEPT` default server var to `Request`
* Changed `Cookie` to accept either a `DateTime` or an int

<h3>Testing</h3>
* Added `Opulence\Framework\Testing\PhpUnit\Console\Assertions\ResponseAssertions`
* Added `Opulence\Framework\Testing\PhpUnit\Http\Assertions\ResponseAssertions`
* Added `Opulence\Framework\Testing\PhpUnit\Http\Assertions\ViewAssertions`
* Moved all console `assert***()` methods to `Opulence\Framework\Testing\PhpUnit\Console\Assertions\ResponseAssertions`
* Moved all HTTP `assertResponse***()` methods to `Opulence\Framework\Testing\PhpUnit\Http\Assertions\ResponseAssertions`
* Moved all HTTP `assertView***()` methods to `Opulence\Framework\Testing\PhpUnit\Http\Assertions\ViewAssertions`

<h2>v1.0.0-alpha28</h2>

<h3>Testing</h3>
* Renamed `Opulence\Framework\Testing\PhpUnit\Http\RequestBuilder::withEnv()` to `withEnvironmentVars()`

<h2>v1.0.0-alpha27</h2>

<h3>Testing</h3>
* Fixed bug that prevented assertions from working if they weren't chained to `RequestBuilder` calls

<h2>v1.0.0-alpha26</h2>

<h3>HTTP</h3>
* Added `Opulence\Http\Requests\Request::createFromUrl()`

<h3>Testing</h3>
* Removed `$method` and `$path` parameters from `Opulence\Framework\Testing\PhpUnit\Http\ApplicationTestCase::route()`
* Added `Opulence\Framework\Testing\PhpUnit\Http\RequestBuilder`
* Added following methods to `Opulence\Framework\Testing\PhpUnit\Http\ApplicationTestCase`:
  * `assertResponseJsonEquals()`
  * `delete()`
  * `get()`
  * `head()`
  * `options()`
  * `patch()`
  * `post()`
  * `put()`
* All assertions in `Opulence\Framework\Testing\PhpUnit\Console\ApplicationTestCase` and `Opulence\Framework\Testing\PhpUnit\Http\ApplicationTestCase` now return `$this` for method chaining

<h2>v1.0.0-alpha25</h2>

<h3>HTTP</h3>
* Added ability to specify raw body in `Request::createFromGlobals()` and `Request::__construct()`
* Added ability to override globals in `Request::createFromGlobals()`c

<h3>Views</h3>
* Added `catch()` for `Throwable` exceptions in `PhpCompiler::compile()`
* Changed `Opulence\Views\Compilers\ICompiler::compile()` to throw `Exception` and `Throwable` rather than just `ViewCompilerException`

<h2>v1.0.0-alpha24</h2>

<h3>ORM</h3>
* Fixed critical bug that did prevented entities with no Id generators from being added through the data mapper in `UnitOfWork::commit()`

<h2>v1.0.0-alpha23</h2>

<h3>Bootstrappers</h3>
* Fixed `Opulence\Bootstrappers\Dispatchers\Dispatcher` to accept `Opulence\Applications\Tasks\IDispatcher`

<h3>ORM</h3>
* Changed `Opulence\Orm\IEntityRegistry::runAggregateRootChildFunctions()` to `runAggregateRootCallbacks()`

<h2>v1.0.0-alpha22</h2>

<h3>Bootstrappers</h3>
* Fixed bug that prevented lazy bootstrappers from having targeted bindings

<h3>IoC</h3>
* Added new target parameter to `Opulence\Ioc\IContainer::make()`, `makeNew()`, and `makeShared()`

<h3>ORM</h3>
* Extracted interface `Opulence\Orm\IUnitOfWork` to `Opulence\Orm\UnitOfWork`
* Renamed `Opulence\Orm\Repositories\Repo` to `Repository` and `IRepo` to `IRepository`
* Updated `Opulence\Orm\Repositories\Repo` to use `IUnitOfWork`
* Moved `Opulence\Orm\Ids\BigIntSequenceIdGenerator`, `IdGenerator`, `IntSequenceIdGenerator` to `Opulence\Orm\Ids\Generators` namespace
* Added `Opulence\Orm\Ids\Generators\IIdGenerator`
* Added `Opulence\Orm\Ids\Generators\SequenceIdGenerator`
* Removed `Opulence\Orm\Ids\Generators\IdGenerator`
* Moved `Opulence\Orm\Ids\IdAccessorRegistry` and `IIdAccessorRegistry` to `Opulence\Orm\Ids\Accessors` namespace
* Changed `Opulence\Orm\UnitOfWork::getDataMapper()`, `getScheduledEntityDeletions()`, `getScheduledEntityInsertions()`, and `getScheduledEntityUpdates` to protected
* Removed `Opulence\Orm\DataMappers\ISqlDataMapper` and moved its constants to `SqlDataMapper`
* Removed `Opulence\Orm\DataMappers\SqlDataMapper::getIdGenerator()` and `setIdGenerator()`
* Changed `Opulence\Orm\DataMappers\SqlDataMapper` to accept `IConnection $readConnection` and `IConnection $writeConnection` rather than a `ConnectionPool` in its constructor

<h3>Sessions</h3>
* For consistency, moved `Opulence\Sessions\Ids\IdGenerator` and IIdGenerator` to `Opulence\Sessions\Ids\Generators` namespace

<h2>v1.0.0-alpha21</h2>

<h3>Views</h3>
* Fixed bug that did not extract included views' vars
* Included view vars are now completely isolated from vars set outside the included view

<h2>v1.0.0-alpha20</h2>

<h3>Debug</h3>
* Improved default page template for exceptions in development environment

<h3>Framework</h3>
* Added `currentRouteIs()` view function

<h3>HTTP</h3>
* Added `Opulence\Http\Requests\Request::isUrl()`

<h3>Routing</h3>
* Changed `Opulence\Routing\Url` to `Opulence\Routing\Urls`
* Added `Opulence\Routing\Urls\UrlGenerator::createRegexFromName()`

<h2>v1.0.0-alpha19</h2>

<h3>General</h3>
* Updated libraries' composer.json dependencies

<h3>Debug</h3>
* Added `LoggerInterface` and levels to log and throw in `Opulence\Debug\Errors\Handlers\ErrorHandler`

<h2>v1.0.0-alpha18</h2>

<h3>Environments</h3>
* Moved `Opulence\Applications\Environments` to its own library `Opulence\Environments`

<h2>v1.0.0-alpha17</h2>

<h3>Applications</h3>
* `Opulence\Applications\Application::start()` and `shutDown()` no longer catch all exceptions

<h3>Exceptions</h3>
* Any re-thrown exceptions now set the original exception in the previous exception property

<h2>v1.0.0-alpha16</h2>

<h3>Framework</h3>
* Fixed framework cache flushing after renaming the application

<h2>v1.0.0-alpha15</h2>

<h3>Debug</h3>
* Added `Opulence\Debug` library
* Added exception handlers and renderers to kernels

<h3>Framework</h3>
* Added `Opulence\Framework\Debug` classes
* Moved `Opulence\Framework\Console\Kernel` and `StatusCodes` to `Opulence\Console` namespace
* Removed `Opulence\Framework\Testing\PhpUnit\ApplicationTestCase::setApplicationAndIocContainer()`

<h3>Memcached</h3>
* Fixed bug that did not set timezone of `DateTime` objects created from Memcached timestamps

<h3>Redis</h3>
* Fixed bug that did not set timezone of `DateTime` objects created from Redis timestamps

<h2>v1.0.0-alpha14</h2>

<h3>Paths</h3>
* Moved `Opulence\Applications\Paths` to `Opulence\Bootstrappers\Paths` because it was no longer being used in `Applications` namespace

<h2>v1.0.0-alpha13</h2>

<h3>General</h3>
* Added develop branch
* Renamed `/app` directory to `/src`
* Renamed `/tests/app` directory to `/tests/src`

<h3>Applications</h3>
* Removed `Opulence\Applications\Application::setEnvironment()`

<h3>Console</h3>
* Updated references to `app` directory to `src` in various console commands

<h3>Databases</h3>
* Moved `Opulence\Databases\Pdo` namespace to `Opulence\Databases\Adapters\Pdo`
* Removed `Opulence\Databases\IConnection::getTypeMapper()`
* Moved `Opulence\Databases\Providers\TypeMapper` and `Opulence\Databases\Providers\Types` namespace
* Removed `Opulence\Databases\Providers\Types\TypeMapper::getProvider()` and `setProvider()`
* Added `Opulence\Databases\Providers\Types\Factories\TypeMapperFactory`

<h3>Memcached</h3>
* Removed type mapper from `Opulence\Memcached\Memcached::__construct()`
* Removed `Opulence\Memcached\Memcached::getTypeMapper()`
* Moved `Opulence\Memcached\TypeMapper` and `Opulence\Memcached\Types` namespace
* Added `Opulence\Memcached\Types\Factories\TypeMapperFactory`

<h3>Redis</h3>
* Removed type mapper from `Opulence\Redis\Redis::__construct()`
* Removed `Opulence\Redis\Redis::getTypeMapper()`
* Moved `Opulence\Redis\TypeMapper` and `Opulence\Redis\Types` namespace
* Added `Opulence\Redis\Factories\TypeMapperFactory`

<h2>v1.0.0-alpha12</h2>

<h3>Cryptography</h3>
* Removed dependency on Symfony for secure string comparisons
* Added `Opulence\Cryptography\Utilities\Strings::getRandomBytes()`
* Changed `Opulence\Cryptography\Utilities\Strings::getRandomString()` to throw `RuntimeException` on error
* Removed `Opulence\Cryptography\CryptographicException` because it wasn't being used by anything

<h2>v1.0.0-alpha11</h2>

<h3>Bootstrappers</h3>
* Moved Bootstrappers into their own namespace: `Opulence\Bootstrappers`
* Added Bootstrapper library

<h2>v1.0.0-alpha10</h2>

<h3>Applications</h3>
* Removed `Paths` and `IContainer` parameters from `Opulence\Applications\Application`

<h3>Framework</h3>
* Changed `Opulence\Framework\Testing\PhpUnit\ApplicationTestCase::setApplication()` to `setApplicationAndIocContainer()`
* Removed `Opulence\Framework\Testing\PhpUnit\ApplicationTestCase::getApplication()`

<h2>v1.0.0-alpha9</h2>

<h3>Build</h3>
* Updated build bash script

<h2>v1.0.0-alpha8</h2>

<h3>General</h3>
* Fixed more capitalization mismatches between file names and class names

<h2>v1.0.0-alpha7</h2>

<h3>General</h3>
* Changed PHPDoc format for all classes/interfaces/traits
* Changed capitalization of namespace/class name/variable acronyms to follow pascal case
* Changed all directory names to match namespace capitalization

<h2>v1.0.0-alpha6</h2>

<h3>HTTP</h3>
* Added support for detection of HTTP methods besides GET/POST for form requests
* Made HTTP headers case-insensitive
* Renamed `Opulence\HTTP\Parameters` to `Collection`

<h3>Testing</h3>
* Moved all classes under `Opulence\Framework\Tests` to `Opulence\Framework\Testing\PHPUnit`

<h3>Views</h3>
* Added `httpMethodInput()` Fortune view function to add support for HTTP methods besides GET/POST

<h2>v1.0.0-alpha5</h2>

<h3>ORM</h3>
* Added ability to register an array of class names in `Opulence\ORM\Ids\IIdAccessorRegistry::registerIdAccessors()`

<h3>Views</h3>
* Moved `Opulence\Views\Factories\Resolvers\IViewNameResolver` and `FileViewNameResolver` to `Opulence\Views\Factories\IO` namespace

<h2>v1.0.0-alpha4</h2>

<h3>ORM</h3>
* Fixed data mapper "make:" commands to not include `IEntity` type hint in methods
* Re-added `Opulence\ORM\IEntity` and added Id accessors for classes that implement `IEntity` to reduce boilerplate code
* Changed all private methods in `Opulence\ORM\ChangeTracking\ChangeTracker` to `protected` to make extending easier
* Changed all private methods in `Opulence\ORM\UnitOfWork` to `protected` to make extending easier

<h2>v1.0.0-alpha3</h2>

<h3>ORM</h3>
* Made all of ORM accept POPOs rather than IEntity
* Added `Opulence\ORM\ChangeTracking\IChangeTracker` and `ChangeTracker`
* Added `Opulence\ORM\Ids\IIdAccessorRegistry` and `IdAccessorRegistry`
* Added `IIdAccessorRegistry` and `IChangeTracker` parameters to `UnitOfWork` and `EntityRegistry` constructors
* Removed `Opulence\ORM\IEntityRegistry::hasChanged()` and `registerComparisonFunction()`
* Removed `Opulence\ORM\IEntity`
* Removed any `IEntity` type-hints

<h2>v1.0.0-alpha2</h2>

<h3>Views</h3>
* Fixed bug that prevented `.fortune.php` files from being resolved by the file name resolver

<h2>v1.0.0-alpha1</h2>

<h3>General</h3>
* Created first alpha release
* Removed forms library
* Updated Composer configs to latest branch

<h2>v0.6.17</h2>

<h3>Bootstrappers</h3>
* Renamed `Opulence\Applications\Bootstrappers\Caching\Cache` to `FileCache`

<h3>Console</h3>
* Removed `Opulence\Console\Responses\Compilers\Compiler::getElements()`
* Added `Opulence\Console\Responses\Compilers\ICompiler::registerElement()`
* Added `Opulence\Console\Responses\Compilers\ElementRegistrant` to register built-in Apex elements
* Moved `Opulence\Console\Responses\Formatters\Elements` namespace to `Opulence\Console\Responses\Compilers\Elements`
* Added "Apex" to various commands that show title of console library

<h3>Databases</h3>
* Moved `ConnectionPool`, `MasterSlaveConnectionPool`, and `SingleServerConnectionPool` to new `Opulence\Databases\ConnectionPools` namespace
* Added `Opulence\Databases\ConnectionPools\Strategies\ServerSelection\IServerSelectionStrategy`, `SingleServerSelectionStrategy`, and `RandomServerSelectionStrategy`
* Added optional slave server selection strategy parameter to `MasterSlaveConnectionPool` constructor

<h3>ORM</h3>
* Renamed `Opulence\ORM\IEntityRegistry::register()` to `registerEntity()` and `deregister()` to `deregisterEntity()`
* Changed `Opulence\ORM\DataMappers\SQLDataMapper::read()` to accept the value-type parameter before the `$expectingSingleResult` parameter
* Fixed `Opulence\ORM\DataMappers\ICachedSQLDataMapper` to extend `IDataMapper`, not `ISQLDataMapper`
* Removed `Opulence\ORM\Repositories\ActionTypes` because it wasn't being used anywhere

<h3>Routing</h3>
* Changed "variables" group parameter to "vars"
* Renamed `Opulence\Routing\Routes\Caching\Cache` to `FileCache`

<h3>Sessions</h3>
* Renamed `Opulence\Sessions\Ids\IIdGenerator::isIdValid()` to `idIsValid()`

<h3>Views</h3>
* Removed `$fileSystem` parameter from `Opulence\Views\Caching\FileCache`
* Removed reliance on `Opulence\Files` in `composer.json`
* Renamed `Opulence\Views\Caching\Cache` to `FileCache`

<h2>v0.6.16</h2>

<h3>Console</h3>
* Officially titled the console library "Apex"

<h2>v0.6.15</h2>

<h3>Cache</h3>
* Refactored `Opulence\Cache\MemcachedBridge` to use `Opulence\Memcached\Memcached` and accept name of default client
* Refactored `Opulence\Cache\RedisBridge` to use `Opulence\Redis\Redis` and accept name of default client

<h3>Memcached</h3>
* Added `Opulence\Memcached\Memcached`
* Removed `Opulence\Memcached\OpulenceMemcached`
* Removed `Opulence\Memcached\Server`

<h3>Redis</h3>
* Added `Opulence\Redis\Redis`
* Removed `Opulence\Redis\IRedis`
* Removed `Opulence\Redis\PHPRedis`
* Removed `Opulence\Redis\Predis`
* Removed `Opulence\Redis\Server`
* Removed `Opulence\Redis\TRedis`

<h2>v0.6.14</h2>

<h3>Environments</h3>
* Changed `IEnvironmentDetector::detect()` to `resolve()`, which now accepts a host name parameter
* Moved `Opulence\Applications\Environments\IEnvironmentDetector` and `EnvironmentDetector` to namespace `Opulence\Applications\Environments\Resolvers`

<h3>Framework</h3>
* Updated `Opulence\Framework\Bootstrappers\HTTP\Views\ViewBootstrapper` to inject an `IViewReader` rather than a `FileSystem` into the `ViewFactory`

<h3>Views</h3>
* Moved `Opulence\Views\Factories\IViewNameResolver` and `FileViewNameResolver` to namespace `Opulence\Views\Factories\Resolvers`
* Added `Opulence\Views\Factories\IO\IViewReader` and `FileViewReader`
* Refactored `ViewFactory` to accept an `IViewReader` rather than `FileSystem` to make the factory more flexible when it comes to view storage

<h2>v0.6.13</h2>

<h3>Bootstrappers</h3>
* Added `Opulence\Applications\Bootstrappers\Bootstrapper::initialize()` to run before absolutely anything else in the bootstrapper

<h3>Framework</h3>
* Added `Opulence\Framework\Bootstrappers\PHP\PHPBootstrapper`
* Removed `Opulence\Framework\setupcheck.php` because it's not necessary with the new bootstrapper

<h3>General</h3>
* Fixed various PSR-2 formatting issues

<h3>Views</h3>
* Added ability to use `<% show %>` directive to both end the current part and show it
* Changed `Opulence\Views\Factories\ViewFactory::registerBuilder()` to accept a closure that accepts an `Opulence\Views\IVew` parameter and returns the built view
  * This differs from before when the closure would simply return an instance of `Opulence\Views\Factories\IViewBuilder`

<h2>v0.6.12</h2>

<h3>General</h3>
* Switched to PSR-2 code style

<h3>Routing</h3>
* Fixed bug when generating URLs with brackets
* Removed `Opulence\Routing\Routes\Compilers\Parsers\IParser::getVariableMatchingRegex()`
* Changed `Opulence\Routing\URL\URLGenerator::createFromName()` and Fortune view function `route()` to accept a variable-length number of parameters as arguments rather than bundling them all in an array

<h2>v0.6.11</h2>

<h3>Routing</h3>
* Changed path variables from being in `/{foo}` format to `/:foo` format
* Added ability to make path variables optional using brackets, eg `/foo[/bar]`

<h2>v0.6.10</h2>

<h3>Bootstrappers</h3>
* Added `Bootstrapper` suffix to all bootstrapper class names

<h2>v0.6.9</h2>

<h3>General</h3>
* Switched to PSR-2 spacing between `namespace` and `use` statements

<h2>v0.6.8</h2>

<h3>ORM</h3>
* To provide better abstraction `Opulence\ORM\Repositories\IRepo::getDataMapper()`, `getUnitOfWork()`, and `setDataMapper()` have been removed

<h2>v0.6.7</h2>

<h3>HTTP</h3>
* Renamed `Opulence\HTTP\Requests\UploadedFile::getActualMimeType()` to `getMimeType()`
* Renamed `Opulence\HTTP\Requests\UploadedFile::getTempName()` to `getTempFilename()`

<h2>v0.6.6</h2>

<h3>HTTP</h3>
* Added `Opulence\HTTP\Requests\UploadedFile` and `Files`
* Uploaded files now instantiate `UploadedFile` objects rather than simple arrays

<h2>v0.6.5</h2>

<h3>Environment</h3>
* `Opulence\Applications\Environments\Environment::getVariable()` renamed to `getVar()`
* `Opulence\Applications\Environments\Environment::setVariable()` renamed to `setVar()`

<h3>Routing</h3>
* `Opulence\Routing\Routes\Compilers\Parsers\Parser::getVariableMatchingRegex()` renamed to `getVarMatchingRegex()`
* `Opulence\Routing\Routes\Route::getVariableRegex()` renamed to `getVarRegex()`
* `Opulence\Routing\Routes\Route::getVariableRegexes()` renamed to `getVarRegexes()`
* `Opulence\Routing\Routes\Route::setVariableRegex()` renamed to `setVarRegex()`
* `Opulence\Routing\Routes\Route::setVariableRegexes()` renamed to `setVarRegexes()`
* `Opulence\Routing\Routes\CompiledRoute::getPathVariable()` renamed to `getPathVar()`
* `Opulence\Routing\Routes\CompiledRoute::getPathVariables()` renamed to `getPathVars()`

<h2>v0.6.4</h2>

<h3>Routing</h3>
* Added setter methods for view factory and view compiler in base controller class

<h2>v0.6.3</h2>

<h3>Routing</h3>
* Renamed `Controller::compiler` to `viewCompiler` to avoid naming conflicts

<h2>v0.6.2</h2>

<h3>General</h3>
* Re-split Git subtrees

<h2>v0.6.1</h2>

<h3>General</h3>
* Re-split Git subtrees

<h3>Events</h3>
* Manually re-added this library after some issues with Git

<h2>v0.6.0</h2>

<h3>General</h3>
* Renamed RDev to Opulence, renamed all namespaces to `Opulence\...`
* Renamed `app/rdev` directory to `app/opulence`
* Renamed `tests/app/rdev` directory to `tests/app/opulence`

<h3>Memcached</h3>
* Renamed `RDevMemcached` to `OpulenceMemcached`

<h3>Redis</h3>
* Renamed `RDevPHPRedis` to `OpulenceRedis`
* Renamed `RDevPredis` to `OpulencePredis`

<h3>Views</h3>
* Renamed `Opulence\Views\Templates` to `Opulence\Views\View` and `ITemplate` to `IView`
* Renamed `Opulence\Views\Factories\ITemplateFactory` to `IViewFactory` and `TemplateFactory` to `ViewFactory`
* Renamed `Opulence\Views\IBuilder` to `Opulence\Views\Factories\IViewBuilder`
* Added `Opulence\Views\Compilers\Fortune` namespace
* Changed default statement delimiter from `{% %}` to `<% %>`

<h2>v0.5.7</h2>

<h3>Applications</h3>
* Renamed `RDev\Applications\Environments\Host` to `RDev\Applications\Environments\Hosts\HostName`
* Added `RDev\Applications\Environments\Hosts\IHost` and `HostRegex`

<h2>v0.5.6</h2>

<h3>Applications</h3>
* Renamed `RDev\Applications\Bootstrappers\IBootstrapperRegistry::registerBootstrapperClasses()` to `registerBootstrappers()`
* Renamed `RDev\Applications\Bootstrappers\IBootstrapperRegistry::getBindingsToLazyBootstrapperClasses()` to `getLazyBootstrapperBindings()`
* Renamed `RDev\Applications\Bootstrappers\IBootstrapperRegistry::getEagerBootstrapperClasses()` to `getEagerBootstrappers()`
* Added `RDev\Applications\Environments\Host`
* Added `RDev\Applications\Environments\EnvironmentDetector::registerHost()`
* Updated `EnvironmentDetector::detect()` not accept parameters

<h3>Cryptography</h3>
* Renamed `RDev\Cryptography\Hashing\IHasher::generate()` to `hash()`

<h3>View</h3>
* Removed `RDev\Views\Caching\ICache::getLifetime()` and `setLifetime()`

<h2>v0.5.5</h2>

<h3>Applications</h3>
* Renamed `RDev\Applications\Bootstrappers\IO` namespace to `Caching`
* Renamed `RDev\Applications\Bootstrappers\IO\IBootstrapperIO` to `ICache` and `BootstrapperIO` to `Cache`
* Renamed `IBootstrapperIO::read()` to `get()` and `write()` to `set()`

<h3>Framework</h3>
* Added `RDev\Framework\Bootstrappers\Routing\Router::getRouteMatchers()`
* Added ability to flush router and view cache with `php rdev framework:flushcache`

<h3>Routing</h3>
* Moved `RDev\Routing\Compilers` namespace to `RDev\Routing\Routes\Compilers`
* Added `RDev\Routing\Routes\Compilers\Matchers` namespace and classes in it
* Added `IRouteMatcher[]` parameter to `RDev\Routing\Routes\Compilers\Compiler::__construct()`
* Added `IParser` to `RDev\Routing\Router::__construct()`
* Removed `IParser` from `RDev\Routing\Routes\Compilers\Compiler::__construct()`

<h2>v0.5.4</h2>

<h3>Events</h3>
* Made `RDev\Events\Event` a concrete class

<h2>v0.5.3</h2>

<h3>Events</h3>
* Renamed `RDev\Events\Dispatchers\IDispatcher::addListener()` to `registerListener()`

<h2>v0.5.2</h2>

<h3>Events</h3>
* Fixed missing library

<h2>v0.5.1</h2>

<h3>Events</h3>
* Added all classes under `RDev\Events` namespace
* Added `RDev\Framework\Bootstrappers\Events\Dispatcher`

<h2>v0.5.0</h2>

<h3>General</h3>
* Now uses fully-qualified class names in `use` statements and PHPDoc
* Changed class names to make them readable without having to be fully-qualified

<h3>Applications</h3>
* Added `Bootstrapper::shutdown()` to handle any cleaning up that needs to be done on application shutdown
* Removed logger from `Application` constructor
  * Removed `getLogger()`, `setIoCContainer()`, and `setLogger()`
* Removed `RDev\Applications\Application::registerPreStartTask()`, `registerPostStartTask()`, `registerPreShutdownTask()`, and `registerPostShutdownTask()`
* Added `RDev\Tasks\Dispatchers\Dispatcher`
  * This now handles pre/post-start and -shutdown tasks
* Added ability to lazy-load bootstrappers by implementing `RDev\Applications\Bootstrappers\ILazyBootstrapper`
* Removed `Application::registerBootstrapper()` and `Application::registerBootstrappersTask()`
  * Added `RDev\Applications\Bootstrappers\IBootstrapperRegistry` and `BootstrapperRegistry` to act as the bootstrapper registry
  * Added `RDev\Applications\Bootstrappers\Dispatcher\IDispatcher` and `Dispatcher` to actually dispatch bootstrappers
  * Added `RDev\Applications\Bootstrappers\IO\BootstrapperIO` to read bootstrappers from storage

<h3>Authentication</h3>
* `RDev\Authentication\Credentials\Credentials` renamed to `RDev\Authentication\Credentials\CredentialCollection`
* `RDev\Authentication\Credentials\ICredentials` renamed to `RDev\Authentication\Credentials\ICredentialCollection`

<h3>Console</h3>
* Moved `RDev\Console\Kernels\Kernel` to `RDev\Framework\Console\Kernel`
* Moved `RDev\Console\Kernels\StatusCodes` to `RDev\Framework\Console\StatusCodes`
* `RDev\Console\Commands\About` renamed to `RDev\Console\Commands\AboutCommand`
* `RDev\Console\Commands\Command::setCommands()` renamed to `setCommandCollection()`
* `RDev\Console\Commands\Command::commands` renamed to `commandCollection`
* `RDev\Console\Commands\Commands` renamed to `RDev\Console\Commands\CommandCollection`
* `RDev\Console\Commands\Help` renamed to `RDev\Console\Commands\HelpCommand`
* `RDev\Console\Commands\Version` renamed to `RDev\Console\Commands\VersionCommand`
* `RDev\Console\Requests\Parsers\Argv` renamed to `RDev\Console\Requests\Parsers\ArgvParser`
* `RDev\Console\Requests\Parsers\ArrayList` renamed to `RDev\Console\Requests\Parsers\ArrayListParser`
* `RDev\Console\Requests\Parsers\String` renamed to `RDev\Console\Requests\Parsers\StringParser`
* `RDev\Console\Requests\Tokenizers\Argv` renamed to `RDev\Console\Requests\Tokenizers\ArgvTokenizer`
* `RDev\Console\Requests\Tokenizers\ArrayList` renamed to `RDev\Console\Requests\Tokenizers\ArrayListTokenizer`
* `RDev\Console\Requests\Tokenizers\String` renamed to `RDev\Console\Requests\Tokenizers\StringTokenizer`
* `RDev\Console\Responses\Console` renamed to `RDev\Console\Responses\ConsoleResponse`
* `RDev\Console\Responses\Formatters\Command` renamed to `RDev\Console\Responses\Formatters\CommandFormatter`
* `RDev\Console\Responses\Formatters\Elements\Elements` renamed to `RDev\Console\Responses\Formatters\Elements\ElementCollection`
* `RDev\Console\Responses\Formatters\Padding` renamed to `RDev\Console\Responses\Formatters\PaddingFormatter`
* `RDev\Console\Responses\Formatters\Table` renamed to `RDev\Console\Responses\Formatters\TableFormatter`
* `RDev\Console\Responses\Silent` renamed to `RDev\Console\Responses\SilentResponse`
* `RDev\Console\Responses\Stream` renamed to `RDev\Console\Responses\StreamResponse`
* Added `RDev\Console\Responses\Compilers\MockCompiler` for use in the `SilentResponse` response

<h3>Cryptography</h3>
* `RDev\Cryptography\IToken` and `Token` now under `RDev\Authentication\Tokens` namespace
* Switched from `mcrypt` to `openssl` in `Encrypter`
* Removed dependency on `mcrypt` library

<h3>Databases</h3>
* `RDev\Databases\NoSQL\Memcached` now under `RDev\Memcached` namespace
* `RDev\Databases\NoSQL\Redis` now under `RDev\Redis` namespace
* `RDev\Databases\SQL\QueryBuilders` now under `RDev\QueryBuilders` namespace
* `RDev\Databases\SQL` now under `RDev\Databases` namespace
* `RDev\Databases\SQL\Providers\MySQL` renamed to `RDev\Databases\Providers\MySQLProvider`
* `RDev\Databases\SQL\Providers\PostgreSQL` renamed to `RDev\Databases\Providers\PostgreSQLProvider`

<h3>Framework</h3>
* Added CSRF token checking
  * Added `RDev\Framework\HTTP\CSRFTokenChecker`
  * Added `RDev\Framework\HTTP\Middleware\CheckCSRFToken`
* All commands in `RDev\Framework\Console\Commands` appended with "Command"
* `RDev\Framework\Tests\Console\ApplicationTestCase::getCommands()` renamed to `getCommandCollection()`
* Added `RDev\Framework\Bootstrappers\HTTP\Sessions\Session`
* Added `RDev\Framework\HTTP\Middleware\Session`

<h3>HTTP</h3>
* Moved `RDev\HTTP\Routing` namespace to new namespace `RDev\Routing`
* Moved `RDev\HTTP\Kernels\Kernel` to `RDev\Framework\HTTP\Kernel`
* Added `Request::getFullURL()`
* Added `Request::getHost()`
* Added `Request::getUser()` and `Request::getPassword()`
* Added `Request::getPreviousURL()` and `Request::setPreviousURL()`

<h3>IoC</h3>
* Changed `RDev\IoC\IContainer::call()` and `RDev\IoC\Container::call()` to accept a `callable` rather than an instance and method name
* Added support for callbacks in bindings

<h3>Routing</h3>
* Moved `RDev\HTTP\Routing` namespace to new namespace `RDev\Routing`
* `RDev\HTTP\Routing\Routes\Routes` renamed to `RDev\Routing\Routes\RouteCollection`
* `RDev\HTTP\Routing\Router::routes` renamed to `routeCollection`
* `RDev\HTTP\Routing\Router::getRoutes()` renamed to `getRouteCollection()`
* Added `CompiledRoute::getPathVariable()`
* Fixed type-hint for `Router::getMatchedRoute()` to return type `CompiledRoute`
* Controller/method names are now passed into the `Route` constructor directly rather than through the options array
  * The following methods in `RDev\Routing\Router` have been changed to accept `$path`, `$controller`, and `$options`:
    * `any()`
    * `delete()`
    * `get()`
    * `head()`
    * `options()`
    * `patch()`
    * `post()`
    * `put()`
  * `Router::multiple()` now accepts `$methods`, `$path`, `$controller`, and `$options`
* Opened up routing to also accept non-RDev controllers

<h3>Sessions</h3>
* Removed session from the application and bootstrapper constructors
  * Removed `Application::getSession()` and `Application::setSession()`
* Added the following methods to `ISession` and `Session`:
  * `ageFlashData()`
  * `delete()`
  * `flash()`
  * `flush()`
  * `get()`
  * `getAll()`
  * `getId()`
  * `getName()`
  * `has()`
  * `hasStarted()`
  * `reflash()`
  * `regenerateId()`
  * `set()`
  * `setId()`
  * `setMany()`
  * `setName()`
  * `start()`
* Added `FileSessionHandler`
* Added `ISession::getId()` and `ISession::setId()`
* Removed `User` and `Credentials` from `ISession` and `Session`

<h3>Users</h3>
* Made `$roles` optional in `RDev\Users\User::__construct()`

<h3>Views</h3>
* added `csrfToken()` and `csrfInput()` template functions
* `RDev\Views\Cache` namespace renamed to `RDev\Views\Caching`
* `RDev\Views\Filters\XSS` renamed to `RDev\Views\Filters\XSSFilter`
* `RDev\Views\Caching\ICache::DEFAULT_GC_TOTAL` renamed to `DEFAULT_GC_DIVISOR`

<h2>v0.4.0</h2>
* Added `RDev\Framework\Tests\HTTP\ApplicationTestCase`
* Added `RDev\Framework\Tests\Console\ApplicationTestCase`
* Added `$controller` parameter to `IDispatcher::dispatch()` to be able to test the matched controller
* Added `Router::getMatchedController()` to get the matched controller
* Made `Router::getRoutes()` return by reference so that its return value can be passed by reference to the bootstrapper
* Added `composer:dump-autoload` console command
* Updated Composer dependencies

<h2>v0.3.6</h2>
* Fixed bug with `NO_VALUE` console option
* Fixed formatting in About command
* Added `encryption:generatekey` command
* Added `RDev\Framework\Bootstrappers\Cryptography\Bootstrapper`

<h2>v0.3.5</h2>
* Moved `Hasher` classes to `RDev\Cryptography\Hashing` namespace
* Added `RDev\Cryptography\Encryption\Encrypter`
* Added `RDev\Cryptography\Utilities\Strings`
