<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Http\Console\Commands;

use Opulence\Console\Commands\Command;
use Opulence\Console\Responses\IResponse;
use Opulence\Environments\Environment;

/**
 * Defines the application environment command
 */
class AppEnvironmentCommand extends Command
{
    /** @var Environment The current environment */
    private $environment = null;

    /**
     * @param Environment $environment The current environment
     */
    public function __construct(Environment $environment)
    {
        parent::__construct();

        $this->environment = $environment;
    }

    /**
     * @inheritdoc
     */
    protected function define()
    {
        $this->setName("app:env")
            ->setDescription("Displays the current application environment");
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
    {
        $response->writeln("<info>{$this->environment->getName()}</info>");
    }
}