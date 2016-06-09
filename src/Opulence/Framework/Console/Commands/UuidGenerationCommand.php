<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Console\Commands;

use Opulence\Console\Commands\Command;
use Opulence\Console\Responses\IResponse;
use Opulence\Cryptography\Utilities\Strings;

/**
 * Defines the UUID generator command
 */
class UuidGenerationCommand extends Command
{
    /** @var Strings The string utility */
    private $strings = null;

    /**
     * @param Strings $strings The string utility
     */
    public function __construct(Strings $strings)
    {
        parent::__construct();

        $this->strings = $strings;
    }

    /**
     * @inheritdoc
     */
    protected function define()
    {
        $this->setName("uuid:generate")
            ->setDescription("Creates a UUID");
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
    {
        $response->writeln("<info>{$this->strings->generateUuidV4()}</info>");
    }
}