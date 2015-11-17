<h2>v1.0.0-alpha15</h2>

<h3>Logging</h3>
* Changed dependency on `Monolog\Logger` to `Psr\Log\LoggerInterface`

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