<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Cryptography\Console\Commands;

use Aphiria\Console\Commands\ICommandHandler;
use Aphiria\Console\Input\Input;
use Aphiria\Console\Output\IOutput;

/**
 * Defines the UUID generator command handler
 */
final class UuidGenerationCommandHandler implements ICommandHandler
{
    /**
     * @inheritdoc
     */
    public function handle(Input $input, IOutput $output)
    {
        $output->writeln("<info>{$this->generateUuidV4()}</info>");
    }

    private function generateUuidV4(): string
    {
        $string = \random_bytes(16);
        $string[6] = \chr(\ord($string[6]) & 0x0f | 0x40);
        $string[8] = \chr(\ord($string[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', \str_split(\bin2hex($string), 4));
    }
}
