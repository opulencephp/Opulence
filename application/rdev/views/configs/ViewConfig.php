<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the view config
 */
namespace RDev\Views\Configs;
use RDev\Models\Configs;
use RDev\Models\Files;
use RDev\Views\Factories;
use RDev\Views\Templates;

class ViewConfig extends Configs\Config
{
    /**
     * {@inheritdoc}
     */
    public function exchangeArray($configArray)
    {
        if(!$this->isValid($configArray))
        {
            throw new \RuntimeException("Invalid view config");
        }

        if(!isset($configArray["templates"]["gcChance"]))
        {
            $configArray["templates"]["gcChance"] = Templates\ICache::DEFAULT_GC_CHANCE;
        }

        if(!isset($configArray["templates"]["gcTotal"]))
        {
            $configArray["templates"]["gcTotal"] = Templates\ICache::DEFAULT_GC_TOTAL;
        }

        if(isset($configArray["templates"]["cache"]))
        {
            if(is_string($configArray["templates"]["cache"]))
            {
                // We assume this is a custom cache class
                if(!class_exists($configArray["templates"]["cache"]))
                {
                    throw new \RuntimeException("Invalid custom view cache: " . $configArray["templates"]["cache"]);
                }

                $configArray["templates"]["cache"] = new $configArray["templates"]["cache"]();
            }

            if(!$configArray["templates"]["cache"] instanceof Templates\ICache)
            {
                throw new \RuntimeException("View cache does not implement ICache");
            }
        }

        $this->configArray = $configArray;
    }

    /**
     * {@inheritdoc}
     */
    protected function isValid(array $configArray)
    {
        if(!isset($configArray["templates"]))
        {
            return false;
        }

        if(isset($configArray["templates"]["gcChance"]) && !is_int($configArray["templates"]["gcChance"]))
        {
            return false;
        }

        if(isset($configArray["templates"]["gcTotal"]) && !is_int($configArray["templates"]["gcTotal"]))
        {
            return false;
        }

        if(!isset($configArray["templates"]["cachePath"]) || !is_string($configArray["templates"]["cachePath"]))
        {
            return false;
        }

        return true;
    }
}