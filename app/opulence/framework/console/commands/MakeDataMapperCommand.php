<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Makes a data mapper class
 */
namespace Opulence\Framework\Console\Commands;

use Opulence\Console\Prompts\Questions\MultipleChoice;
use Opulence\Console\Responses\IResponse;

class MakeDataMapperCommand extends MakeCommand
{
    /** @var array The list of data mappers that can be made */
    private static $dataMapperTypes = [
        "Memcached-backed cached SQL data mapper" => "MemcachedCachedSQLDataMapper",
        "PHPRedis data mapper" => "PHPRedisDataMapper",
        "Predis data mapper" => "PredisDataMapper",
        "Redis-backed cached SQL data mapper" => "RedisCachedSQLDataMapper",
        "SQL data mapper" => "SQLDataMapper"
    ];
    /** @var string The type of data mapper to generate */
    private $dataMapperType = "";

    /**
     * @inheritdoc
     */
    protected function define()
    {
        parent::define();

        $this->setName("make:datamapper")
            ->setDescription("Creates a data mapper class");
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
    {
        $this->dataMapperType = self::$dataMapperTypes[$this->prompt->ask(
            new MultipleChoice(
                "Which type of data mapper are you making?", array_keys(self::$dataMapperTypes)
            ), $response)];

        return parent::doExecute($response);
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . "\\ORM";
    }

    /**
     * @inheritdoc
     */
    protected function getFileTemplatePath()
    {
        return __DIR__ . "/templates/" . $this->dataMapperType . ".template";
    }
}