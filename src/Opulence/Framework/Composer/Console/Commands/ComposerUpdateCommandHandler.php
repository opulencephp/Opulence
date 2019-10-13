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

use Aphiria\Console\Commands\ICommandBus;
use Aphiria\Console\Commands\ICommandHandler;
use Aphiria\Console\Input\Input;
use Aphiria\Console\Output\IOutput;
use Opulence\Framework\Composer\Executable;

/**
 * Defines the Composer update command handler
 */
final class ComposerUpdateCommandHandler implements ICommandHandler
{
    /** @var Executable The executable wrapper */
    private Executable $executable;
    /** @var ICommandBus The console application */
    private ICommandBus $app;

    /**
     * @param Executable $executable The Composer executable
     * @param ICommandBus $app The console application
     */
    public function __construct(Executable $executable, ICommandBus $app)
    {
        $this->executable = $executable;
        $this->app = $app;
    }

    /**
     * @inheritdoc
     */
    public function handle(Input $input, IOutput $output)
    {
        $output->write($this->executable->update());
        $this->app->handle('composer:dump-autoload', $output);
    }
}
