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

use Opulence\Framework\Console\Commands\MakeCommand;

/**
 * Makes an entity class
 */
class MakeEntityCommand extends MakeCommand
{
    /**
     * @inheritdoc
     */
    protected function define(): void
    {
        parent::define();

        $this->setName('make:entity')
            ->setDescription('Creates an entity class');
    }

    /**
     * @inheritdoc
     */
    protected function getFileTemplatePath(): string
    {
        return __DIR__ . '/templates/Entity.template';
    }
}
