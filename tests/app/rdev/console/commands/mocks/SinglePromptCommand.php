<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a command with a single prompt
 */
namespace RDev\Tests\Console\Commands\Mocks;
use RDev\Console\Commands;
use RDev\Console\Prompts;
use RDev\Console\Prompts\Questions;
use RDev\Console\Responses;

class SinglePromptCommand extends Commands\Command
{
    /** @var Prompts\Prompt The prompt to use */
    private $prompt = null;

    /**
     * @param Prompts\Prompt $prompt The prompt to use
     */
    public function __construct(Prompts\Prompt $prompt)
    {
        parent::__construct();

        $this->prompt = $prompt;
    }

    /**
     * {@inheritdoc}
     */
    protected function define()
    {
        $this->setName("singleprompt");
        $this->setDescription("Asks a question");
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute(Responses\IResponse $response)
    {
        $question = new Questions\Question("What else floats", "Very small rocks");
        $answer = $this->prompt->ask($question, $response);

        if($answer == "A duck")
        {
            $response->write("Very good");
        }
        else
        {
            $response->write("Wrong");
        }
    }
}