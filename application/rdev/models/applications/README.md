# Application

## Table of Contents
1. [Introduction](#introduction)
2. [Basic Usage](#basic-usage)
3. [Config](#config)
4. [Bindings](#bindings)
5. [Monolog](#monolog)
  1. [Monolog Handler Options](#monolog-handler-options)
6. [Starting and Shutting Down An Application](#starting-and-shutting-down-an-application)

## Introduction
An **RDev** application is started up through the `Application` class.  In it, you can configure things like the environment you're on (eg "development" or "production"), pre-/post-start and -shutdown tasks to run, and URL routing.

## Basic Usage
Applications are started and shutdown in a bootstrap file.  Applications use an `ApplicationConfig` ([learn more about application configs](#config)) to specify settings for an application.  The following is an example of a bootstrap file:

```php
use RDev\Models\Applications;
use RDev\Models\Applications\Configs;
use RDev\Models\Configs\Readers;

// Get the autoloader from Composer
require_once(__DIR__ . "/../vendor/autoload.php");

// Let's pretend that the config is in a JSON file
$jsonReader = new Readers\JSONReader();
$applicationConfig = $jsonReader->readFromFile(PATH_TO_CONFIG, "RDev\\Models\\Applications\\Configs\\ApplicationConfig");
$application = new Applications\Application($applicationConfig);
$application->start();
$application->shutdown();
```

## Config
Applications are initialized with an `ApplicationConfig` object or array ([learn more about application configs](https://github.com/ramblingsofadev/RDev/tree/master/application/rdev/models/configs)).  You can setup rules for automatically detecting which environment the current server belongs on, eg "production", "staging", "testing", or "development".  For example, you could specify a list of server IP addresses for each of the environments.  You could also use regular expressions to match against the servers' hosts, or you could use a callback to completely customize the logic for determining the environment.

Let's break down the structure of the config.  The following keys are optional:
* "environment"
  * There are two values you can pass in: a keyed array or a callback
    * In the case of an array, it must have at least one of the following keys:
      * "production"
      * "staging"
      * "testing"
      * "development"
    * Each of these keys may map to one of the following values:
      * A server host or an array of server hosts that belong under that environment
      * An array containing the following keys:
        * "type" => One of the following values:
          * "regex" to denote that the rule uses a regular expression
        * "value" => The value of the rule, eg the regular expression to use
    * If you use a callback, it must simply return the name of the environment the current server resides in
* "bindings"
  * The following keys are optional:
    * "container" => The dependency injection container to use throughout the application
      * Must either be an instance or name of a class that implements `RDev\Models\IoC\IContainer`
    * "universal" => Mapping of universal bindings of interface/abstract class names to concrete class names
    * "targeted" => Mapping of targeted classes to their list of bindings of interface/abstract class names to concrete class names
      * Targeted bindings take precedence over universal bindings
  * For more about dependency injection, [read this](https://github.com/ramblingsofadev/RDev/tree/master/application/rdev/models/ioc)
* "router"
  * See the [Routing page](https://github.com/ramblingsofadev/RDev/tree/master/application/rdev/models/web/routing)
* "monolog"
  The following are required:
  * "handlers" => Maps to names of handlers, which map to options for that handler
    * "type" => The class name or instance of the Monolog handler
      * This is the only option that is required for all handlers
    * [Read the full list of Monolog handler options](#monolog-handler-options)

Let's take a look at an example that uses an array:
```php
use RDev\Models\Application\Configs;

$configArray = [
    "environment" => [
        // Let's say that there's only one production server
        "production" => "123.456.7.8",
        // Let's say there's a list of staging servers
        "staging" => ["123.456.7.9", "123.456.7.10"],
        // Let's use a regular expression to detect a development environment
        "development" => [
            ["type" => "regex", "value" => "/^192\.168\..*$/"]
        ]
    ],
    "bindings" => [
        "universal" => [
            "IFoo" => "ConcreteFoo"
        ]
    ]
];
$config = new Configs\ApplicationConfig($configArray);
```
The following is an example with a custom callback:
```php
use RDev\Models\Application\Configs;

$configArray = [
    "environment" => function()
    {
        // Look to see if a PHP environment variable was set
        if(isset($_ENV["environment"]))
        {
            return $_ENV["environment"];
        }

        // By default, return production
        return "production";
    },
    "bindings" => [
        "universal" => [
            "IFoo" => "ConcreteFoo"
        ]
    ]
];
$config = new Configs\ApplicationConfig($configArray);
```
The following specifies a custom container class:
```php
use RDev\Models\Application\Configs;

$configArray = [
    "bindings" => [
        "container" => "Class\\That\\Implements\\IContainer"
    ]
];
$config = new Configs\ApplicationConfig($configArray);
```

## Bindings
Bindings in the config are set when the application is instantiated.  If you'd like to register more bindings after the application has been instantiated, you may do so by registering a pre-start task:

```php
use RDev\Models\Applications;

$application = new Applications\Application();
$application->registerPreStartTask(function() use ($application)
{
    $container = $application->getIoCContainer();
    $container->bind("IFoo", "ConcreteFoo");
    $container->bind("IBar", "ConcreteBar");
});
```

> **Note:** Register all of your bindings before the application is started.  Otherwise, they may not be available.

## Monolog
RDev takes advantage of Monolog, a popular error logger.  By default, RDev will simply write to the built-in PHP error logs, but you can customize how Monolog works.  The following is an example of a logger that writes warnings to the PHP error log with a `FingersCrossedHandler`:
```php
$configArray = [
    "monolog" => [
        "handlers" => [
            "main" => [
                "type" => "Monolog\\Handler\\FingersCrossedHandler",
                "handler" => "Monolog\\Handler\\ErrorLogHandler",
                "level" => Monolog\Logger::WARNING
            ]
        ]
    ]
];
$config = new Configs\ApplicationConfig($configArray);
```

#### Monolog Handler Options
Certain Monolog handlers require a lot more information to get started.  To handle those cases, the following is a list of all the possible keys you may set in the config:
```php
$defaults = [
    "activationStrategy" => null,
    "amqpExchange" => null,
    "amqpExchangeName" => "log",
    "bubble" => true,
    "bufferSize" => 0,
    "couchDBClient" => null,
    "cubeHandlerURL" => "",
    "dynamoDBClient" => null,
    "dynamoTable" => "",
    "elasticClient" => null,
    "facility" => LOG_USER,
    "filename" => "",
    "formatter" => null,
    "fromEmail" => "",
    "gelfPublisher" => null,
    "handler" => null,
    "handlers" => [],
    "hipChatRoom" => "",
    "id" => null,
    "level" => Monolog\Logger::DEBUG,
    "mailer" => null,
    "maxLevel" => Monolog\Logger::EMERGENCY,
    "maxFiles" => 0,
    "minLevel" => Monolog\Logger::DEBUG,
    "mongo" => null,
    "mongoCollection" => "",
    "mongoDB" => "",
    "passThroughLevel" => null,
    "priority" => 0,
    "pushoverUsers" => [],
    "ravenClient" => null,
    "redisClient" => null,
    "redisKey" => "",
    "rollbarNotifier" => null,
    "slackChannel" => "",
    "slackUsername" => "",
    "socketConnectionString" => "",
    "stopBuffering" => true,
    "stream" => "",
    "subject" => "",
    "swiftMailer" => null,
    "swiftMessage" => null,
    "sysLogHost" => "",
    "sysLogPort" => 514,
    "toEmail" => "",
    "token" => ""
];
```

Obviously, you do not have to set every one.  Just set the ones that are applicable to the handler you're creating.

## Starting And Shutting Down An Application
To start and shutdown an application, simply call the `start()` and `shutdown()` methods, respectively, on the application object.  If you'd like to do some tasks before or after startup, you may do them using `registerPreStartTask()` and `registerPostStartTask()`, respectively.  Similarly, you can add tasks before and after shutdown using `registerPreShutdownTask()` and `registerPostShutdownTask()`, respectively.  These tasks are handy places to do any setting up that your application requires or any housekeeping after start/shutdown.

Let's look at an example of using these tasks:
```php
use RDev\Models\Applications;
use RDev\Models\Applications\Configs;
use RDev\Models\Configs\Readers;

// Get the autoloader from Composer
require_once(__DIR__ . "/../vendor/autoload.php");

$application = new Applications\Application([]);
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