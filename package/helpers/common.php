<?php

/*
 * 	此文件在app对象前已经装载
 *
 */
function show($value){
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
		$m = new model($default_db);
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
 *
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

	if(empty($layer)){
		$model_suffix = 'Model.php';	//模型后缀
		$class_suffix = 'Model';
	}else{
		$model_suffix = $layer.'.php';	//模型后缀
		$class_suffix = $layer;
	}

	if(count($value) == 1){
		//当前模块
		$value = $value[0];
	}else{
		$module_name = $value[0];
		$value = $value[1];
	}

	$app_path = APP_PATH.$module_name.'/model/';	//模块目录
	$model_file = $app_path.$value.$model_suffix;	//模块文件

	if(is_file($model_file)){
		require_once($model_file);
		trace_add('load_file',$model_file);	//记录加载文件
		$class_name = explode('/',$value);
		$class_name = array_pop($class_name).$class_suffix;
		if(class_exists($class_name)){
			$model_store[$key_code] = new $class_name();
			return $model_store[$key_code];
		}else{
			show_error('Class( '.$class_name.' ) does not exist');
			return false;
		}
	}else{
		show_error('Model file does not exist, File: '.$model_file);
	}

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
		return $_get_instance->config->item($value2,$value1);
	}


}



/*
 * 	url类中用到
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
	if(class_exists('trace')){
		if($trace_info_tmp){
			foreach($trace_info_tmp as $k=>$v){
				foreach($v as $key_tmp=>$value_tmp){
					trace::write($k,$value_tmp);
				}
			}
			$trace_info_tmp = false;
		}
		//echo $key.'---'.$value.'<br>';
		trace::write($key,$value);
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
 *  判断变量是否空
 */
function is_exist($value = null){

	if($value === null) return false;
	if($value === '') return false;
	if($value === "") return false;
	if($value === false) return false;

	return true;

}