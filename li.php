<?php
use Exception\Exception;

use \Security\Security AS Security,
	\Config\Config AS Config,
	\Router\Router AS Router,
	\Exception\Exception AS Exception;

if(!defined('PROJECT_ROOT_DIR'))	exit('No direct script access allowed');

ob_start('ob_gzhandler');

define('LI_ROOT_DIR', dirname(__FILE__));
define('CORE_PATH_NAME', 'core');

if(!defined('PROJECT_CONFIG_DIR'))		define('PROJECT_CONFIG_DIR',		PROJECT_ROOT_DIR . DIRECTORY_SEPARATOR . 'config' );
if(!defined('PROJECT_CONTROLLER_DIR'))	define('PROJECT_CONTROLLER_DIR',	PROJECT_ROOT_DIR . DIRECTORY_SEPARATOR . 'controller' );
if(!defined('PROJECT_LAYOUT_DIR'))		define('PROJECT_LAYOUT_DIR',		PROJECT_ROOT_DIR . DIRECTORY_SEPARATOR . 'layout' );
//if(!defined('PROJECT_COMPILE_DIR'))		define('PROJECT_COMPILE_DIR',		PROJECT_ROOT_DIR . DIRECTORY_SEPARATOR . 'compile' );
//if(!defined('PROJECT_CACHE_DIR'))		define('PROJECT_CACHE_DIR',			PROJECT_ROOT_DIR . DIRECTORY_SEPARATOR . 'cache' );


function &li_objs( $obj_name, $file_path = NULL )
{
	$obj_name	= strtolower( $obj_name );
	static $li_objs	= array();
	if(!isset( $li_objs[$obj_name] ))
	{
		if( $file_path === NULL )	$file_path	= LI_ROOT_DIR . DIRECTORY_SEPARATOR . CORE_PATH_NAME . DIRECTORY_SEPARATOR . $obj_name .'.php';
		require_once $file_path;
		$li_objs[$obj_name]	= new $obj_name();
	}

	return $li_objs[$obj_name];
}

function li_throw_exception( $message )
{
	throw new Exception($message);
}


li_objs('security');
li_objs('config');
li_objs('router')->run();

