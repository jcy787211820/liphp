<?php
namespace Config;
if(!defined('PROJECT_ROOT_DIR')) exit('No direct script access allowed');
class Config
{
	private $_config	= array();
	public function __construct()
	{
		foreach($this->_getFiles() AS $name	=> $file )
		{
			$this->_config[$name]	= $this->_appendVars( $file  );
		}
	}

	public function get( $key = NULL )
	{
		return $key = NULL ? $this->_config : $this->_config[$key];
	}

	/**
	 * 获取所有配置文件的路径。返回文件名与文件路径，键值对应的数组.
	 * @return multitype:string
	 */
	private function _getFiles()
	{
		$result			= array();

		$config_files	= @scandir(PROJECT_CONFIG_DIR);
		if(is_array( $paths ))
		{
			foreach( $config_files AS $config_file )
			{
				if( $config_file == '.' || $config_file == '..' )	continue;

				$name	= $config_file;
				$path	= PROJECT_CONFIG_DIR . $config_file;

				if(($tmp = strstr( $config_file, '.', TRUE )) !== FALSE)	$name	= $tmp;

				$result[$name]	= $path;
			}
		}

		return $result;
	}

	/**
	 * 解析配置文件返回一个多维数组
	 */
	private function _appendVars( $name )
	{
		return parse_ini_file( $name, TRUE );
	}
}

