<?php
namespace Core;

class Config  {

	private static $_config_paths = array();

	private static $config = array();

	private static $config_cache;	//缓存

	private static $config_write;	//缓存预写入

	public static $is_cache = true;	//是否开启缓存
	public static $time_cache = 7200;	// (S)缓存周期


	public static function _initialize(){
		Self::$_config_paths[]= PACKAGE_DIR.'Config/';	//配置目录
		Self::$_config_paths[]= APP_PATH.'Config/';	//公共配置目录

		//读取缓存
		$config_cache_file = RUNTIME_DIR.'Config/main';
		if(is_file($config_cache_file)){
			$config_cache = json_decode(file_get_contents($config_cache_file),true);
			Self::$config_cache = $config_cache;
		}
	}


	/*
     * @introduce:  根据医师类型 形成查询条件
     * @param1：	需要加载的文件
	 * @param2：	允许屏蔽当配置文件不存在时产生的错误信息:
     */
	public static function load( $file = '' , $fail_gracefully = false){
		$config_name = $file = strtolower($file);
		if(!empty(Self::$config[$config_name])){
			return true;
		}

		//Step1、判断加载的配置是否在与缓存中并且是否过期，如果存在则不再加载
			//缓存过期时间存在
			if(Self::$is_cache && !empty(Self::$config_cache['overtime'][$config_name])){
				//还未过期
				if(time() < Self::$config_cache['overtime'][$config_name]){
					Self::$config[$config_name] = Self::$config_cache['config'][$config_name];
					trace_add('load_config','(缓存)：'.$config_name);	//记录加载文件
					return true;
				}
			}

		//Step2、依次加载模块配置，公共配置，系统配置等文件，进行合并
			$config = array();	//加载的配置
			$config_tmp = array();	//临时配置存放
			foreach(Self::$_config_paths as $v){
				$file_path = rtrim($v,'/').'/'.$file.'.php';
				if(is_file($file_path)){
					trace_add('load_config',$file_path);	//记录加载文件
					$config_tmp[] = require_once($file_path);
					$loaded = true;
				}
			}
			if(!empty($config_tmp)){
				foreach($config_tmp as $item){
					if(!empty($item)){
						if(empty($config))
							$config = $item;
						else
							$config = array_merge($config,$item);
					}
				}
			}

		//Step3、将配置添加至config,和$config_write预写入变量中
			Self::$config[$config_name] = $config;
			Self::$config_write['overtime'][$config_name] = time()+Self::$time_cache;
			Self::$config_write['config'][$config_name] = $config;

	}
	/*
	 * 	手动调用析构方法
	 */
	public static function destruct(){

		/*
		 * 	生成缓存
		 *		如果缓存开启，且预写入缓存不为空 则写入
		 */
		if(Self::$is_cache && !empty(Self::$config_write)){
			$config_cache_file = RUNTIME_DIR.'Config/main';
			foreach(Self::$config_write['overtime'] as $k=>$v){
				Self::$config_cache['overtime'][$k] = $v;
				Self::$config_cache['config'][$k] = Self::$config_write['config'][$k];
			}
			if(!empty(Self::$config_cache))
				file_put_contents($config_cache_file,json_encode(Self::$config_cache));
		}


	}


	/*
	 * 	获取配置
	 */
	public static function get($name){
		$key = explode('/',$name);
		if(count($key) == 1){
			array_unshift($key,'config');
		}
		$config = Self::$config;
		foreach($key as $v){
			$config = $config[$v];
		}
		return $config;
	}
}

?>
