<?php
namespace Controller;
class Controller
{
	private			$_template				= NULL,
					$_base_assign_data		= array(	// base assign_data
						'tpl_title'			=> '',		// meta title
						'tpl_keywords'		=> '',		// meta keywords
						'tpl_description'	=> '',		// meta description
						'tpl_top'			=> '',		// layout top
						'tpl_left'			=> '',		// layout left
						'tpl_right'			=> '',		// layout right
						'tpl_foot'			=> '',		// layout footer
						'tpl_js'			=> '',		// view js
						'tpl_css'			=> '',		// view css
	);

	/**
	 * Contruct
	 */
	public function __construct()
	{
	}

	final public function post( $key = NULL, $default = NULL )
	{
		return isset( $_POST[$key] ) ? $_POST[$key] : $default;
	}

	final public function get( $key = NULL, $default = NULL )
	{
		return isset( $_GET[$key] ) ? $_GET[$key] : $default;
	}

	final public function request( $key = NULL, $default = NULL )
	{
		return isset( $_REQUEST[$key] ) ? $_REQUEST[$key] : $default;
	}

	/**
	 * get view page
	 * @param (array) $assign_data
	 * @param (int) $expires
	 */
	public function view( $assign_data = array(), $option = array('layout'=>'default','path'=> null ))
	{
		if(class_exists('\Template_') == FALSE)	require_once LI_ROOT_DIR . '/template/Template_.class.php';
		$this->template					= new \Template_();
		$this->template->compile_dir	= PROJECT_COMPILE_DIR;
		$this->template->cache_dir		= PROJECT_CACHE_DIR;
		$this->template->assign(array_merge( $assign_data, $this->_base_assign_data ));

		$router		= &li_objs('router');
		$view_path	= dirname($router->getController()) . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . $router->getFunction() . '.html';

		$this->template->define('view', $view_path );
		$this->template->assign('tpl_view', $this->template->fetch('view'));

		$left					= LAYOUT_DIR . '/html/left.html';
		if(is_file( $left ))
		{
			$this->template->define('left', $left );
			$this->template->assign('tpl_left', $this->template->fetch('left'));
		}

		$top					= LAYOUT_DIR . '/html/top.html';
		if(is_file( $top ))
		{
			$this->template->define('top', $top );
			$this->template->assign('tpl_top', $this->template->fetch('top'));
		}

		$foot	= LAYOUT_DIR . '/html/foot.html';
		if(is_file( $foot ))
		{
			$this->template->define('foot', $foot );
			$this->template->assign('tpl_foot', $this->template->fetch('foot'));
		}

		$js_file	= dirname(\core\globals\router::getController()) . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . \core\globals\router::getFunction() . '.js';
		if(is_file( $js_file ))
		{
			$this->template->define('js', $js_file );
			$this->template->assign('tpl_js', $this->template->fetch('js'));
		}

		$css_file	= dirname(\core\globals\router::getController()) . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . \core\globals\router::getFunction() . '.css';
		if(is_file( $css_file ))	$this->template->assign('css', file_get_contents( $css_file ));

		$this->template->define('layout', LAYOUT_DIR . "/{$option['layout']}.html");

		$this->template->print_('layout');
	}
}
