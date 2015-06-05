<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the command that flushes the framework's cache
 */
namespace RDev\Framework\Console\Commands;
use RDev\Applications\Bootstrappers\IO\IBootstrapperIO;
use RDev\Applications\Paths;
use RDev\Console\Commands\Command;
use RDev\Console\Responses\IResponse;

class FlushFrameworkCacheCommand extends Command
{
    /** @var Paths The application paths */
    private $paths = null;
    /** @var IBootstrapperIO The bootstrapper IO */
    private $bootstrapperIO = null;

    /**
     * @param Paths $paths The application paths
     * @param IBootstrapperIO $bootstrapperIO The bootstrapper IO
     */
    public function __construct(Paths $paths, IBootstrapperIO $bootstrapperIO)
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
            $fileNames[] = $this->paths["tmp.framework.console"] . "/" . IBootstrapperIO::DEFAULT_CACHED_REGISTRY_FILE_NAME;
        }

        if(isset($this->paths["tmp.framework.http"]))
        {
            $fileNames[] = $this->paths["tmp.framework.http"] . "/" . IBootstrapperIO::DEFAULT_CACHED_REGISTRY_FILE_NAME;
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