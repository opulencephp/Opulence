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
* Renamed `tests/app/rdev` directory to `tests/app/rdev`

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