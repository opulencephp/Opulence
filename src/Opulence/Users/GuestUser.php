<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Users;

use DateTime;

/**
 * Defines a guest user
 */
class GuestUser extends User
{
    public function __construct()
    {
        parent::__construct(-1, new DateTime("now"), []);
    }
} 