<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Orm;

/**
 * Defines different states of entities
 */
final class EntityStates
{
    /** A new entity that will be registered */
    public const QUEUED = 1;
    /** A registered, persisted entity */
    public const REGISTERED = 2;
    /** An entity that is no longer registered */
    public const UNREGISTERED = 3;
    /** An entity that will be unregistered */
    public const DEQUEUED = 4;
    /** An entity that was never registered */
    public const NEVER_REGISTERED = 5;
}
