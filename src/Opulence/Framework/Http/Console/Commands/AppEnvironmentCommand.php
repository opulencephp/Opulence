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

/**
 * Defines the application environment command
 */
class AppEnvironmentCommand extends Command
{
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
        $response->writeln("<info>" . getenv("ENV_NAME") . "</info>");
    }
}