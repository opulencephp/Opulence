<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Makes a data mapper class
 */
namespace RDev\Framework\Console\Commands;
use RDev\Console\Prompts\Questions;
use RDev\Console\Responses;

class MakeDataMapper extends Make
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
     * {@inheritdoc}
     */
    protected function define()
    {
        parent::define();

        $this->setName("make:datamapper")
            ->setDescription("Creates a data mapper class");
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute(Responses\IResponse $response)
    {
        $this->dataMapperType = self::$dataMapperTypes[$this->prompt->ask(
            new Questions\MultipleChoice(
                "Which type of data mapper are you making?", array_keys(self::$dataMapperTypes)
            ), $response)];

        return parent::doExecute($response);
    }

    /**
     * {@inheritdoc}
     */
    protected function getFileTemplatePath()
    {
        return __DIR__ . "/templates/" . $this->dataMapperType . ".template";
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . "\\ORM";
    }
}