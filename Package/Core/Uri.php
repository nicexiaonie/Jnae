<?php
namespace Core;

class Uri {
	public $module_name = '';	//模块
	public $directory_name = '';	//分组
	public $controller_name = '';	//控制器
	public $function_name = '';	//方法

	public $segments = array();

	private $protocol = '';

	public function _initialize($app){

		//step1、解析URI
			Config::load('Uri');

			$this->config = $app->config;

			$protocol = $this->protocol = Config::get('uri/protocol');

			$protocol = empty($protocol) ? 'AUTO' : $protocol;

			switch($protocol){
				case 'AUTO' :
				case 'REQUEST_URI';
					$uri = $this->_parse_request_uri();
					break;
				case 'QUERY_STRING':
					$uri = $this->_parse_query_string();
					break;
				case 'PATH_INFO':
				default:
					$uri = isset($_SERVER[$protocol])
						? $_SERVER[$protocol]
						: $this->_parse_request_uri();
					break;
			}
			$uri = rtrim($uri,'.html');
			$this->_set_uri_string($uri);

		//step2、确定模块 分组  控制器 操作  并给予默认
			//$result = (Object)array();
			//确定模块	Config::get('uri/function_default')

			(empty($this->segments)) ?
				$this->module_name = ucfirst(strtolower(Config::get('uri/module_default'))) :
				$this->module_name = ucfirst(strtolower(array_shift($this->segments)));
			//如果分组则确定 组名称
			if(Config::get('uri/'.'module_'.strtolower($this->module_name).'/is_group')){
				(empty($this->segments)) ?
					$this->directory_name = ucfirst(strtolower(Config::get('uri/directory_default'))) :
					$this->directory_name = ucfirst(strtolower(array_shift($this->segments)));
			}
			//确定控制器
			(empty($this->segments)) ?
				$this->controller_name = ucfirst(strtolower(Config::get('uri/controller_default'))) :
				$this->controller_name = ucfirst(strtolower(array_shift($this->segments)));

			//确定操作
			(empty($this->segments)) ?
				$this->function_name = Config::get('uri/function_default') :
				$this->function_name = array_shift($this->segments);

		//step3、定义常量
			if(!empty($this->module_name)) define('MODULE_NAME',$this->module_name);
			if(!empty($this->directory_name)) define('DIRECTORY_NAME',$this->directory_name);
			if(!empty($this->controller_name)) define('CONTROLLER_NAME',$this->controller_name);
			if(!empty($this->function_name)) define('FUNCTION_NAME',$this->function_name);

/*
		show(MODULE_NAME);
		show(DIRECTORY_NAME);
		show(CONTROLLER_NAME);
		show(FUNCTION_NAME);
*/


	}



	protected function _parse_request_uri()
	{
		if ( ! isset($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME']))
		{
			return '';
		}
		$uri = parse_url('http://dummy'.$_SERVER['REQUEST_URI']);
		$query = isset($uri['query']) ? $uri['query'] : '';
		$uri = isset($uri['path']) ? $uri['path'] : '';

		if (isset($_SERVER['SCRIPT_NAME'][0]))
		{
			if (strpos($uri, $_SERVER['SCRIPT_NAME']) === 0)
			{
				$uri = (string) substr($uri, strlen($_SERVER['SCRIPT_NAME']));
			}
			elseif (strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0)
			{
				$uri = (string) substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
			}
		}


		if (trim($uri, '/') === '' && strncmp($query, '/', 1) === 0)
		{
			$query = explode('?', $query, 2);
			$uri = $query[0];
			$_SERVER['QUERY_STRING'] = isset($query[1]) ? $query[1] : '';
		}
		else
		{
			$_SERVER['QUERY_STRING'] = $query;
		}

		parse_str($_SERVER['QUERY_STRING'], $_GET);

		if ($uri === '/' OR $uri === '')
		{
			return '/';
		}


		return $this->_remove_relative_directory($uri);
	}

	protected function _remove_relative_directory($uri)
	{
		$uris = array();
		$tok = strtok($uri, '/');
		while ($tok !== FALSE)
		{
			if (( ! empty($tok) OR $tok === '0') && $tok !== '..')
			{
				$uris[] = $tok;
			}
			$tok = strtok('/');
		}
		return implode('/', $uris);
	}
	protected function _parse_query_string()
	{
		$uri = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : @getenv('QUERY_STRING');


		$return[] = $_GET[$this->config->item('module_trigger','uri')];
		$return[] = $_GET[$this->config->item('directory_trigger','uri')];
		$return[] = $_GET[$this->config->item('controller_trigger','uri')];
		$return[] = $_GET[$this->config->item('function_trigger','uri')];

		$return = implode('/',$return);
		parse_str($_SERVER['QUERY_STRING'], $_GET);

		return $this->_remove_relative_directory($return);
	}
	protected function _set_uri_string($str)
	{
		// Filter out control characters and trim slashes
		$this->uri_string = trim(remove_invisible_characters($str, FALSE), '/');

		if ($this->uri_string !== '')
		{
			// Remove the URL suffix, if present
			if (($suffix = (string) '/') !== '')
			{
				$slen = strlen($suffix);

				if (substr($this->uri_string, -$slen) === $suffix)
				{
					$this->uri_string = substr($this->uri_string, 0, -$slen);
				}
			}

			$this->segments[0] = NULL;
			// Populate the segments array
			foreach (explode('/', trim($this->uri_string, '/')) as $val)
			{
				$val = trim($val);
				// Filter segments for security
				$this->filter_uri($val);

				if ($val !== '')
				{
					$this->segments[] = $val;
				}
			}
			unset($this->segments[0]);
		}
	}
	public function filter_uri(&$str)
	{
		if ( ! empty($str) && ! empty($this->_permitted_uri_chars) && ! preg_match('/^['.$this->_permitted_uri_chars.']+$/i'.(UTF8_ENABLED ? 'u' : ''), $str))
		{
			show_error('The URI you submitted has disallowed characters.', 400);
		}
	}

}

?>