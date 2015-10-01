<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the array list tokenizer
 */
namespace Opulence\Console\Requests\Tokenizers;

use RuntimeException;

class ArrayListTokenizer implements ITokenizer
{
    /**
     * @inheritdoc
     */
    public function tokenize($input)
    {
        if(!isset($input["name"]))
        {
            throw new RuntimeException("No command name given");
        }

        if(!isset($input["arguments"]))
        {
            $input["arguments"] = [];
        }

        if(!isset($input["options"]))
        {
            $input["options"] = [];
        }

        $tokens = [$input["name"]];
        $tokens = array_merge($tokens, $input["arguments"]);
        $tokens = array_merge($tokens, $input["options"]);

        return $tokens;
    }
}