<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Routing\Console\Commands;

use Opulence\Framework\Console\Commands\MakeCommand;

/**
 * Makes an HTTP middleware class
 */
class MakeHttpMiddlewareCommand extends MakeCommand
{
    /**
     * @inheritdoc
     */
    protected function define(): void
    {
        parent::define();

        $this->setName('make:httpmiddleware')
            ->setDescription('Creates an HTTP middleware class');
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $rootNamespace . '\\Application\\Http\\Middleware';
    }

    /**
     * @inheritdoc
     */
    protected function getFileTemplatePath(): string
    {
        return __DIR__ . '/templates/HttpMiddleware.template';
    }
}
