<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Composer\Console\Commands;

use Aphiria\Console\Commands\ICommandHandler;
use Aphiria\Console\Input\Input;
use Aphiria\Console\Output\IOutput;
use Opulence\Framework\Composer\Executable;

/**
 * Defines the Composer dump autoload command handler
 */
final class ComposerDumpAutoloadCommandHandler implements ICommandHandler
{
    /** @var Executable The executable wrapper */
    private Executable $executable;

    /**
     * @param Executable $executable The Composer executable
     */
    public function __construct(Executable $executable)
    {
        $this->executable = $executable;
    }

    /**
     * @inheritdoc
     */
    public function handle(Input $input, IOutput $output)
    {
        $output->write($this->executable->dumpAutoload('-o'));
    }
}
