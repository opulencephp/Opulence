<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the view config
 */
/**
 * ----------------------------------------------------------
 * Defines view properties
 * ----------------------------------------------------------
 */
return [
    // The lifetime of cached templates
    "cacheLifetime" => 3600,
    // The chance that garbage collection will be run
    "gcChance" => 1,
    // The number the chance will be divided by to calculate the probability (default is 1 in 100 chance)
    "gcTotal" => 100
];