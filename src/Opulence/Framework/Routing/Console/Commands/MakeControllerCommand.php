<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Framework\Routing\Console\Commands;

use Opulence\Framework\Console\Commands\MakeCommand;

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

        $this->setName('make:controller')
            ->setDescription('Creates a controller class');
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultNamespace(string $rootNamespace) : string
    {
        return $rootNamespace . '\\Application\\Http\\Controllers';
    }

    /**
     * @inheritdoc
     */
    protected function getFileTemplatePath() : string
    {
        return __DIR__ . '/templates/Controller.template';
    }
}
