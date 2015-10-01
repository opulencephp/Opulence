<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the Composer dump autoload command
 */
namespace Opulence\Framework\Console\Commands;

use Opulence\Console\Commands\Command;
use Opulence\Console\Responses\IResponse;
use Opulence\Framework\Composer\Executable;

class ComposerDumpAutoloadCommand extends Command
{
    /** @var Executable The executable wrapper */
    private $executable = null;

    /**
     * @param Executable $executable The Composer executable
     */
    public function __construct(Executable $executable)
    {
        parent::__construct();

        $this->executable = $executable;
    }

    /**
     * @inheritdoc
     */
    protected function define()
    {
        $this->setName("composer:dump-autoload")
            ->setDescription("Dumps the Composer autoload");
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
    {
        $response->write($this->executable->dumpAutoload("-o"));
    }
}