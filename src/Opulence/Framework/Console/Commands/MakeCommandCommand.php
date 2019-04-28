<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Console\Commands;

/**
 * Makes a command class
 */
class MakeCommandCommand extends MakeCommand
{
    /**
     * @inheritdoc
     */
    protected function define(): void
    {
        parent::define();

        $this->setName('make:command')
            ->setDescription('Creates a command class');
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $rootNamespace . '\\Application\\Console\\Commands';
    }

    /**
     * @inheritdoc
     */
    protected function getFileTemplatePath(): string
    {
        return __DIR__ . '/templates/ConsoleCommand.template';
    }
}
