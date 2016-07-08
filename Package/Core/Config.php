<?php
namespace Core;

class Config  {
	public $_config_paths = array();

	public $config = array();
	private static $_instance;
	private $_suffix = '.php';

	public function _initialize(){

		$this->_config_paths[]= PACKAGE_DIR.'Config/';	//配置目录
		$this->_config_paths[]= APP_PATH.'Config/';	//公共配置目录



	}


	/*
     * @introduce:  根据医师类型 形成查询条件
     * @param1：	需要加载的文件
	 * @param2：	第二个参数设置为 TRUE ，这可以使每个配置文件的内容存储在一个单独的数组中，数组的索引就是配置文件的文件名
	 * @param3：	允许屏蔽当配置文件不存在时产生的错误信息:
     */
	static $file_path_tmp = array();
	public function load( $file = '' , $fail_gracefully = false){

		//show($this->_config_paths);
		//屏蔽重复加载
			if(!empty(self::$file_path_tmp[$file])) return true;
			self::$file_path_tmp[$file] = 1;

		$loaded = FALSE;
		$file= trim($file,'/');
		$config_name = explode('/',$file);
		$config_name = array_pop($config_name);	//获取配置文件名
		//step1、解析配置文件
			$config = array();
			foreach($this->_config_paths as $v){
				$file_path = rtrim($v,'/').'/'.$file.$this->_suffix;
				if(is_file($file_path)){
					switch($this->_suffix){
						case '.ini':
							$config[] = parse_ini_file($file_path,true);
							break;
						case '.php':
							$config[] = require($file_path);
							break;
						default:
							break;
					}
					//show($file_path);
					trace_add('load_config',$file_path);	//记录加载文件
					if($file == 'config'){
						//show($config);
					}
					$loaded = true;
				}
			}

		$result = array();
		foreach($config as $k => $v){
			//$v = array_filter($v);	//去空
			if(!empty($v)) $result = array_merge($result,$v);
		}

		$this->$config_name = $result;

		if($loaded){
			return true;
		}
		if($fail_gracefully){
			return false;
		}

		show_error('Configuration file does not exist: '.$file);
		
		
	}



	/*
	 * 	获取配置
	 * 		默认获取config文件配置
	 * 		item('temp_suffix');
	 * 		item('temp_suffix','db');	//指定配置文件获取
	 * 		item('temp_demo/temp_suffix');	//获取temp_demo下的temp_suffix元素
	 *
	 *
	 */
	public function item($key = '' , $name = ''){
		empty($name) ? $config = $this->config : $config = $this->$name;
		if(empty($key)) return empty($config) ? false : $config;

		$key = explode('/',$key);

		foreach($key as $v){
			//if(!isset($config[$v])) return false;
			$config = $config[$v];
		}

		return $config;

	}

}

?>
