<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Databases\Console\Commands;

use DateTime;
use Opulence\Framework\Console\Commands\MakeCommandHandler;

/**
 * Makes a migration class
 */
final class MakeMigrationCommandHandler extends MakeCommandHandler
{
    /**
     * @inheritdoc
     */
    protected function compile(string $templateContents, string $fullyQualifiedClassName): string
    {
        $compiledContents = parent::compile($templateContents, $fullyQualifiedClassName);
        $formattedCreationDate = (new DateTime)->format(DateTime::ATOM);

        return str_replace('{{creationDate}}', $formattedCreationDate, $compiledContents);
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $rootNamespace . '\\Databases\\Migrations';
    }

    /**
     * @inheritdoc
     */
    protected function getFileTemplatePath(): string
    {
        return __DIR__ . '/templates/Migration.template';
    }
}
