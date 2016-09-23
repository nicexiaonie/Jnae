<?php
/*
 *	此类是操作模版引擎临时类
 * 		你所对模版操作的数据临时存放在此类中，
 * 		在dispaly时进行加载模版驱动 处理之前操作的数据
 *
 */
namespace View;
use Core\Config;
use Core\Hook;

class View {

	/**
	 * 	模版基础目录
	 * 	string
	 */
	public static $basic_dir = '';

	/**
	 * 	模版目录
	 * 	string
	 */
	public static $temp_dir = '';

	/**
	 * 	模版文件
	 * 	string
	 */
	public static $filename = '';

	/**
	 * 	编译目录
	 * 	string
	 */
	public static $compile_dir = '';

	/**
	 * 	缓存目录
	 * 	string
	 */
	public static $cache_dir = '';

	/**
	 *  临时分配模版变量
	 */
	public static $assign = array();


	/**
	 * 	初始化模版引擎
	 *	实例模版驱动
	 *	初始化并自动计算引擎中需要的变量设置
	 *
	 * 	返回驱动中的init方法 因各个引擎机制不同，因此返回的值和实现方法也不同，具体的实现方法在驱动中
	 */
	public static function init($value = null){

		#视图输出开始标签位
		Hook::listen('view_begin');

		//step1、实例相应驱动
			$class = '\\View\\'.Config::get('template_driver');
			$view = new $class($value);

		//step2、设置参数
            #基础目录
			if(empty(self::$basic_dir)) self::setBasicDir();
            #缓存目录
			if(empty(self::$temp_dir)) self::setTempDir();
            #编译目录
			if(empty(self::$compile_dir)) self::setCompileDir();
            #缓存目录
			if(empty(self::$cache_dir)) self::setCacheDir();
            #模版文件
			if(empty(self::$filename)) self::setFileName();

		//step3、处理模版变量
			Config::load('view');

		return $view->run();
	}
	public static function assign($k,$v){
		static::$assign[$k] = $v;
	}

	public static function display($filename){
		if(empty($filename)){
			self::$filename = $filename;
		}
		return true;
	}

	/*
	 * 设置模版目录
	 */
	private  static function setBasicDir(){
		$path = array();

		if(!is_exist(Config::get('template_path'))){
			//目录位置在各自应用目录下
			$path[] = rtrim(APP_PATH,'/');
		}else{
			//自定义模版目录
			$path[] = ROOT.join('/',array_filter(explode('/',Config::get('template_path'))));
		}

		//当前模块
		if(defined('MODULE_NAME')) $path[] = MODULE_NAME;


		if(!is_exist(Config::get('template_path'))){
			$path[] = 'View';
		}

		//模版主题
		if(is_exist(trim(Config::get('template_default'),'/')))
			$path[] = trim(Config::get('template_default'),'/');


		$dir = join('/',$path).'/';

		return self::$basic_dir = $dir;

	}

	private static function setTempDir(){
		$path = array();
		if(defined('DIRECTORY_NAME')) $path[] = DIRECTORY_NAME;
		if(defined('CONTROLLER_NAME')) $path[] = CONTROLLER_NAME;

		$dir = self::$basic_dir. join('/',$path).'/';

		self::$temp_dir = $dir;
	}

	private static function setCompileDir(){
		$compile_dir = RUNTIME_DIR.'Compile/';	//编译目录

		self::$compile_dir = $compile_dir;
	}
	private static function setCacheDir(){
		$cache_dir = RUNTIME_DIR.'Cache/';	//缓存目录

		self::$cache_dir = $cache_dir;
	}
	private static function setFileName(){
		$filename = FUNCTION_NAME.Config::get('template_suffix');


		switch(Config::get('template_unlimit')){
			case 'ucwords':
				$filename = ucwords(strtolower($filename));
				break;
			case 'strtolower':
				$filename = strtolower($filename);
				break;
			default:
				break;
		}
		self::$filename = $filename;
	}


}

?>