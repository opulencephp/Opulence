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
use Opulence\Framework\Console\ClassFileCompiler;
use Opulence\Framework\Console\Commands\MakeCommandHandler;

/**
 * Makes an entity class
 */
final class MakeEntityCommandHandler extends MakeCommandHandler
{
    /**
     * @inheritdoc
     */
    public function __construct(ClassFileCompiler $classFileCompiler)
    {
        parent::__construct($classFileCompiler);
    }

    /**
     * @inheritDoc
     */
    protected function getTemplateFilePath(Input $input, IOutput $output): string
    {
        return __DIR__ . '/templates/Entity.template';
    }
}
