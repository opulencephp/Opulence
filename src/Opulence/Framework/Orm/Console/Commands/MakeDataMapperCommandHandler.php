<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Orm\Console\Commands;

use Aphiria\Console\Input\Input;
use Aphiria\Console\Output\IOutput;
use Aphiria\Console\Output\Prompts\MultipleChoice;
use Aphiria\Console\Output\Prompts\Question;
use Opulence\Framework\Console\Commands\MakeCommandHandler;

/**
 * Makes a data mapper class
 */
final class MakeDataMapperCommandHandler extends MakeCommandHandler
{
    /** @var array The list of data mappers that can be made */
    private static array $dataMapperTypes = [
        'Memcached-backed cached SQL data mapper' => 'MemcachedCachedSqlDataMapper',
        'Redis data mapper' => 'RedisDataMapper',
        'Redis-backed cached SQL data mapper' => 'RedisCachedSqlDataMapper',
        'SQL data mapper' => 'SqlDataMapper'
    ];
    /** @var string The type of data mapper to generate */
    private string $dataMapperType = '';
    /** @var string The name of the entity class */
    private string $entityClassName = '';
    /** @var string The name of the entity variable */
    private string $entityVariableName = '';

    /**
     * @inheritdoc
     */
    public function handle(Input $input, IOutput $output)
    {
        $this->dataMapperType = self::$dataMapperTypes[$this->prompt->ask(
            new MultipleChoice(
                'Which type of data mapper are you making?',
                array_keys(self::$dataMapperTypes)
            ),
            $output
        )];
        $this->entityClassName = $this->prompt->ask(
            new Question(
                'What is the fully-qualified class name for the types of entities this data mapper is handling?'
            ),
            $output
        );
        $this->entityClassName = '\\' . ltrim($this->entityClassName, '\\');
        $explodedEntityClassName = explode('\\', $this->entityClassName);
        $this->entityVariableName = lcfirst(end($explodedEntityClassName));

        return parent::handle($input, $output);
    }

    /**
     * @inheritdoc
     */
    protected function compile(string $templateContents, string $fullyQualifiedClassName): string
    {
        $compiledContents = parent::compile($templateContents, $fullyQualifiedClassName);

        return str_replace(
            ['{{entityType}}', '{{entityVarName}}'],
            [$this->entityClassName, $this->entityVariableName],
            $compiledContents
        );
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultNamespace(string $rootNamespace): string
    {
        // Todo: Needs to be updated to use latest directory structure
        return $rootNamespace . '\\Infrastructure\\Orm';
    }

    /**
     * @inheritdoc
     */
    protected function getFileTemplatePath(): string
    {
        return __DIR__ . "/templates/{$this->dataMapperType}.template";
    }
}
