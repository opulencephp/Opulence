<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the flush view cache command
 */
namespace RDev\Framework\Console\Commands;
use RDev\Console\Commands;
use RDev\Console\Responses;
use RDev\Views\Cache;

class FlushViewCache extends Commands\Command
{
    /** @var Cache\ICache The view cache */
    private $viewCache = null;

    /**
     * @param Cache\ICache $viewCache The view cache
     */
    public function __construct(Cache\ICache $viewCache)
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
    protected function doExecute(Responses\IResponse $response)
    {
        $this->viewCache->flush();
        $response->writeln("<success>Cache flushed</success>");
    }
}