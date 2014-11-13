# Application

## Table of Contents
1. [Introduction](#introduction)
2. [Workflow](#workflow)
3. [Kernels](#kernels)
4. [Environment](#environment)
  1. [Config Structure](#config-structure)
  2. [Environment Variables](#environment-variables)
5. [Bootstrappers](#bootstrappers)
6. [Starting and Shutting Down An Application](#starting-and-shutting-down-an-application)

## Introduction
An **RDev** application is started up through the `Application` class.  In it, you can configure things like the environment you're on (eg "development" or "production") as well as pre-/post-start and -shutdown tasks to run.

## Workflow
RDev uses a single point of entry for all pages.  In other words, all HTTP requests get redirected through `index.php`, which instantiates the application and handles the request.  Here's a breakdown of the workflow of a typical RDev application:

1. User requests http://www.example.com/users/23/profile
2. `.htaccess` file redirects the request through http://www.example.com/index.php
3. `bootstrap/start.php` is loaded, which instantiates an `Application` object
4. An HTTP `Kernel` is instantiated, which converts the HTTP request into a response
  * The path "/users/23/profile" is detected by the request
5. The `Router` finds a route that matches the request
  * The user Id 23 is extracted from the URL here
6. The `Dispatcher` dispatches the request to the `Controller`
7. The `Controller` processes data from the request, updates/retrieves any appropriate models, and creates a `Response`
8. The `Response` is sent back to the user and the application is shut down

## Kernels
A kernel is something that takes input, performs processing on it, and returns output.  In RDev, there are 2 kernels:

1. `RDev\Applications\Kernels\HTTP\Kernel`
2. `RDev\Applications\Kernels\Console\Kernel`

Having these two kernels allows RDev to function as both a console application and a traditional HTTP web application.

## Environment
Sometimes, you might want to change the way your application behaves depending on whether or not it's running on a production, staging, testing, or development machine.  A common example is a database connection - each environment might have different server credentials.  By detecting the environment, you can load the appropriate credentials.  To actually detect the environment, use an `EnvironmentDetector`.  In it, you can specify rules for various environment names.  You can also detect if you're running in a console vs an HTTP connection.

#### Config Structure
The configuration that's passed into `EnvironmentDetector::detect()` should be either:

* A callback function that returns the name of the environment the current server resides in OR
* An array that maps environment names to rules
  * Each rule must be one of the following:
    1. A server host IP or array of host IPs that belong to that environment
    2. An array containing the following keys:
      * "type" => One of the following values:
        * "regex" => Denotes that the rule uses a regular expression
      * "value" => The value of the rule, eg the regular expression to use

Let's take a look at an example:
```php
use RDev\Applications\Environments;

$configArray = [
   // Let's say that there's only one production server
   "production" => "123.456.7.8",
   // Let's say there's a list of staging servers
   "staging" => ["123.456.7.9", "123.456.7.10"],
   // Let's use a regular expression to detect a development environment
   "development" => [
       ["type" => "regex", "value" => "/^192\.168\..*$/"]
   ]
];
$detector = new Environments\EnvironmentDetector($configArray);
$environmentName = $detector->getName();
$environment = new Environments\Environment($environmentName);
```
The following is an example with a custom callback:
```php
use RDev\Applications\Environments;

$callback = function()
{
    // Look to see if a PHP environment variable was set
    if(isset($_ENV["environment"]))
    {
        return $_ENV["environment"];
    }

    // By default, return production
    return "production";
};
$detector = new Environments\EnvironmentDetector($callback);
$environmentName = $detector->getName();
$environment = new Environments\Environment($environmentName);
```

#### Environment Variables
Variables that are specifically tied to the environment the application is running on are called *environment variables*.  Let's say the password for your database connection is different on your development server vs your production server.  Set an environment variable to hold this data:

```php
use RDev\Applications\Environments;
use RDev\Databases\SQL;

$environment = new Environments\Environment("production");
$environment->setVariable("DB_PASSWORD", "Pr0ducti0nP4$$w0rD");
// Do some stuff...
$server = new SQL\Server("localhost", "dbuser", $environment->getVariable("DB_PASSWORD"), "dbname");
```

## Bootstrappers
Most applications need to do some configuration before starting.  A common task is registering bindings, and yet another is setting up database connections.  You can do this bootstrapping by implementing `RDev\Applications\Bootstrappers\IBootstrapper`.  If your bootstrapper has any dependencies such as an IoC `Container`, inject it through the constructor:

```php
namespace MyApp\Bootstrappers;
use RDev\Applications\Bootstrappers;
use RDev\IoC;
use RDev\Databases\SQL;
use RDev\Databases\SQL\PDO\PostgreSQL;

class MyBootstrapper implements Bootstrappers\IBootstrapper
{
    private $container = null;
    
    public function __construct(IoC\IContainer $container)
    {
        $this->container = $container;
    }

    public function run()
    {
        $driver = new PostgreSQL\Driver();
        $server = new SQL\Server("127.0.0.1", "dbuser", "password", "mydb");
        $connectionPool = new SQL\SingleServerConnectionPool($driver, $server);
        $this->container->bind("RDev\\Databases\\SQL\\ConnectionPool", $connectionPool);
    }
}
```
Then, in your start file, register the bootstrapper:

```php
$application->registerBootstrappers(["MyApp\\Bootstrappers\\MyBootstrapper"]);
```

When the application boots, `MyBootstrapper` will be `run()`.

## Starting And Shutting Down An Application
To start and shutdown an application, simply call the `start()` and `shutdown()` methods, respectively, on the application object.  If you'd like to do some tasks before or after startup, you may do them using `registerPreStartTask()` and `registerPostStartTask()`, respectively.  Similarly, you can add tasks before and after shutdown using `registerPreShutdownTask()` and `registerPostShutdownTask()`, respectively.  These tasks are handy places to do any setting up that your application requires or any housekeeping after start/shutdown.

Let's look at an example of using these tasks:
```php
// Let's register a pre-start task
$application->registerPreStartTask(function()
{
    error_log("Application issued start command at " . date("Y-m-d H:i:s"));
});
// Let's register a post-start task
$application->registerPostStartTask(function()
{
    error_log("Application started at " . date("Y-m-d H:i:s"));
});
// Let's register a pre-shutdown task
$application->registerPreShutdownTask(function()
{
    error_log("Application issued shutdown command at " . date("Y-m-d H:i:s"));
});
// Let's register a post-shutdown task
$application->registerPostShutdownTask(function()
{
    error_log("Application shutdown at " . date("Y-m-d H:i:s"));
});
$application->start();
$application->shutdown();
```