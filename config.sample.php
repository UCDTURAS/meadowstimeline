<?php
/**
 * Configuration paramaters array.
 *
 * change params and renmae to `config.php`
 *
 * @see Config::__construct()
 * @var array
 */
$config = array(
    'db' => array(
        'host' => 'localhost',
        'user' => 'root',
        'pass' => 'toor',
        'name' => 'meadows',
    ),
    'admin' => array(
        'email' => 'Daithi Coombes',
        'name' => 'daithi.coombes@futureanalytics.ie',
    ),);

define('MEADOWS_DEBUG', 1);

return $config;
