<?php
/**
 * Security
 */
namespace View;
if(!defined('PROJECT_ROOT_DIR')) exit('No direct script access allowed');
class View
{
	private	$_view_path		= NULL,
			$_assign_data	= array(),
			$_view_content	= '',
			$_template_tags	= array(
								'left'		=> '{',
								'right'		=> '}',
								'variable'	=> '$',
								'function'	=> '=',
								'if'		=> '?',
								'for'		=> '?',
								'end'		=> '/',
							);
	public function __construct(){}

	public function setViewPath( $path )
	{
		$this->_view_path	= $path;
	}

	public function setLayout( $name )
	{
		$this->_layout_kind	= $name;
	}

	public function setAssgin( $key, $value )
	{
		$this->_assign_data[$key]	= $value;
	}

	public function setAssignArray( $assign )
	{
		foreach( $assign AS $key => $value )
		{
			$this->setAssgin( $key, $value );
		}
	}

	public function display()
	{
		// set vie path
		$this->_checkViewPath();

		// inlcude view content
		$this->_setViewContent();

		// parse view content
		$this->_parseViewContent();

		// echo result
	}

	private function _checkViewPath()
	{
		$li_router	= &li_objs('router');
		if( $this->_view_path == NULL )
		{
			$this->_view_path	= dirname($router->getController()) . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . $router->getFunction() . '.html';
		}

		if(is_file( $this->_view_path ) == FALSE)	li_throw_exception('Not find controller path.');
	}

	private function _setViewContent()
	{
		$this->_view_content	= file_get_contents( $this->_view_path );
	}

	private function _parseViewContent()
	{
		$result_content			= '';
		$prev_is_space			= FALSE;
		$template_is_start		= FALSE;
		$template_value			= '';
		$template_type			= FALSE;

		for( $i = 0, $length = strlen( $this->_view_content ); $i < $length; $i++ )
		{
			$str		= $this->_view_content[$i];

			if( $template_is_start == TRUE )
			{
				if(trim( $str ) == '')	continue;

				if( $template_type == FALSE )
				{
					switch( $str )
					{
						case $this->_template_tags['variable']:
						case $this->_template_tags['function']:
						case $this->_template_tags['if']:
						case $this->_template_tags['for']:
							$template_type	= $str;
							break;
						default:
							$template_is_start	= FALSE;
							$template_value		= '';
							$template_type		= FALSE;
					}
				}
				else
				{
					switch( $template_type )
					{
						case $this->_template_tags['variable']:
							if( $str == $this->_template_tags['right'] )
							{
								$result_content		= isset( $this->_assign_data[$template_value] ) ? '<?php echo $template_value; ?>' : '';
								$template_is_start	= FALSE;
								$template_value		= '';
								$template_type		= FALSE;
								$prev_is_space		= FALSE;
							}
							else
							{
								$template_value .= $str;  		// 一直到找到 } 结束 用$_assign_data 结果替换, 不存在的话 设为‘’字符串
							}
							break;
						case $this->_template_tags['function']:	//
							$template_value .= $str;
						case $this->_template_tags['if']:
							$template_value .= $str;
						case $this->_template_tags['for']:
							$template_value .= $str;
							break;
						default:
							li_throw_exception('模版类型设置错误.');
					}

				}
			}
			else if( $str == $this->_template_tags['left'] )
			{
				$template_is_start	= TRUE;
				$prev_is_space		= FALSE;
			}
			else
			{
				if(trim( $str ) == '')
				{
					if( $prev_is_space == FALSE )
					{
						$prev_is_space		= TRUE;
						$result_content		.= $str;
					}
					continue;
				}
				else
				{
					$prev_is_space	= FALSE;
					$result_content	.= $str;
				}
			}
//		clearstatcache()清除 php对文件系统的缓存
//			filemtime()比较模版文件和controller文件的时间，(如果模版文件时间 遭遇controller 则重写compile file. 反之直接使用过去编译的文件 ?
//		a file_get_contents(controller => 文件目录使用被修改需要找的基本信息);
//		b 如果找到了原来存在的模版，并且php代码都没有修改 那么直接选择已经编译好的文件，显示
//      c 如果找不到做编译操作
//			1. file flag replace
//          2. set compile file			//controller path encode 以后，首尾各2字符做文件路径 path encode.tpl为文件名
//          3. include compile  if(exists compile tag) 1
//
//
// 不对，一个模版里面可能include多个php, 解决 get_included_files() 找出所有这次compile file生成时，加载php文件。如果有一个文件被更新或者删除。需要重新编译.)

// 问题：如果模版里面有一处使用file_get_contents()输出内容。同时这个文件中也包含模版符号怎么办。



			//test	= '{$test}' 需要考虑
			//test	= array('{$test}') 需要考虑

			// {$test}		对于变量 替换后如果还是以 模版符号{$}，不处理直接输出
			//	<? //echo $test;}//?>
			// {=function_name(test)}		对于方法 替换后如果还有 模版符号{$}，那么通过函数回掉，继续解开{$}。
				<? //echo function_name($test);//}?>
			// {?test}
				<? //if($test): //?>
			// {:test}
				<? //else($test):// ?>
			// 		{?test}
						//<? if($test):// ?>
			// 		{/}
						<?// ENDIF// ?>
			// {/}
			//	<? //ENDIF;// ?>

			// {@test}
			<? //is_array($test): //?>
				<? //foreach($test $key => $value ): ?>
// 					{.key_} => {.value_}
// 						{@ .child}
// 							{..iii}
// 						{/}
// 					{.abc}

			// {:}
				<? //endforeach; ?>
				<? //ELSE: ?>
			// {/}
				<?php //end if?>
		}

		return $result_content;
	}
}

