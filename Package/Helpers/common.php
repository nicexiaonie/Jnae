<?php

/*
 * 	此文件在app对象前已经装载
 *
 */
function show($value = null){
	echo '<pre>';
	var_dump($value);
	echo '</pre>';
}
/*
 * 	实例化数据库 获取数据模型
 */
function M($table = null , $db = false){
	static $_model  = array();
	$_get_instance = get_instance();
	$_get_instance->config->load('db');	//加载数据库配置
	if(empty($db)){
		$default_db = $_get_instance->config->item('DEFAULT_DB','db');
	}else{
		$default_db = $db;
	}

	if(empty($_model[$default_db])){
		$m = new \Core\Model($default_db);
		$m -> _set_table($table);
		$_model[$default_db] = $m;
		return $m;
	}else{
		$_model[$default_db] -> _set_table($table);
		return $_model[$default_db];
	}
}


/*
 * 加载实例化模型
 *
 *	区分大小写
 *
 * 模型包含：
 * 		1、模块模型
 * 		2、模块公共模型 (此类模型不能被实例化，但可以被所有的模块模型继承)
 *
 *
 * 	param1	模型名称
 *		user	当前模型下的[userModel.php]文件
 *		user/login	当前模型下的[user/loginModel.php]文件
 * 		demo:user	demo模型下 [userModel.php] 文件
 * 		demo:user/login	demo模型下 [user/loginModel.php] 文件
 * 	param2	模型分层[userModel.php]
		user Logic	当前模型下的[userLogic.php]文件
 *		user/login Logic	当前模型下的[user/loginLogic.php]文件
 * 		demo:user Logic	demo模型下 [loginLogic.php] 文件
 * 		demo:user/login Logic	demo模型下 [user/loginLogic.php] 文件
 *
 *
 */
function D($value = null , $layer = null){
	static $model_store;

	if(empty($value)) return false;

	$_get_instance = get_instance();	//引入app超级对象

	$key_code = $value;	//模型标记

	if($model_store[$key_code]) return $model_store[$key_code];	//如果此模型已被实例过则直接返回

	$value = explode(':',$value);

	$module_name = $_get_instance->uri->module_name;	//当前模块

	if(count($value) == 1){
		//当前模块
		$value = $value[0];
	}else{
		$module_name = $value[0];
		$value = $value[1];
	}

	empty($layer) ? $layer = 'Model' : 1 ;
	$class = '\\'.$module_name.'\\Model\\'.implode('\\',explode('/',$value)).$layer;

	$model_store[$key_code] = new $class();

	return $model_store[$key_code];

}

function C($key = null,$value = null){
	$_get_instance = get_instance();
	if(is_exist($key)){
		$key = explode(':',$key);

		if(count($key) == 1) array_unshift($key,null);
	}
	list($value1,$value2) = $key;

	if(is_exist($value)){

	}else{
		return $_get_instance->config->item($value2,strtolower($value1));
	}
}



/*
 * 	url类中用到  Security中xss用到
 */
function &remove_invisible_characters($str, $url_encoded = TRUE)
{
	$non_displayables = array();

	// every control character except newline (dec 10),
	// carriage return (dec 13) and horizontal tab (dec 09)
	if ($url_encoded)
	{
		$non_displayables[] = '/%0[0-8bcef]/';	// url encoded 00-08, 11, 12, 14, 15
		$non_displayables[] = '/%1[0-9a-f]/';	// url encoded 16-31
	}

	$non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127

	do
	{
		$str = preg_replace($non_displayables, '', $str, -1, $count);
	}
	while ($count);

	return $str;
}

function &show_error($message)
{
	if(DEBUG){

		exit($message);
	}else{
		trace_add('debug',$message);
	}

}

function trace_add($key,$value){
	static $trace_info_tmp;

	if(class_exists('\Core\Trace')){
		if($trace_info_tmp){
			foreach($trace_info_tmp as $k=>$v){
				foreach($v as $key_tmp=>$value_tmp){
					\Core\Trace::write($k,$value_tmp);
				}
			}
			$trace_info_tmp = false;
		}
		\Core\Trace::write($key,$value);
	}else{
		$trace_info_tmp[$key][] = $value;
	}

}
/*
 * 	获取app超级对象
 *
 */
function get_instance(){
	return app::get_instance();
}


/*
 *  判断变量是否存在
 */
function is_exist($value = null){
	if($value === null) return false;
	if($value === '') return false;
	if($value === "") return false;
	if($value === false) return false;
	return true;
}

/**
  *		加载文件  绝对路径
  *
  */
function load($file_path = null,$param = null){
	static $file_log;
	$file_code = md5($file_path.$param);
	if(empty($file_log[$file_code]) && is_exist($file_path) && is_file($file_path)){
		require_once($file_path);
		$file_log[$file_code] == 1;
		trace_add('load_file',$file_path);	//记录加载文件
	}else{
		show_error('This file( '.$file_path.' ) does not exist ');
		return false;
	}
}


/**
 * 获取输入参数 支持过滤和默认值
 * 使用方法:
 * <code>
 * I('id',0); 获取id参数 自动判断get或者post
 * I('post.name','','htmlspecialchars'); 获取$_POST['name']
 * I('get.'); 获取$_GET
 * </code>
 * @param string $name 变量的名称 支持指定类型
 * @param mixed $default 不存在的时候默认值
 * @param mixed $filter 参数过滤方法
 * @param mixed $datas 要获取的额外数据源
 * @return mixed
 */
function I($name,$default='',$filter=null,$datas=null) {
	static $_PUT	=	null;
	if(strpos($name,'/')){ // 指定修饰符
		list($name,$type) 	=	explode('/',$name,2);
	}elseif(C('VAR_AUTO_STRING')){ // 默认强制转换为字符串
		$type   =   's';
	}
	if(strpos($name,'.')) { // 指定参数来源
		list($method,$name) =   explode('.',$name,2);
	}else{ // 默认为自动判断
		$method =   'param';
	}

	switch(strtolower($method)) {
		case 'get'     :
			$input =& $_GET;
			break;
		case 'post'    :
			$input =& $_POST;
			break;
		case 'put'     :
			if(is_null($_PUT)){
				parse_str(file_get_contents('php://input'), $_PUT);
			}
			$input 	=	$_PUT;
			break;
		case 'param'   :
			switch($_SERVER['REQUEST_METHOD']) {
				case 'POST':
					$input  =  $_POST;
					break;
				case 'PUT':
					if(is_null($_PUT)){
						parse_str(file_get_contents('php://input'), $_PUT);
					}
					$input 	=	$_PUT;
					break;
				default:
					$input  =  $_GET;
			}
			break;

		case 'request' :
			$input =& $_REQUEST;
			break;
		case 'session' :
			$input =& $_SESSION;
			break;
		case 'cookie'  :
			$input =& $_COOKIE;
			break;
		case 'server'  :
			$input =& $_SERVER;
			break;
		case 'globals' :
			$input =& $GLOBALS;
			break;
		case 'data'    :
			$input =& $datas;
			break;
		default:
			return null;
	}
	if(''==$name) { // 获取全部变量
		$data       =   $input;
		$filters    =   isset($filter)?$filter:C('DEFAULT_FILTER');
		if($filters) {
			if(is_string($filters)){
				$filters    =   explode(',',$filters);
			}
			foreach($filters as $filter){
				$data   =   array_map_recursive($filter,$data); // 参数过滤
			}
		}
	}elseif(isset($input[$name])) { // 取值操作
		$data       =   $input[$name];
		$filters    =   isset($filter)?$filter:C('DEFAULT_FILTER');
		if($filters) {
			if(is_string($filters)){
				if(0 === strpos($filters,'/')){
					if(1 !== preg_match($filters,(string)$data)){
						// 支持正则验证
						return   isset($default) ? $default : null;
					}
				}else{
					$filters    =   explode(',',$filters);
				}
			}elseif(is_int($filters)){
				$filters    =   array($filters);
			}

			if(is_array($filters)){
				foreach($filters as $filter){
					if(function_exists($filter)) {
						$data   =   is_array($data) ? array_map_recursive($filter,$data) : $filter($data); // 参数过滤
					}else{
						$data   =   filter_var($data,is_int($filter) ? $filter : filter_id($filter));
						if(false === $data) {
							return   isset($default) ? $default : null;
						}
					}
				}
			}
		}
		if(!empty($type)){
			switch(strtolower($type)){
				case 'a':	// 数组
					$data 	=	(array)$data;
					break;
				case 'd':	// 数字
					$data 	=	(int)$data;
					break;
				case 'f':	// 浮点
					$data 	=	(float)$data;
					break;
				case 'b':	// 布尔
					$data 	=	(boolean)$data;
					break;
				case 's':   // 字符串
				default:
					$data   =   (string)$data;
			}
		}
	}else{ // 变量默认值
		$data       =    isset($default)?$default:null;
	}

	is_array($data) && array_walk_recursive($data,'app_filter');
	return $data;
}

function array_map_recursive($filter, $data) {
	$result = array();
	foreach ($data as $key => $val) {
		$result[$key] = is_array($val)
			? array_map_recursive($filter, $val)
			: call_user_func($filter, $val);
	}
	return $result;
}

function app_filter(&$value){
	// TODO 其他安全过滤

	// 过滤查询特殊字符
	if(preg_match('/^(EXP|NEQ|GT|EGT|LT|ELT|OR|XOR|LIKE|NOTLIKE|NOT BETWEEN|NOTBETWEEN|BETWEEN|NOTIN|NOT IN|IN)$/i',$value)){
		$value .= ' ';
	}
}


/*
     * @name:       输出
     * @parameter： all
     * @array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间]);
     *          验证规则：
     *              require 字段必须（不能为空）
     * 				function	函数验证
     *
     * unique
     *          验证条件：
     *              0：存在则验证     默认
     *              1：必须验证
     *              2：值不为空时验证
     *          附加规则：
     *              confirm：验证表单中的两个字段是否相同，定义的验证规则是一个字段名
     *              in：验证是否在某个范围内，定义的验证规则必须是一个数组
     * 				either:	必须存在一个  验证规则：array || string
 	 *
     *              length：验证长度，定义的验证规则可以是一个数字（表示固定长度）或者数字范围（例如3,12 表示长度从3到12的范围）
     *              between：验证范围，定义的验证规则表示范围，可以使用字符串或者数组，例如1,31或者array(1,31)
     *          验证时间：
     *              1：新增数据时验证
     *              2：编辑数据时验证
     *              3：全部情况下验证   默认
     * @type:   string
     *          操作类型
     *              add：插入
     *              save:更新
     *
     * @Author:     nieyuanpei
     * @date:       2016-3-21
    */
function _verification($data,$verification,$type = 'add')
{
	foreach($verification as $k=>$v){
		$v = array_pad($v, 4, 0);   //0：存在则验证     默认
		$v = array_pad($v, 5, null);
		$v = array_pad($v, 6, 3);   //3：全部情况下验证   默认

		//根据验证条件过滤
		if($v[3] == 0){
			//存在则验证  ：不存在则跳出验证
			if(!isset($data[$v[0]])){
				continue;
			}
		}else if($v[3] == 1){
			//必须验证
		}else if($v[3] == 2){
			//值不为空验证   则值为空则跳出验证
			if(empty($data[$v[0]])) continue;
		}

		//根据验证时间过滤
		if($v[5] != 3){
			if($v[5] == 1 && $type != 'add'){
				continue;
			}
			if($v[5] == 2 && $type != 'save'){
				continue;
			}
		}

		//是否需要字段必须验证
		if($v[1] == 'require'){
			if(empty($data[$v[0]])) return $v[2];
		}

		switch($v[4]){
			//验证表单中的两个字段是否相同，定义的验证规则是一个字段名
			case 'confirm':
				if(!isset($data[$v[1]])) $data[$v[1]]=null;
				if($data[$v[0]] != $data[$v[1]]){
					return $v[2];
				}
				break;
			case 'in':
				//验证是否在某个范围内，定义的验证规则必须是一个数组
				if(!in_array($data[$v[0]],$v[1])){
					return $v[2];
				}
				break;
			case 'either':
				//必须存在一个  验证规则：array || string
				$check_data_tmp = array();
				if(!empty($data[$v[0]])) $check_data_tmp[] = $data[$v[0]];
				if(is_array($v[1])){
					foreach($v[1] as $key_tmp => $value_tmp){
						if(!empty($data[$value_tmp])) $check_data_tmp[] = $data[$value_tmp];
					}
				}else{
					if(!empty($data[$v[1]])) $check_data_tmp[] = $data[$v[1]];
				}
				if(!count($check_data_tmp) > 0) return $v[2];
				break;
			case 'function':

				break;
			default:
				break;

		}
	}
	return 'success';
}


    /*
     * @name:       字段过滤
     * @parameter： all
     * @array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间]);
     *
     * @Author:     nieyuanpei
     * @date:       2016-3-21
    */
function _filtration($data,$filtration)
{
	$result = array();
	foreach($data as $k=>$v){
		if(in_array($k,$filtration)){
			$result[$k] = $v;
		}
	}
	return $result;
}




/*
 * 	生成url
 *
 */
function U($value = null,$param = array()){
	C('URI:IS_BASE_URL') ? $result = base_url() : $result = '';

	if(defined('MODULE_NAME'))
		$path[C('URI:module_trigger')] = MODULE_NAME;
	if(defined('DIRECTORY_NAME') && C('URI:module_'.MODULE_NAME.'/is_group'))
		$path[C('URI:directory_trigger')] = DIRECTORY_NAME;
	if(defined('CONTROLLER_NAME'))
		$path[C('URI:controller_trigger')] = CONTROLLER_NAME;
	if(defined('FUNCTION_NAME'))
		$path[C('URI:function_trigger')] = FUNCTION_NAME;

	if(!empty($value)) $value = explode('/',$value);

	foreach(array_reverse($path) as $k=>$v){
		if(!empty($value)) $path[$k] = array_pop($value);
	}

	switch(C('uri:protocol')){
		case 'PATH_INFO':
			$result .= '/' . implode('/',$path) . C('URI:URL_SUFFIX'); unset($path);
			break;
		case 'QUERY_STRING':
			$result .= '/' . trim($_SERVER['SCRIPT_NAME'],'/');
			break;
		default :
			break;
	}
	if(!empty($param)){

		if(!empty($path)) $param = array_merge($path,$param);
	}else{
		$param = $path;
	}

	if(!empty($param)) $result .= '?' . (http_build_query($param));

	return $result;
}

/*
 * 	改变当前的URl中参数
 */
function self_url($param = null){
	C('URI:IS_BASE_URL') ? $result = base_url() : $result = '';

	if(defined('MODULE_NAME'))
		$path[C('URI:module_trigger')] = MODULE_NAME;
	if(defined('DIRECTORY_NAME') && C('URI:module_'.MODULE_NAME.'/is_group'))
		$path[C('URI:directory_trigger')] = DIRECTORY_NAME;
	if(defined('CONTROLLER_NAME'))
		$path[C('URI:controller_trigger')] = CONTROLLER_NAME;
	if(defined('FUNCTION_NAME'))
		$path[C('URI:function_trigger')] = FUNCTION_NAME;

show();
	switch(C('URI:protocol')){
		case 'PATH_INFO':
			$result .= '/' . implode('/',$path) . C('URI:URL_SUFFIX'); unset($path);
			break;
		case 'QUERY_STRING':
			$result .= '/' . trim($_SERVER['SCRIPT_NAME'],'/');

			break;
		default :
			break;
	}


	$get = I('get.');

	if(!empty($path)) $get = array_merge($path,$get);
	foreach($param as $k=>$v){
		$get[$k] = $v;
	}

	if(!empty($get)) $result .= '?' . (http_build_query($get));
	return $result;
}

/*
 * 获取域名
 */
function base_url(){
	$domain=$_SERVER['HTTP_HOST'];
	if(empty($domain)) return '';
	return 'http://'.$domain;
}



if ( ! function_exists('is_https'))
{
	/**
	 * Is HTTPS?
	 *
	 * Determines if the application is accessed via an encrypted
	 * (HTTPS) connection.
	 *
	 * @return	bool
	 */
	function is_https()
	{
		if ( ! empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
		{
			return TRUE;
		}
		elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
		{
			return TRUE;
		}
		elseif ( ! empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off')
		{
			return TRUE;
		}

		return FALSE;
	}
}


?>