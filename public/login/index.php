<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Displays the login page
 */
use TBA\Views\Pages;

require_once(__DIR__ . "/../../configs/PHP.php");

$page = new Pages\Login();
echo $page->render();