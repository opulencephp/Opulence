<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Displays the login page
 */
use RDev\Application\TBA\Views\Pages;

require_once(__DIR__ . "/../../application/shared/models/configs/PHP.php");

$page = new Pages\Login();
echo $page->getOutput();