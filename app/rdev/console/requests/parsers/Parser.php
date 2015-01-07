<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines a base request parser
 */
namespace RDev\Console\Requests\Parsers;
use RDev\Console\Requests;

abstract class Parser implements IParser
{
    /** @var Argument The parser to use for arguments */
    private $argumentParser = null;
    /** @var LongOption The parser to use for long options */
    private $longOptionParser = null;
    /** @var ShortOption The parser to use for short options */
    private $shortOptionParser = null;

    public function __construct()
    {
        $this->argumentParser = new Argument();
        $this->longOptionParser = new LongOption();
        $this->shortOptionParser = new ShortOption();
    }

    /**
     * Parses a list of tokens into a request
     *
     * @param array $tokens The tokens to parse
     * @return Requests\Request The parsed request
     */
    protected function parseTokens(array $tokens)
    {
        $request = new Requests\Request();
        $argumentCounter = 0;

        while($token = array_shift($tokens))
        {
            if(substr($token, 0, 2) == "--")
            {
                $option = $this->longOptionParser->parse($token, $tokens);
                $request->addOptionValue($option[0], $option[1]);
            }
            elseif(substr($token, 0, 1) == "-")
            {
                $options = $this->shortOptionParser->parse($token);

                foreach($options as $option)
                {
                    $request->addOptionValue($option[0], $option[1]);
                }
            }
            else
            {
                if($argumentCounter == 0)
                {
                    // We consider this to be the command name
                    $request->setCommandName($token);
                }
                else
                {
                    // We consider this to be an argument
                    $request->addArgumentValue($this->argumentParser->parse($token));
                }

                $argumentCounter++;
            }
        }

        return $request;
    }
}