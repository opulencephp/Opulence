<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Displays the login page
 */
use RamODev\Application\TBA\Web\Websites\Pages;

require_once(__DIR__ . "/../../application/shared/configs/PHPConfig.php");

$page = new Pages\Login();
echo $page->getOutput();