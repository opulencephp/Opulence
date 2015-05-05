<h2>v0.5.0</h2>

<h3>General</h3>
* Now uses fully-qualified class names in `use` statements and PHPDoc
* Changed class names to make them readable without having to be fully-qualified

<h3>Applications</h3>
* Added `Bootstrapper::shutdown()` to handle any cleaning up that needs to be done on application shutdown

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

<h3>Databases</h3>
* `RDev\Databases\NoSQL\Memcached` now under `RDev\Memcached` namespace
* `RDev\Databases\NoSQL\Redis` now under `RDev\Redis` namespace
* `RDev\Databases\SQL\QueryBuilders` now under `RDev\QueryBuilders` namespace
* `RDev\Databases\SQL` now under `RDev\Databases` namespace
* `RDev\Databases\SQL\Providers\MySQL` renamed to `RDev\Databases\Providers\MySQLProvider`
* `RDev\Databases\SQL\Providers\PostgreSQL` renamed to `RDev\Databases\Providers\PostgreSQLProvider`

<h3>Framework</h3>
* All commands in `RDev\Framework\Console\Commands` appended with "Command"
* `RDev\Framework\Tests\Console\ApplicationTestCase::getCommands()` renamed to `getCommandCollection()`

<h3>HTTP</h3>
* Moved `RDev\HTTP\Routing` namespace to new namespace `RDev\Routing`
* Moved `RDev\HTTP\Kernels\Kernel` to `RDev\Framework\HTTP\Kernel`
* Added `CompiledRoute::getPathVariable()`
* Fixed type-hint for `Router::getMatchedRoute()` to return type `CompiledRoute`

<h3>Routing</h3>
* Moved `RDev\HTTP\Routing` namespace to new namespace `RDev\Routing`
* `RDev\HTTP\Routing\Routes\Routes` renamed to `RDev\Routing\Routes\RouteCollection`
* `RDev\HTTP\Routing\Router::routes` renamed to `routeCollection`
* `RDev\HTTP\Routing\Router::getRoutes()` renamed to `getRouteCollection()`
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
  * `has()`
  * `hasStarted()`
  * `reflash()`
  * `regenerateId()`
  * `set()`
  * `setId()`
  * `start()`
* Added `FileSessionHandler`
* Added `ISession::getId()` and `ISession::setId()`
* Removed `User` and `Credentials` from `ISession` and `Session`

<h3>Users</h3>
* Made `$roles` optional in `RDev\Users\User::__construct()`

<h3>Views</h3>
* `RDev\Views\Cache` namespace renamed to `RDev\Views\Caching`
* `RDev\Views\Filters\XSS` renamed to `RDev\Views\Filters\XSSFilter`

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