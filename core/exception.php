<?php
namespace Exception;
if(!defined('PROJECT_ROOT_DIR')) exit('No direct script access allowed');
class Exception extends \Exception
{
	public function __construct( $exception )
	{
		parent::__construct();
 		$this->createLog( $exception );
	}

	public function createLog( $exception )
	{
		//write log
	}
}


