<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks the reader for use in testing
 */
namespace RDev\Tests\Models\Configs\Mocks;
use RDev\Models\Configs;

class Reader extends Configs\Reader
{
    /** @var bool True if the config should return as valid, otherwise false and it returns as invalid */
    private $isValid = true;

    /**
     * {@inheritdoc}
     */
    public function hasRequiredFields(array $configArray, array $requiredFields)
    {
        return parent::hasRequiredFields($configArray, $requiredFields);
    }

    /**
     * Sets whether or not the validation function should return true or false for use in testing
     *
     * @param bool $isValid True if configs should return as valid, otherwise false and they should return as invalid
     */
    public function setValidFlag($isValid)
    {
        $this->isValid = $isValid;
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfig(Configs\IConfig $config)
    {
        return $this->isValid;
    }
} 