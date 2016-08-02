<?php
/*
 * 	smart引擎的驱动
 * 		直接完成对smart引擎的一些操作，
 * 		可以扩展方法，更好的兼容引擎
 *
 */

use Core\Config;
class TwigDriver {
	public $Smarty;

	public function __construct(){
		$smarty_file = __DIR__.'/lib/Twig/Autoloader.php';

		$compile_dir = RUNTIME_DIR.'Compile/';	//编译目录
		$cache_dir = RUNTIME_DIR.'Cache/';	//缓存目录
		//检查缓存目录

		if(!is_dir($compile_dir)) create_dir($compile_dir);
		if(!is_writable($compile_dir)){
			show_error('Directory ( '.$compile_dir.' ) does not have write permission');
		}

		load($smarty_file);	//记录加载文件


		if(defined('MODULE_NAME')) $path[] = MODULE_NAME;	//模块名称
		if(is_exist(trim(Config::get('template_default'),'/')))
			$path[] = trim(Config::get('template_default'),'/');
		if(defined('DIRECTORY_NAME')) $path[] = DIRECTORY_NAME;	//目录名称
		if(defined('CONTROLLER_NAME')) $path[] = CONTROLLER_NAME;	//控制器

		$compile_dir .= implode('/',$path);
		//检查编译后目录是否存在
		if(!is_dir($compile_dir)){
			create_dir($compile_dir);
		}
		//检查缓存目录是否存在
		if(!is_dir($cache_dir)){
			create_dir($cache_dir);
		}

		//初始化 Smarty 配置
		/*
			$this->Smarty->compile_dir = $compile_dir;	//设置编译目录
			$this->Smarty->cache_dir  = $cache_dir;	//设置缓存目录
			$this->Smarty->caching = Config::get('template_cache');   //开启缓存
			$this->Smarty->cache_lifetime = Config::get('template_cache_lifetime');  //缓存存活时间（秒）
			$this->Smarty->compile_check = false;	//编译检查变量，如果开启此变量，smarty会检查模板文件是否改动过，
			$this->Smarty->force_compile = Config::get('force_compile');	//强制重新编译
			$this->Smarty->left_delimiter = Config::get('left_delimiter');
			$this->Smarty->right_delimiter = Config::get('right_delimiter');
		*/

	}

	public function __call($key,$value){

	}

	public function display($value){
		echo 11;
	}


}