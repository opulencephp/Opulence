<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the application config
 */
namespace RDev\Models\Applications\Configs;
use Monolog;
use Monolog\Handler;
use RDev\Models\Configs;
use RDev\Models\IoC;

class ApplicationConfig extends Configs\Config
{
    private static $monologHandlerDefaultOptions = [
        "handler" => null,
        "handlers" => [],
        "level" => Monolog\Logger::DEBUG,
        "bubble" => true,
        "fromEmail" => "",
        "subject" => "",
        "toEmail" => "",
        "mailer" => null,
        "formatter" => null,
        "priority" => 0,
        "id" => null,
        "stopBuffering" => true,
        "bufferSize" => 0,
        "activationStrategy" => null,
        "passThroughLevel" => null,
        "facility" => LOG_USER,
        "cubeHandlerURL" => "",
        "amqpExchange" => null,
        "amqpExchangeName" => "log",
        "couchDBClient" => null,
        "dynamoDBClient" => null,
        "dynamoTable" => "",
        "elasticClient" => null,
        "minLevel" => Monolog\Logger::DEBUG,
        "maxLevel" => Monolog\Logger::EMERGENCY,
        "token" => "",
        "gelfPublisher" => null,
        "hipChatRoom" => "",
        "swiftMailer" => null,
        "swiftMessage" => null,
        "mongo" => null,
        "mongoDB" => "",
        "mongoCollection" => "",
        "pushoverUsers" => [],
        "ravenClient" => null,
        "redisClient" => null,
        "redisKey" => "",
        "rollbarNotifier" => null,
        "filename" => "",
        "maxFiles" => 0,
        "slackChannel" => "",
        "slackUsername" => "",
        "socketConnectionString" => "",
        "stream" => "",
        "sysLogHost" => "",
        "sysLogPort" => 514
    ];

    /**
     * {@inheritdoc}
     */
    public function fromArray(array $configArray)
    {
        if(!$this->isValid($configArray))
        {
            throw new \RuntimeException("Invalid config");
        }

        $this->setupMonologFromArray($configArray);
        $this->setupEnvironmentFromArray($configArray);
        $this->setupRouterFromArray($configArray);
        $this->setupBindingsFromArray($configArray);

        $this->configArray = $configArray;
    }

    /**
     * {@inheritdoc}
     */
    protected function isValid(array $configArray)
    {
        if(!$this->monologConfigIsValid($configArray) || !$this->bindingsConfigIsValid($configArray))
        {
            return false;
        }

        return true;
    }

    /**
     * Checks if the bindings config array is valid
     *
     * @param array $configArray The config array
     * @return bool Whether or not the bindings config array is valid
     */
    private function bindingsConfigIsValid(array $configArray)
    {
        if(isset($configArray["bindings"]))
        {
            if(isset($configArray["bindings"]["container"])
                && !is_string($configArray["bindings"]["container"])
                && !$configArray["bindings"]["container"] instanceof IoC\IContainer
            )
            {
                return false;
            }

            if(isset($configArray["bindings"]["universal"]))
            {
                if(!is_array($configArray["bindings"]["universal"]))
                {
                    return false;
                }

                foreach($configArray["bindings"]["universal"] as $interfaceName => $concreteClassName)
                {
                    if(!is_string($interfaceName) || !is_string($concreteClassName))
                    {
                        return false;
                    }
                }
            }

            if(isset($configArray["bindings"]["targeted"]))
            {
                if(!is_array($configArray["bindings"]["targeted"]))
                {
                    return false;
                }

                foreach($configArray["bindings"]["targeted"] as $targetClassName => $bindings)
                {
                    if(!is_array($bindings))
                    {
                        return false;
                    }

                    foreach($bindings as $interfaceName => $concreteClassName)
                    {
                        if(!is_string($interfaceName) || !is_string($concreteClassName))
                        {
                            return false;
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * Creates all the default values for Monolog handlers
     * Various handlers require different items from the config, and this sets all of them to their default values if
     * they're not already set
     *
     * @param array $configArray
     */
    private function createDefaultMonologHandlerOptions(array &$configArray)
    {
        foreach(self::$monologHandlerDefaultOptions as $name => $value)
        {
            if(!isset($configArray[$name]))
            {
                $configArray[$name] = $value;
            }
        }
    }

    /**
     * Creates an instances of a Monolog handler from the input config
     *
     * @param string $handlerClassName The name of the handler we're instantiating
     * @param array $configArray The handler config array
     * @return Handler\HandlerInterface The instance of the Monolog handler
     * @throws \RuntimeException Thrown if the Monolog handler is not supported
     */
    private function createMonologHandlerInstance($handlerClassName, array $configArray)
    {
        $handlerClassName = trim($handlerClassName, "\\");
        $handlerClassName = strtolower(str_replace("Monolog\\Handler\\", "", $handlerClassName));

        switch($handlerClassName)
        {
            case "amqphandler":
                return new Handler\AmqpHandler($configArray["amqpExchange"], $configArray["amqpExchangeName"],
                    $configArray["level"], $configArray["bubble"]);
            case "browserconsolehandler":
                return new Handler\BrowserConsoleHandler();
            case "bufferhandler":
                return new Handler\BufferHandler($configArray["handler"], $configArray["bufferSize"],
                    $configArray["level"], $configArray["bubble"]);
            case "chromephphandler":
                return new Handler\ChromePHPHandler($configArray["level"], $configArray["bubble"]);
            case "couchdbhandler":
                return new Handler\CouchDBHandler([], $configArray["level"], $configArray["bubble"]);
            case "cubehandler":
                return new Handler\CubeHandler($configArray["cubeHandlerURL"], $configArray["level"], $configArray["bubble"]);
            case "doctrinecouchdbhandler":
                return new Handler\DoctrineCouchDBHandler($configArray["couchDBClient"], $configArray["level"],
                    $configArray["bubble"]);
            case "dynamodbhandler":
                return new Handler\DynamoDbHandler($configArray["dynamoDBClient"], $configArray["dynamoTable"],
                    $configArray["level"], $configArray["bubble"]);
            case "elasticsearchhandler":
                return new Handler\ElasticSearchHandler($configArray["elasticClient"], [], $configArray["level"],
                    $configArray["bubble"]);
            case "errorloghandler":
                return new Handler\ErrorLogHandler(Handler\ErrorLogHandler::OPERATING_SYSTEM, $configArray["level"],
                    $configArray["bubble"]);
            case "filterhandler":
                return new Handler\FilterHandler($configArray["handler"], $configArray["minLevel"],
                    $configArray["maxLevel"], $configArray["bubble"]);
            case "fingerscrossedhandler":
                return new Handler\FingersCrossedHandler($configArray["handler"], $configArray["activationStrategy"],
                    $configArray["bufferSize"], $configArray["bubble"]);
            case "firephphandler":
                return new Handler\FirePHPHandler($configArray["level"], $configArray["bubble"]);
            case "fleephookhandler":
                return new Handler\FleepHookHandler($configArray["token"], $configArray["level"], $configArray["bubble"]);
            case "flowdockhandler":
                return new Handler\FlowdockHandler($configArray["token"], $configArray["level"], $configArray["bubble"]);
            case "gelfhandler":
                return new Handler\GelfHandler($configArray["gelfPublisher"], $configArray["level"], $configArray["bubble"]);
            case "grouphandler":
                $handlers = [];

                foreach($configArray["handlers"] as $handler)
                {
                    $handlers[] = $this->createMonologHandlerInstance($handler, $configArray);
                }

                return new Handler\GroupHandler($handlers, $configArray["bubble"]);
            case "hipchathandler":
                return new Handler\HipChatHandler($configArray["token"], $configArray["hipChatRoom"], "Monolog", false,
                    $configArray["level"], $configArray["bubble"]);
            case "logentrieshandler":
                return new Handler\LogEntriesHandler($configArray["token"], true, $configArray["level"],
                    $configArray["bubble"]);
            case "logglyhandler":
                return new Handler\LogglyHandler($configArray["token"], $configArray["level"], $configArray["bubble"]);
            case "mandrillhandler":
                return new Handler\MandrillHandler($configArray["token"], $configArray["swiftMessage"],
                    $configArray["level"], $configArray["bubble"]);
            case "mongodbhandler":
                return new Handler\MongoDBHandler($configArray["mongo"], $configArray["mongoDB"],
                    $configArray["mongoCollection"], $configArray["level"], $configArray["bubble"]);
            case "nativemailerhandler":
                return new Handler\NativeMailerHandler($configArray["toEmail"], $configArray["subject"],
                    $configArray["fromEmail"], $configArray["level"], $configArray["bubble"]);
            case "newrelichandler":
                return new Handler\NewRelicHandler($configArray["level"], $configArray["bubble"]);
            case "nullhandler":
                return new Handler\NullHandler($configArray["level"]);
            case "pushoverhandler":
                return new Handler\PushoverHandler($configArray["token"], $configArray["pushoverUsers"], null,
                    $configArray["level"], $configArray["bubble"]);
            case "ravenhandler":
                return new Handler\RavenHandler($configArray["ravenClient"], $configArray["level"], $configArray["bubble"]);
            case "redishandler":
                return new Handler\RedisHandler($configArray["redisClient"], $configArray["redisKey"],
                    $configArray["level"], $configArray["bubble"]);
            case "rollbarhandler":
                return new Handler\RollbarHandler($configArray["rollbarHandler"], $configArray["level"],
                    $configArray["bubble"]);
            case "rotatingfilehandler":
                return new Handler\RotatingFileHandler($configArray["filename"], $configArray["maxFiles"],
                    $configArray["level"], $configArray["bubble"]);
            case "slackhandler":
                return new Handler\SlackHandler($configArray["token"], $configArray["slackChannel"],
                    $configArray["slackUsername"], true, null, $configArray["level"], $configArray["bubble"]);
            case "sockethandler":
                return new Handler\SocketHandler($configArray["socketConnectionString"], $configArray["level"],
                    $configArray["bubble"]);
            case "streamhandler":
                return new Handler\StreamHandler($configArray["stream"], $configArray["level"],
                    $configArray["bubble"]);
            case "swiftmailerhandler":
                return new Handler\SwiftMailerHandler($configArray["swiftMailer"], $configArray["swiftMessage"],
                    $configArray["level"], $configArray["bubble"]);
            case "sysloghandler":
                return new Handler\SyslogHandler($configArray["id"], $configArray["facility"],
                    $configArray["level"], $configArray["bubble"]);
            case "syslogudphandler":
                return new Handler\SyslogUdpHandler($configArray["sysLogHost"], $configArray["sysLogPort"],
                    $configArray["facility"], $configArray["level"], $configArray["bubble"]);
            case "whatfailuregrouphandler":
                $handlers = [];

                foreach($configArray["handlers"] as $handler)
                {
                    $handlers[] = $this->createMonologHandlerInstance($handler, $configArray);
                }

                return new Handler\WhatFailureGroupHandler($handlers, $configArray["bubble"]);
            case "zendmonitorhandler":
                return new Handler\ZendMonitorHandler($configArray["level"], $configArray["bubble"]);
            default:
                throw new \RuntimeException("Monolog handler type {$handlerClassName} not supported");
        }
    }

    /**
     * Checks if the Monolog config array is valid
     *
     * @param array $configArray The config array
     * @return bool Whether or not the Monolog config array is valid
     */
    private function monologConfigIsValid(array $configArray)
    {
        if(isset($configArray["monolog"]))
        {
            if(!isset($configArray["monolog"]["handlers"]) || !is_array($configArray["monolog"]["handlers"]))
            {
                return false;
            }

            foreach($configArray["monolog"]["handlers"] as $name => $handlerConfig)
            {
                if(!isset($handlerConfig["type"]))
                {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Sets up the bindings from a config array
     *
     * @param array $configArray The config array
     */
    private function setupBindingsFromArray(array &$configArray)
    {
        if(!isset($configArray["bindings"]))
        {
            // Setup a default bindings
            $configArray["bindings"] = [
                "container" => "RDev\\Models\\IoC\\Container",
                "universal" => [],
                "targeted" => []
            ];
        }

        if(!isset($configArray["bindings"]["container"]))
        {
            // Default to our container
            $configArray["bindings"]["container"] = new IoC\Container();
        }

        if(is_string($configArray["bindings"]["container"]))
        {
            if(!class_exists($configArray["bindings"]["container"]))
            {
                throw new \RuntimeException("Class {$configArray['bindings']['container']} does not exist");
            }

            $configArray["bindings"]["container"] = new $configArray["bindings"]["container"];
        }

        if(!$configArray["bindings"]["container"] instanceof IoC\IContainer)
        {
            throw new \RuntimeException("Container does not implement IContainer");
        }
    }

    /**
     * Sets up the environment from a config array
     *
     * @param array $configArray The config array
     */
    private function setupEnvironmentFromArray(array &$configArray)
    {
        if(!isset($configArray["environment"]))
        {
            $configArray["environment"] = [];
        }
    }

    /**
     * Sets up Monolog from a config array
     *
     * @param array $configArray The config array
     */
    private function setupMonologFromArray(array &$configArray)
    {
        if(!isset($configArray["monolog"]))
        {
            // Setup a default Monolog handler
            $configArray["monolog"] = [
                "handlers" => [
                    "main" => [
                        "type" => "Monolog\\Handler\\ErrorLogHandler",
                        "level" => Monolog\Logger::DEBUG
                    ]
                ]
            ];
        }

        foreach($configArray["monolog"]["handlers"] as $handlerName => &$handlerConfig)
        {
            $this->createDefaultMonologHandlerOptions($handlerConfig);

            // If we have to create a handler from a config
            if(isset($handlerConfig["handler"]) && !$handlerConfig["handler"] instanceof Handler\HandlerInterface)
            {
                $handlerConfig["handler"] = $this->createMonologHandlerInstance($handlerConfig["handler"], $handlerConfig);
            }

            // If we have to create a type from a config
            if(!$handlerConfig["type"] instanceof Handler\HandlerInterface)
            {
                $handlerConfig["type"] = $this->createMonologHandlerInstance($handlerConfig["type"], $handlerConfig);
            }

            /** @var Handler\AbstractHandler[] $handlerConfig */
            $configArray["monolog"]["handlers"][$handlerName] = $handlerConfig["type"];
        }
    }

    /**
     * Sets up the router from a config array
     *
     * @param array $configArray The config array
     */
    private function setupRouterFromArray(array &$configArray)
    {
        if(!isset($configArray["router"]))
        {
            $configArray["router"] = [];
        }
    }
} 