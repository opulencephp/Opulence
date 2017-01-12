<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Framework\Cryptography\Console\Commands;

use Opulence\Console\Commands\Command;
use Opulence\Console\Responses\IResponse;

/**
 * Defines the UUID generator command
 */
class UuidGenerationCommand extends Command
{
    /**
     * @inheritdoc
     */
    protected function define()
    {
        $this->setName('uuid:generate')
            ->setDescription('Creates a UUID');
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
    {
        $response->writeln("<info>{$this->generateUuidV4()}</info>");
    }

    private function generateUuidV4() : string
    {
        $string = \random_bytes(16);
        $string[6] = \chr(\ord($string[6]) & 0x0f | 0x40);
        $string[8] = \chr(\ord($string[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', \str_split(\bin2hex($string), 4));
    }
}
