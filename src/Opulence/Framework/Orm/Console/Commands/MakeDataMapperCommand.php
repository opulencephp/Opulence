<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Framework\Orm\Console\Commands;

use Opulence\Console\Prompts\Questions\MultipleChoice;
use Opulence\Console\Responses\IResponse;
use Opulence\Framework\Console\Commands\MakeCommand;

/**
 * Makes a data mapper class
 */
class MakeDataMapperCommand extends MakeCommand
{
    /** @var array The list of data mappers that can be made */
    private static $dataMapperTypes = [
        'Memcached-backed cached SQL data mapper' => 'MemcachedCachedSqlDataMapper',
        'PHPRedis data mapper' => 'PhpRedisDataMapper',
        'Predis data mapper' => 'PredisDataMapper',
        'Redis-backed cached SQL data mapper' => 'RedisCachedSqlDataMapper',
        'SQL data mapper' => 'SqlDataMapper'
    ];
    /** @var string The type of data mapper to generate */
    private $dataMapperType = '';

    /**
     * @inheritdoc
     */
    protected function define()
    {
        parent::define();

        $this->setName('make:datamapper')
            ->setDescription('Creates a data mapper class');
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
    {
        $this->dataMapperType = self::$dataMapperTypes[$this->prompt->ask(
            new MultipleChoice(
                'Which type of data mapper are you making?', array_keys(self::$dataMapperTypes)
            ), $response)];

        return parent::doExecute($response);
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultNamespace(string $rootNamespace) : string
    {
        return $rootNamespace . '\\Infrastructure\\Orm';
    }

    /**
     * @inheritdoc
     */
    protected function getFileTemplatePath() : string
    {
        return __DIR__ . '/templates/' . $this->dataMapperType . '.template';
    }
}
