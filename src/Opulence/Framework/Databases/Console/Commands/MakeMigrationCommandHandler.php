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

use Aphiria\Console\Input\Input;
use Aphiria\Console\Output\IOutput;
use Closure;
use DateTime;
use Opulence\Framework\Console\ClassFileCompiler;
use Opulence\Framework\Console\Commands\MakeCommandHandler;

/**
 * Makes a migration class
 */
final class MakeMigrationCommandHandler extends MakeCommandHandler
{
    /**
     * @inheritdoc
     */
    public function __construct(ClassFileCompiler $classFileCompiler)
    {
        parent::__construct($classFileCompiler);
    }

    /**
     * @inheritdoc
     */
    protected function getCustomTagCompiler(Input $input, IOutput $output): ?Closure
    {
        $formattedCreationDate = (new DateTime)->format(DateTime::ATOM);

        return fn (string $compiledContents) => str_replace('{{creationDate}}', $formattedCreationDate, $compiledContents);
    }

    /**
     * @inheritdoc
     */
    protected function getTemplateFilePath(Input $input, IOutput $output): string
    {
        return __DIR__ . '/templates/Migration.template';
    }
}
