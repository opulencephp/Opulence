<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

echo json_encode([
    'error' => [
        'code' => $statusCode,
        'message' => 'There was a technical error while handling your request'
    ]
]);
