<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Views\Console\Commands;

use Aphiria\Console\Commands\ICommandHandler;
use Aphiria\Console\Input\Input;
use Aphiria\Console\Output\IOutput;
use Opulence\Views\Caching\ICache;

/**
 * Defines the flush view cache command handler
 */
final class FlushViewCacheCommandHandler implements ICommandHandler
{
    /** @var ICache The view cache */
    private ICache $viewCache;

    /**
     * @param ICache $viewCache The view cache
     */
    public function __construct(ICache $viewCache)
    {
        $this->viewCache = $viewCache;
    }

    /**
     * @inheritdoc
     */
    public function handle(Input $input, IOutput $output)
    {
        $this->viewCache->flush();
        $output->writeln('<success>View cache flushed</success>');
    }
}
