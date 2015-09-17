<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the different types of tasks
 */
namespace Opulence\Applications\Tasks;

class TaskTypes
{
    /** Pre-shutdown tasks */
    const PRE_SHUTDOWN = "preShutdown";
    /** Pre-start tasks */
    const PRE_START = "preStart";
    /** Post-start tasks */
    const POST_SHUTDOWN = "postShutdown";
    /** Post-shutdown tasks */
    const POST_START = "postStart";
}