<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Console\Commands;

use Opulence\Console\Responses\IResponse;

/**
 * Defines the version command
 */
class VersionCommand extends Command
{
    /** @var string The template for the output */
    private static $template = <<<EOF
<info>Opulence Console {{version}}</info>
EOF;
    /** @var string The version number of the application */
    private $applicationVersion = 'Unknown';

    /**
     * @param string $applicationVersion The version number of the application
     */
    public function __construct(string $applicationVersion)
    {
        parent::__construct();

        $this->applicationVersion = $applicationVersion;
    }

    /**
     * @inheritdoc
     */
    protected function define()
    {
        $this->setName('version')
            ->setDescription('Displays the application version');
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
    {
        // Compile the template
        $compiledTemplate = self::$template;
        $compiledTemplate = str_replace('{{version}}', $this->applicationVersion, $compiledTemplate);

        $response->writeln($compiledTemplate);
    }
}
