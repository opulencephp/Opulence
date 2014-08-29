<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks the config class for use in testing
 */
namespace RDev\Tests\Models\Configs\Mocks;
use RDev\Models\Configs;

class Config extends Configs\Config
{
    /** @var array The list of required fields */
    private $requiredFields = [];

    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        return $this->hasRequiredFields($this->configArray, $this->requiredFields);
    }

    /**
     * @param array $requiredFields
     */
    public function setRequiredFields(array $requiredFields)
    {
        $this->requiredFields = $requiredFields;
    }
} 