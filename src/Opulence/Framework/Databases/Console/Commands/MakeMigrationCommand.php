<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Framework\Databases\Console\Commands;

use DateTime;
use Opulence\Framework\Console\Commands\MakeCommand;

/**
 * Makes a migration class
 */
class MakeMigrationCommand extends MakeCommand
{
    /**
     * @inheritdoc
     */
    protected function compile(string $templateContents, string $fullyQualifiedClassName) : string
    {
        $compiledContents = parent::compile($templateContents, $fullyQualifiedClassName);
        $formattedCreationDate = (new DateTime)->format(DateTime::ATOM);

        return str_replace('{{creationDate}}', $formattedCreationDate, $compiledContents);
    }

    /**
     * @inheritdoc
     */
    protected function define()
    {
        parent::define();

        $this->setName('make:migration')
            ->setDescription('Creates a database migration class');
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultNamespace(string $rootNamespace) : string
    {
        return $rootNamespace . '\\Infrastructure\\Databases\\Migrations';
    }

    /**
     * @inheritdoc
     */
    protected function getFileTemplatePath() : string
    {
        return __DIR__ . '/templates/Migration.template';
    }
}
