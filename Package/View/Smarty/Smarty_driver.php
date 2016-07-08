<?php
/*
 * 	smart引擎的驱动
 * 		直接完成对smart引擎的一些操作，
 * 		可以扩展方法，更好的兼容引擎
 *
 */

class smartyDriver {
	public $Smarty;

	public function __construct($file_path){
		$smarty_file = __DIR__.'/Smarty.class.php';

		$compile_dir = RUNTIME_DIR.'Compile/';	//编译目录
		$cache_dir = RUNTIME_DIR.'Cache/';	//缓存目录
		//检查缓存目录

		if(!is_dir($compile_dir)) create_dir($compile_dir);
		if(!is_writable($compile_dir)){
			show_error('Directory ( '.$compile_dir.' ) does not have write permission');
		}

		load($smarty_file);	//记录加载文件
		$this->Smarty = new Smarty();

		if(defined('MODULE_NAME')) $path[] = MODULE_NAME;	//模块名称
		if(is_exist(trim(C('template_default'),'/')))
			$path[] = trim(C('template_default'),'/');
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
			$this->Smarty->compile_dir = $compile_dir;	//设置编译目录
			$this->Smarty->template_dir = dirname($file_path);	//设置模版目录
			$this->Smarty->cache_dir  = RUNTIME_DIR.'cache/';	//设置缓存目录
			$this->Smarty->caching = C('template_cache');   //开启缓存
			$this->Smarty->cache_lifetime = C('template_cache_lifetime');  //缓存存活时间（秒）
			$this->Smarty->compile_check = false;	//编译检查变量，如果开启此变量，smarty会检查模板文件是否改动过，
			$this->Smarty->force_compile = C('force_compile');	//强制重新编译
			$this->Smarty->left_delimiter = C('left_delimiter');
			$this->Smarty->right_delimiter = C('right_delimiter');


	}

	public function __call($key,$value){

		list($v1,$v2) = $value;
		if(!method_exists($this->Smarty,$key))
			show($key.' Methods there is no');
		list($v1,$v2,$v3) = $value[0];
		$this->Smarty->$key($v1,$v2);
	}

	public function display($value){
		if(!method_exists($this->Smarty,'display'))
			show('display() Methods there is no');
		$result = $this->Smarty->display($value);
		return $result;
	}

}