<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Framework\Views\Console\Commands;

use Opulence\Console\Commands\Command;
use Opulence\Console\Responses\IResponse;
use Opulence\Views\Caching\ICache;

/**
 * Defines the flush view cache command
 */
class FlushViewCacheCommand extends Command
{
    /** @var ICache The view cache */
    private $viewCache = null;

    /**
     * @param ICache $viewCache The view cache
     */
    public function __construct(ICache $viewCache)
    {
        parent::__construct();

        $this->viewCache = $viewCache;
    }

    /**
     * @inheritdoc
     */
    protected function define()
    {
        $this->setName('views:flush')
            ->setDescription('Flushes all of the compiled views from cache');
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
    {
        $this->viewCache->flush();
        $response->writeln('<success>View cache flushed</success>');
    }
}
