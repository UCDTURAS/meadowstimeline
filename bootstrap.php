<?php
namespace FAC;
use FAC;
/**
 * @package FAC
 * @author daithi coombes <webeire@gmail.com>
 */

define('MEADOWS_DIR', dirname(__FILE__));

// debug?
if(defined('MEADOWS_DEBUG') && MEADOWS_DEBUG==1){
	ini_set('display_errors', 'on');
	error_reporting(E_ALL);
}// end debug



/**
 * Autoloader.
 * @param string $class The class name including namespace
 */
spl_autoload_register(function($class){

	$file = MEADOWS_DIR . '/lib/' . str_replace("FAC\\", "", $class) . '.php';
	if(is_readable($file))
		require_once($file);
});

require_once(MEADOWS_DIR.'/vendor/autoload.php');


/**
 * Configuration
 */
new \FAC\Config(require_once(MEADOWS_DIR . '/config.php'));


/**
 * Model object
 */
global $db;
$db = Model::factory();
