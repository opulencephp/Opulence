<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Ioc\Bootstrappers\Inspection;

use Exception;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Throwable;

/**
 * Defines an exception that is thrown when bindings are impossible because they're not in any bootstrapper
 */
final class ImpossibleBindingException extends Exception
{
    /**
     * @inheritdoc
     * @param Bootstrapper[] $failedInterfacesToBootstrappers The mapping of failed interfaces to bootstrappers
     */
    public function __construct(array $failedInterfacesToBootstrappers, int $code = 0, Throwable $previous = null)
    {
        $message = 'Impossible to resolve following interfaces: ';

        foreach ($failedInterfacesToBootstrappers as $failedInterface => $failedBootstrappers) {
            $message .= $failedInterface . ' (attempted to be resolved in ';

            foreach ($failedBootstrappers as $failedBootstrapper) {
                $message .= \get_class($failedBootstrapper) . ', ';
            }

            // Remove the trailing ', '
            $message = substr($message, 0, -2);
            // Close the parenthesis
            $message .= ')';
        }

        parent::__construct($message, $code, $previous);
    }
}
