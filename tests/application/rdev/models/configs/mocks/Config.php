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
     * @param array $configArray The array to convert from
     * @param array $requiredFields The list of required fields to pass validation
     */
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