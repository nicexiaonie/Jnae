<?php
/*
 *	此类是操作模版引擎临时类
 * 		你所对模版操作的数据临时存放在此类中，
 * 		在dispaly时进行加载模版驱动 处理之前操作的数据
 *
 */
namespace View;
use Core\Config;
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




	//--------------------------------------------
	//新版View

	/**
	 * 	初始化模版引擎
	 *	实例模版驱动
	 *	初始化并自动计算引擎中需要的变量设置
	 *
	 * 	返回驱动中的init方法 因各个引擎机制不同，因此返回的值和实现方法也不同，具体的实现方法在驱动中
	 */
	public static function init($value = null){

		//step1、实例相应驱动
			$class = '\\View\\'.Config::get('template_driver');
			$view = new $class($value);

		//step2、设置参数
			self::setBasicDir();
			self::setTempDir();
			self::setCompileDir();
			self::setCacheDir();
			self::setFileName();
			//show(self::$basic_dir);
			//show(self::$temp_dir);
			//show(self::$compile_dir);
			//show(self::$cache_dir);
			//show(self::$filename);

		//step3、处理模版变量
			Config::load('view');

		return $view->run();
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
			//目录位置在各自应用下
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
		self::create_dir($dir);
		return self::$basic_dir = $dir;

	}

	private static function setTempDir(){
		$path = array();
		if(defined('DIRECTORY_NAME')) $path[] = DIRECTORY_NAME;
		if(defined('CONTROLLER_NAME')) $path[] = CONTROLLER_NAME;

		$dir = self::$basic_dir. join('/',$path).'/';

		self::create_dir($dir);
		self::$temp_dir = $dir;
	}

	private static function setCompileDir(){
		$compile_dir = RUNTIME_DIR.'Compile/';	//编译目录

		self::create_dir($compile_dir);

		self::$compile_dir = $compile_dir;
	}
	private static function setCacheDir(){
		$cache_dir = RUNTIME_DIR.'Cache/';	//缓存目录
		self::create_dir($cache_dir);
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


	private static function create_dir($dir){
		if(!empty($dir) && !is_dir($dir)){
			create_dir($dir);
		}
	}









	//--------------------------------------------








	private static $assign;
	private static $tempDir;

	private static  function assign($key=null,$value=null){
		$assign = self::$assign;
		$assign[$key] = $value;
		self::$assign = $assign;
	}
	private static function display_($value = null){
		static $template_driver;

		//step1、确定模版文件，
			empty($value) ?
				$temp_file = FUNCTION_NAME.Config::get('template_suffix') :
				$temp_file = $value.Config::get('template_suffix');
		//step2、加载驱动
			if(!class_exists('template_driver',false)) load(PACKAGE_DIR.'View/Template_driver.php');
			if(empty($template_driver)) $template_driver = new Template_driver(self);
			if(!empty(self::$assign)){
				foreach(self::$assign as $k=>$v){
					$template_driver->assign($k,$v);
				}
			}

		if(empty(self::$tempDir)) self::setTempDir();
		//ECHO Self::$tempDir;
		//show($temp_file);
		$template_driver -> setTempDir(self::$tempDir);	//设置目录
		$template_driver -> display($temp_file);
	}

	/*
	 * 设置模版目录
	 */
	private static function setTempDiraa($tempDir = null){

		//默认获取模版目录

		if(!is_exist(Config::get('template_path'))){
			$path[] = 'View';
		}else{
			array_unshift($path,Config::get('template_path'));
		}
		if(is_exist(trim(Config::get('template_default'),'/')))
			$path[] = trim(Config::get('template_default'),'/');


		if(empty($tempDir)){
			if(defined('DIRECTORY_NAME')) $path[] = DIRECTORY_NAME;
			if(defined('CONTROLLER_NAME')) $path[] = CONTROLLER_NAME;
		}else{
			$tempDir = explode('/',$tempDir);
			array_unshift($path,array_shift($tempDir));
			if(defined('DIRECTORY_NAME')) $path[] = array_shift($tempDir);
			if(defined('CONTROLLER_NAME')) $path[] = array_shift($tempDir);
		}
		if(defined('MODULE_NAME')) array_unshift($path,MODULE_NAME);
		$tempDir = APP_PATH
			.implode('/',$path).'/';

		return self::$tempDir = $tempDir;
	}


}

?>