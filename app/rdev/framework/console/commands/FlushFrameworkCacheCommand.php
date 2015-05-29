<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the command that flushes the framework's cache
 */
namespace RDev\Framework\Console\Commands;
use RDev\Applications\Bootstrappers\IO\BootstrapperIO;
use RDev\Console\Commands\Command;
use RDev\Console\Responses\IResponse;

class FlushFrameworkCacheCommand extends Command
{
    /** @var BootstrapperIO The bootstrapper IO */
    private $bootstrapperIO = null;

    /**
     * @param BootstrapperIO $bootstrapperIO The bootstrapper IO
     */
    public function __construct(BootstrapperIO $bootstrapperIO)
    {
        parent::__construct();

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
        $cachedRegistryFileNames = [
            BootstrapperIO::CACHED_HTTP_BOOTSTRAPPER_REGISTRY_FILE_NAME,
            BootstrapperIO::CACHED_CONSOLE_BOOTSTRAPPER_REGISTRY_FILE_NAME
        ];

        foreach($cachedRegistryFileNames as $cachedRegistryFileName)
        {
            if(file_exists($this->bootstrapperIO->getCachedRegistryPath($cachedRegistryFileName)))
            {
                @unlink($this->bootstrapperIO->getCachedRegistryPath($cachedRegistryFileName));
            }
        }
    }
}