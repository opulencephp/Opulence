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

    public function __construct(array $configArray = [], array $requiredFields)
    {
        $this->requiredFields = $requiredFields;

        parent::__construct($configArray);
    }

    /**
     * {@inheritdoc}
     */
    protected function isValid(array $configArray)
    {
        return $this->hasRequiredFields($configArray, $this->requiredFields);
    }
} 