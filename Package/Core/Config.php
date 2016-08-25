<?php
namespace Core;

class Config  {

	/**
	 * 配置文件环境
	 * @var array
	 */
	private static $_config_paths = array();

	/**
	 * 配置：每个配置文件的值
	 * @var array
	 */
	private static $config = array();

	/**
	 * 配置缓存
	 * @var array
	 */
	private static $config_cache;

	/**
	 * 缓存预写入：此变量最终写入缓存
	 * @var array
	 */
	private static $config_write;

	/**
	 * 缓存开启
	 * @var bool
	 */
	public static $is_cache = false;

	/**
	 * 缓存有效周期：S 秒
	 * @var int
	 */
	public static $time_cache = 7200;

	/**
	 * 初始化操作
	 * static
	 */
	public static function _initialize(){
		self::$_config_paths[]= PACKAGE_DIR.'Config/';	//配置目录
		self::$_config_paths[]= APP_PATH.'Config/';	//公共配置目录

		//读取缓存
		$config_cache_file = RUNTIME_DIR.'Config/main';
		if(is_file($config_cache_file)){
			$config_cache = json_decode(file_get_contents($config_cache_file),true);
			self::$config_cache = $config_cache;
		}
	}

	/**
	 * 添加配置文件环境
	 * param string $path  路径
	 * static
	 */
	public static function addPath($path){
		self::$_config_paths[]= $path;
	}


	/**
     * @introduce:  根据医师类型 形成查询条件
     * @param1：	需要加载的文件
	 * @param2：	允许屏蔽当配置文件不存在时产生的错误信息:
     */
	public static function load( $file = '' , $fail_gracefully = false){
		$config_name = $file = strtolower($file);
		if(!empty(self::$config[$config_name])){
			return true;
		}

		//Step1、判断加载的配置是否在与缓存中并且是否过期，如果存在则不再加载
			//缓存过期时间存在
			if(self::$is_cache && !empty(self::$config_cache['overtime'][$config_name])){
				//还未过期
				if(time() < self::$config_cache['overtime'][$config_name]){
					self::$config[$config_name] = self::$config_cache['config'][$config_name];
					trace_add('load_config','(缓存)：'.$config_name);	//记录加载文件
					return true;
				}
			}

		//step2、增加配置环境映射管理
			$environ = Config::get('ENVIRON');
			if(!empty($environ) && !empty($_SERVER['ENVIRON'])){
				if(!empty($environ[$config_name]) && !empty($environ[$config_name][$_SERVER['ENVIRON']])){
					$file = $environ[$config_name][$_SERVER['ENVIRON']];
				}
			}

		//Step3、依次加载模块配置，公共配置，系统配置等文件，进行合并
			$config = array();	//加载的配置
			$config_tmp = array();	//临时配置存放
			foreach(self::$_config_paths as $v){

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

		//Step4、将配置添加至config,和$config_write预写入变量中
			self::$config[$config_name] = $config;
			self::$config_write['overtime'][$config_name] = time()+self::$time_cache;
			self::$config_write['config'][$config_name] = $config;

	}

	/**
	 * 手动调用析构方法
	 * static
	 */
	public static function destruct(){
		/*
		 * 	生成缓存
		 *		如果缓存开启，且预写入缓存不为空 则写入
		 */
		if(self::$is_cache && !empty(self::$config_write)){
			$config_cache_file = RUNTIME_DIR.'Config/main';
			foreach(self::$config_write['overtime'] as $k=>$v){
				self::$config_cache['overtime'][$k] = $v;
				self::$config_cache['config'][$k] = self::$config_write['config'][$k];
			}
			if(!empty(self::$config_cache))
				file_put_contents($config_cache_file,json_encode(self::$config_cache));
		}
	}


	/**
	 * 	获取配置
	 * 		[key]	获取conifg配置中的[key]
	 * 		db/	获取db配置中的全部
	 * 		db/[key]	获取db配置中的[key]项
	 */
	public static function get($name){
		$key = explode('/',$name);
		if(count($key) == 1){
			array_unshift($key,'config');
		}
		$config = self::$config;
		foreach($key as $v){
			if(!isset($config[$v])) $config[$v] = null;#防止变量警告
			if(!empty($v)) $config = $config[$v];
		}
		return $config;
	}

	/**
	 * 	临时更改配置
	 */
	public static function set($name,$value){
		$key = explode('/',$name);
		if(count($key) == 1){
			array_unshift($key,'config');
		}
		$config = & self::$config;
		$config[$key[0]][$key[1]] = $value;
		return true;
	}



}

?>
