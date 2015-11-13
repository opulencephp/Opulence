<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Console\Commands;

/**
 * Makes a controller class
 */
class MakeControllerCommand extends MakeCommand
{
    /**
     * @inheritdoc
     */
    protected function define()
    {
        parent::define();

        $this->setName("make:controller")
            ->setDescription("Creates a controller class");
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . "\\Http\\Controllers";
    }

    /**
     * @inheritdoc
     */
    protected function getFileTemplatePath()
    {
        return __DIR__ . "/templates/Controller.template";
    }
}