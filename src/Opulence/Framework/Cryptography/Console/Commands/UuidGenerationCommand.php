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

/**
 * Defines the UUID generation command
 */
final class UuidGenerationCommand extends Command
{
    public function __construct()
    {
        parent::__construct('uuid:generate', [], [], 'Creates a UUID');
    }
}
