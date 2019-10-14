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

use Aphiria\Console\Commands\Command;
use Aphiria\Console\Input\Option;
use Aphiria\Console\Input\OptionTypes;

/**
 * Defines the encryption key generation command
 */
final class EncryptionKeyGenerationCommand extends Command
{
    public function __construct()
    {
        parent::__construct(
            'encryption:generatekey',
            [],
            [new Option('show', 's', OptionTypes::NO_VALUE, 'Whether to just show the new key or replace it in the environment config')],
            'Generates an encryption key'
        );
    }
}
