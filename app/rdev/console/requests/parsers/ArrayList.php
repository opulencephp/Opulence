<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the array list parser
 */
namespace RDev\Console\Requests\Parsers;
use RDev\Console\Requests;

class ArrayList implements IParser
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
        $input = (array)$input;

        if(!isset($input["name"]))
        {
            throw new \RuntimeException("No command name given");
        }

        if(!isset($input["arguments"]))
        {
            $input["arguments"] = [];
        }

        if(!isset($input["options"]))
        {
            $input["options"] = [];
        }

        $inputString = implode(" ", [
            $input["name"],
            implode(" ", $input["arguments"]),
            implode(" ", $input["options"])
        ]);

        return $this->stringParser->parse($inputString);
    }
}