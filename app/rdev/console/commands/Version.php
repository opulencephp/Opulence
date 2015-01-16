<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the version command
 */
namespace RDev\Console\Commands;
use RDev\Console\Responses;

class Version extends Command
{
    /** @var string The template for the output */
    private static $template = <<<EOF
RDev Console {{version}}
EOF;
    /** @var string The version number of the application */
    private $applicationVersion = "Unknown";

    /**
     * @param string $applicationVersion The version number of the application
     */
    public function __construct($applicationVersion)
    {
        parent::__construct();

        $this->applicationVersion = $applicationVersion;
    }

    /**
     * {@inheritdoc}
     */
    protected function define()
    {
        $this->setName("version")
            ->setDescription("Displays the application version");
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute(Responses\IResponse $response)
    {
        // Compile the template
        $compiledTemplate = self::$template;
        $compiledTemplate = str_replace("{{version}}", $this->applicationVersion, $compiledTemplate);

        $response->writeln($compiledTemplate);
    }
}