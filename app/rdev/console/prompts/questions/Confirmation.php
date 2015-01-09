<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines a confirmation question
 */
namespace RDev\Console\Prompts\Questions;

class Confirmation extends Question
{
    /**
     * {@inheritdoc}
     * @param bool $defaultResponse The default response
     */
    public function __construct($question, $defaultResponse = true)
    {
        parent::__construct($question, $defaultResponse);
    }

    /**
     * {@inheritdoc}
     */
    public function formatAnswer($answer)
    {
        if(is_bool($answer))
        {
            return $answer;
        }

        // Accept "yes"
        return strtolower($answer[0]) == "y";
    }
}