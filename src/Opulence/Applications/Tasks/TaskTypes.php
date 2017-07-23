<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Applications\Tasks;

/**
 * Defines the different types of tasks
 * @deprecated 1.1.0 This class will be removed
 */
class TaskTypes
{
    /** Pre-shutdown tasks */
    const PRE_SHUTDOWN = 'preShutdown';
    /** Pre-start tasks */
    const PRE_START = 'preStart';
    /** Post-start tasks */
    const POST_SHUTDOWN = 'postShutdown';
    /** Post-shutdown tasks */
    const POST_START = 'postStart';
}
