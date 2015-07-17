<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a host
 */
namespace Opulence\Applications\Environments\Hosts;

class HostName implements IHost
{
    /** @var string The host name */
    protected $value = "";

    /**
     * @param string $value The host name value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->value;
    }
}