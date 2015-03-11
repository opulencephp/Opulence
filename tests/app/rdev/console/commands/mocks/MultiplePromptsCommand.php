<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a command with multiple prompts
 */
namespace RDev\Tests\Console\Commands\Mocks;
use RDev\Console\Commands;
use RDev\Console\Prompts;
use RDev\Console\Prompts\Questions;
use RDev\Console\Responses;

class MultiplePromptsCommand extends Commands\Command
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
        $this->setName("multipleprompts");
        $this->setDescription("Asks multiple questions");
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute(Responses\IResponse $response)
    {
        $question1 = new Questions\Question("Q1", "default1");
        $question2 = new Questions\Question("Q2", "default2");
        $answer1 = $this->prompt->ask($question1, $response);
        $answer2 = $this->prompt->ask($question2, $response);

        if($answer1 == "default1")
        {
            $response->write("Default1");
        }
        else
        {
            $response->write("Custom1");
        }

        if($answer2 == "default2")
        {
            $response->write("Default2");
        }
        else
        {
            $response->write("Custom2");
        }
    }
}