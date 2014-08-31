<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a simple config
 */
namespace RDev\Models\Configs;

class Config implements IConfig, \ArrayAccess
{
    /** @var array The config as an array */
    protected $configArray = [];

    /**
     * @param array $configArray The config array to initialize from
     */
    public function __construct(array $configArray = [])
    {
        if($configArray != [])
        {
            $this->fromArray($configArray);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function fromArray(array $configArray)
    {
        if(!$this->isValid($configArray))
        {
            throw new \RuntimeException("Invalid config");
        }

        $this->configArray = $configArray;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->configArray[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return isset($this->configArray[$offset]) ? $this->configArray[$offset] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        if(is_null($offset))
        {
            $this->configArray[] = $value;
        }
        else
        {
            $this->configArray[$offset] = $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->configArray[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return $this->configArray;
    }

    /**
     * Gets whether or not the config has the required fields
     * This is a useful validation function
     *
     * @param array $configArray The config array to validate
     *      The reason we pass it into this method is so that we can recursively call it on sub-keys
     * @param array $requiredFields The array of keys required by the config
     * @return bool True if the config has the required fields, otherwise false
     */
    protected function hasRequiredFields(array $configArray, array $requiredFields)
    {
        foreach($requiredFields as $key => $value)
        {
            if(!array_key_exists($key, $configArray))
            {
                return false;
            }

            if(is_array($value))
            {
                return $this->hasRequiredFields($configArray[$key], $requiredFields[$key]);
            }
        }

        return true;
    }

    /**
     * Validates the config
     *
     * @param array $configArray The config array to validate
     * @return bool True if the config is valid, otherwise false
     */
    protected function isValid(array $configArray)
    {
        // Let extending classes override this
        return true;
    }
} 