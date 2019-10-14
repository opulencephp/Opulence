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

use Aphiria\Console\Commands\ICommandHandler;
use Aphiria\Console\Input\Input;
use Aphiria\Console\Output\IOutput;
use Opulence\Views\Caching\ICache as ViewCache;

/**
 * Defines the command handler that flushes the framework's cache
 */
final class FlushFrameworkCacheCommandHandler implements ICommandHandler
{
    /** @var ViewCache The view cache */
    private ViewCache $viewCache;

    /**
     * @param ViewCache $viewCache The view cache
     */
    public function __construct(ViewCache $viewCache)
    {
        $this->viewCache = $viewCache;
    }

    /**
     * @inheritdoc
     */
    public function handle(Input $input, IOutput $output)
    {
        $this->flushBootstrapperCache($output);
        $this->flushViewCache($output);
        $output->writeln('<success>Framework cache flushed</success>');
    }

    /**
     * Flushes the bootstrapper cache
     *
     * @param IOutput $output The output to write to
     */
    private function flushBootstrapperCache(IOutput $output): void
    {
        // Todo: Need to make this work with new way of caching bootstrappers in 2.0

        $output->writeln('<info>Bootstrapper cache flushed</info>');
    }

    /**
     * Flushes the view cache
     *
     * @param IOutput $output The output to write to
     */
    private function flushViewCache(IOutput $output): void
    {
        $this->viewCache->flush();
        $output->writeln('<info>View cache flushed</info>');
    }
}
