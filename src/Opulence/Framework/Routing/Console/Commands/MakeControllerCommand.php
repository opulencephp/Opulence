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
    /** @var array The list of controllers that can be made */
    private static $controllerTypes = [
        'Empty controller' => 'EmptyController',
        'REST controller' => 'RESTController'
    ];
    /** @var string The type of controller to generate */
    private $controllerType = '';
    
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
    protected function doExecute(IResponse $response)
    {
        $this->controllerType = self::$controllerTypes[$this->prompt->ask(
            new MultipleChoice(
                'Which type of controller are you making?', array_keys(self::$controllerTypes)
            ), $response)];

        return parent::doExecute($response);
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
        return __DIR__ . "/templates/{$this->controllerType}.template";
    }
}
