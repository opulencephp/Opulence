<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Orm;

/**
 * Defines different states of entities
 */
class EntityStates
{
    /** A new entity that will be registered */
    const QUEUED = 1;
    /** A registered, persisted entity */
    const REGISTERED = 2;
    /** An entity that is no longer registered */
    const UNREGISTERED = 3;
    /** An entity that will be unregistered */
    const DEQUEUED = 4;
    /** An entity that was never registered */
    const NEVER_REGISTERED = 5;
}
