<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the command that flushes the framework's cache
 */
namespace RDev\Framework\Console\Commands;
use RDev\Applications\Bootstrappers\IO\BootstrapperIO;
use RDev\Applications\Paths;
use RDev\Console\Commands\Command;
use RDev\Console\Responses\IResponse;

class FlushFrameworkCacheCommand extends Command
{
    /** @var Paths The application paths */
    private $paths = null;
    /** @var BootstrapperIO The bootstrapper IO */
    private $bootstrapperIO = null;

    /**
     * @param Paths $paths The application paths
     * @param BootstrapperIO $bootstrapperIO The bootstrapper IO
     */
    public function __construct(Paths $paths, BootstrapperIO $bootstrapperIO)
    {
        parent::__construct();

        $this->paths = $paths;
        $this->bootstrapperIO = $bootstrapperIO;
    }

    /**
     * {@inheritdoc}
     */
    protected function define()
    {
        $this->setName("framework:flushcache")
            ->setDescription("Flushes all of the framework's cache files");
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute(IResponse $response)
    {
        $this->flushBootstrapperCache();
        $response->writeln("<info>Bootstrapper cache flushed</info>");
        $response->writeln("<success>Framework cache flushed</success>");
    }

    /**
     * Flushes the bootstrapper cache
     */
    private function flushBootstrapperCache()
    {
        $fileNames = [];

        if(isset($this->paths["tmp.framework.console"]))
        {
            $fileNames[] = $this->paths["tmp.framework.console"] . "cachedBootstrapperRegistry.json";
        }

        if(isset($this->paths["tmp.framework.http"]))
        {
            $fileNames[] = $this->paths["tmp.framework.http"] . "cachedBootstrapperRegistry.json";
        }

        foreach($fileNames as $cachedRegistryFileName)
        {
            if(file_exists($cachedRegistryFileName))
            {
                @unlink($cachedRegistryFileName);
            }
        }
    }
}