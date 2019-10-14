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

use Aphiria\Console\Commands\Command;
use Aphiria\Console\Input\Argument;
use Aphiria\Console\Input\ArgumentTypes;

/**
 * Defines the command that makes files from templates
 */
abstract class MakeCommand extends Command
{
    protected function __construct(string $name, array $arguments, array $options, string $description, string $helpText = null)
    {
        // Prepend an arg that specifies the name of the class to create
        \array_unshift(
            $arguments,
            new Argument(
                'class',
                ArgumentTypes::REQUIRED,
                'The name of the class to create'
            )
        );

        parent::__construct($name, $arguments, $options, $description, $helpText);
    }
}
