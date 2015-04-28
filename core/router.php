<?php
namespace Router;
if(!defined('PROJECT_ROOT_DIR')) exit('No direct script access allowed');
class Router
{
	/**
	 * var data
	 */
	private	$_controller		= NULL,			//Controller file
			$_class				= NULL,			//Controller class
			$_function			= NULL,			//Controller function
			$_params			= NULL;			//Controller params

	public function __construct( $controller_dir = NULL )
	{
		$this->_parse();
	}

	public function getController()
	{
		return $this->_controller;
	}

	public function getFunction()
	{
		return $this->_function;
	}

	public function getClass()
	{
		return $this->_class;
	}

	/**
	 * new controller object
	 */
	public function run()
	{
		require_once $this->_controller;
		$controller	= new $this->_class();
		$method		= $this->_function;
		$params		= $this->_params;
		if(method_exists( $controller, $method ) == FALSE )	li_throw_exception('Not find controller method.');
		call_user_func_array(array( $controller, $method ), $params );
	}

	/**
	 * pase request uri set this class property
	 */
	private function _parse()
	{
		/*
		 * get URI path
		 */
		$uri_path	= parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );

		/*
		 * unset URI path in request data
		 */
		unset( $_GET[$uri_path], $_POST[$uri_path], $_REQUEST[$uri_path] );

		/*
		 * parse uri
		 */
		$uri_path	= trim( $uri_path, DIRECTORY_SEPARATOR );
		$parse_uris	= empty( $uri_path ) ?  array( 'index' ) : explode( DIRECTORY_SEPARATOR, $uri_path );

		/*
		 * set base property
		 */
		$tmp_path	= rtrim( PROJECT_CONTROLLER_DIR, DIRECTORY_SEPARATOR );
		foreach( $parse_uris AS $key => $item )
		{
			$tmp_path	.= DIRECTORY_SEPARATOR . strtolower( $item );

			if(		!empty( $parse_uris[$key + 1] )
				&&	is_dir( $tmp_path . DIRECTORY_SEPARATOR . strtolower( $parse_uris[$key + 1] )) == TRUE
			) continue;

			if(is_file( $tmp_path . DIRECTORY_SEPARATOR . 'controller.php' ) == TRUE )
			{
				$this->controller	= $tmp_path . DIRECTORY_SEPARATOR . 'controller.php';
				$this->class		= $item;
				$this->function		= empty( $parse_uris[$key + 1] ) ? 'index' : $parse_uris[$key + 1];
				$this->params		= array_splice( $parse_uris, $key + 2 );
				break;
			}

			li_throw_exception('Not find controller path.');
		}
	}
}