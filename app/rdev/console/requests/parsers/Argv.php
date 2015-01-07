<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the argv parser
 */
namespace RDev\Console\Requests\Parsers;
use RDev\Console\Requests;

class Argv implements IParser
{
    /** @var String The string parser */
    private $stringParser = null;

    public function __construct()
    {
        $this->stringParser = new String();
    }

    /**
     * {@inheritdoc}
     */
    public function parse($input)
    {
        if($input === null)
        {
            $input = $_SERVER["argv"];
        }

        // Get rid of the application name
        $input = trim($input);
        $input = explode(" ", $input);
        array_shift($input);

        return $this->stringParser->parse(implode(" ", $input));
    }
}