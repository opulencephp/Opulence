<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the master/slave connection pool config
 */
namespace RDev\Models\Databases\SQL\Configs;
use RDev\Models\Databases\SQL;

class MasterSlaveConnectionPoolConfig extends ConnectionPoolConfig
{
    /**
     * {@inheritdoc}
     */
    public function fromArray(array $configArray)
    {
        parent::fromArray($configArray);

        // We don't want this object's config array set until we're done with this method
        // so, store it off to a temporary variable, and then set it to an empty array
        $configArray = $this->configArray;
        $this->configArray = [];

        if(isset($configArray["servers"]["slaves"]))
        {
            foreach($configArray["servers"]["slaves"] as &$slaveConfigArray)
            {
                if(!$slaveConfigArray instanceof SQL\Server)
                {
                    if(!is_array($slaveConfigArray))
                    {
                        throw new \RuntimeException("Invalid slave server config");
                    }

                    $slaveConfigArray = $this->getServerFromConfig($slaveConfigArray);
                }
            }
        }

        $this->configArray = $configArray;
    }

    /**
     * {@inheritdoc}
     */
    protected function isValid(array $configArray)
    {
        if(!parent::isValid($configArray))
        {
            return false;
        }

        // Validate all of the slave servers
        if(isset($configArray["servers"]["slaves"]))
        {
            if(!is_array($configArray["servers"]["slaves"]))
            {
                return false;
            }

            foreach($configArray["servers"]["slaves"] as $slaveConfigArray)
            {
                if(is_array($slaveConfigArray))
                {
                    if(!$this->validateServer($slaveConfigArray))
                    {
                        return false;
                    }
                }
                elseif(!$slaveConfigArray instanceof SQL\Server)
                {
                    return false;
                }
            }
        }

        return true;
    }
} 