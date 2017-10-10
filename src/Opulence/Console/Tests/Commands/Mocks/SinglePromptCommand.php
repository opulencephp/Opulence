<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Console\Tests\Commands\Mocks;

use Opulence\Console\Commands\Command;
use Opulence\Console\Prompts\Prompt;
use Opulence\Console\Prompts\Questions\Question;
use Opulence\Console\Responses\IResponse;

/**
 * Mocks a command with a single prompt
 */
class SinglePromptCommand extends Command
{
    /** @var Prompt The prompt to use */
    private $prompt = null;

    /**
     * @param Prompt $prompt The prompt to use
     */
    public function __construct(Prompt $prompt)
    {
        parent::__construct();

        $this->prompt = $prompt;
    }

    /**
     * @inheritdoc
     */
    protected function define()
    {
        $this->setName('singleprompt');
        $this->setDescription('Asks a question');
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
    {
        $question = new Question('What else floats', 'Very small rocks');
        $answer = $this->prompt->ask($question, $response);

        if ($answer === 'A duck') {
            $response->write('Very good');
        } else {
            $response->write('Wrong');
        }
    }
}
