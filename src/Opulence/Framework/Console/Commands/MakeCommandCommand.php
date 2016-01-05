<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Console\Commands;

/**
 * Makes a command class
 */
class MakeCommandCommand extends MakeCommand
{
    /**
     * @inheritdoc
     */
    protected function define()
    {
        parent::define();

        $this->setName("make:command")
            ->setDescription("Creates a command class");
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . "\\Console\\Commands";
    }

    /**
     * @inheritdoc
     */
    protected function getFileTemplatePath()
    {
        return __DIR__ . "/templates/ConsoleCommand.template";
    }
}