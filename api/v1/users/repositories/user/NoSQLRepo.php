<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Provides methods for retrieving user data from a NoSQL database
 */
namespace RamODev\API\V1\Users\Repositories\User;
use RamODev\API\V1\Users;
use RamODev\Repositories;

require_once(__DIR__ . "/../../../../../repositories/NoSQLRepo.php");

class NoSQLRepo extends Repositories\NoSQLRepo implements IUserRepo
{

} 