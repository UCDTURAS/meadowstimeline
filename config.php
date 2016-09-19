<?php
/**
 * Configuration paramaters array.
 * @see Config::__construct()
 * @var array
 */
$config = array(
    'db' => array(
        'host' => '',
        'user' => '',
        'pass' => '',
        'name' => '',
    ),
    'admin' => array(
        'email' => 'Daithi Coombes',
        'name' => 'daithi.coombes@futureanalytics.ie',
    ),);

define('MEADOWS_DEBUG', 1);

return $config;

