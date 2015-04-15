v0.5.0
<hr>
* Added `FileSessionHandler`
* Added `ISession::getId()` and `ISession::setId()`
* Removed `User` and `Credentials` from `ISession`
* Now uses fully-qualified class names in `use` statements and PHPDoc
* Changed class names to make them readable without having to be fully-qualified:
  * `RDev\Authentication\Credentials\Credentials` renamed to `RDev\Authentication\Credentials\CredentialCollection`
  * `RDev\Authentication\Credentials\ICredentials` renamed to `RDev\Authentication\Credentials\ICredentialCollection`
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
  * `RDev\Console\Responses\Formatters\Command` renamed to `RDev\Console\Responses\Formatters\CommandFormatter`
  * `RDev\Console\Responses\Formatters\Elements\Elements` renamed to `RDev\Console\Responses\Formatters\Elements\ElementCollection`
  * `RDev\Console\Responses\Formatters\Padding` renamed to `RDev\Console\Responses\Formatters\PaddingFormatter`
  * `RDev\Console\Responses\Formatters\Table` renamed to `RDev\Console\Responses\Formatters\TableFormatter`
  * All commands in `RDev\Framework\Console\Commands` appended with "Command"
  * `RDev\Databases\SQL\Providers\MySQL` renamed to `RDev\Databases\SQL\Providers\MySQLProvider`
  * `RDev\Databases\SQL\Providers\PostgreSQL` renamed to `RDev\Databases\SQL\Providers\PostgreSQLProvider`
  * `RDev\Framework\Tests\Console\ApplicationTestCase::getCommands()` renamed to `getCommandCollection()`
  * `RDev\HTTP\Routing\Routes\Routes` renamed to `RDev\HTTP\Routing\Routes\RouteCollection`
  * `RDev\HTTP\Routing\Router::routes` renamed to `routeCollection`
  * `RDev\HTTP\Routing\Router::getRoutes()` renamed to `getRouteCollection()`
  * `RDev\Views\Cache` namespace renamed to `RDev\Views\Caching`
  * `RDev\Views\Filters\XSS` renamed to `RDev\Views\Filters\XSSFilter`

v0.4.0
<hr>
* Added `RDev\Framework\Tests\HTTP\ApplicationTestCase`
* Added `RDev\Framework\Tests\Console\ApplicationTestCase`
* Added `$controller` parameter to `IDispatcher::dispatch()` to be able to test the matched controller
* Added `Router::getMatchedController()` to get the matched controller
* Made `Router::getRoutes()` return by reference so that its return value can be passed by reference to the bootstrapper
* Added `composer:dump-autoload` console command
* Updated Composer dependencies

v0.3.6
<hr>
* Fixed bug with `NO_VALUE` console option
* Fixed formatting in About command
* Added `encryption:generatekey` command
* Added `RDev\Framework\Bootstrappers\Cryptography\Bootstrapper`

v0.3.5
<hr>
* Moved `Hasher` classes to `RDev\Cryptography\Hashing` namespace
* Added `RDev\Cryptography\Encryption\Encrypter`
* Added `RDev\Cryptography\Utilities\Strings`