<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the flush view cache command
 */
namespace RDev\Framework\Console\Commands;
use RDev\Console\Commands\Command;
use RDev\Console\Responses\IResponse;
use RDev\Views\Caching\ICache;

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
     * {@inheritdoc}
     */
    protected function define()
    {
        $this->setName("views:flush")
            ->setDescription("Flushes all of the compiled views from cache");
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute(IResponse $response)
    {
        $this->viewCache->flush();
        $response->writeln("<success>View cache flushed</success>");
    }
}