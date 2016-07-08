<?php
class smartyDriver {
	public $Smarty;

	public function __construct($file_path){
		$smarty_file = __DIR__.'/Smarty.class.php';

		require_once($smarty_file);
		trace_add('load_file',$smarty_file);	//记录加载文件
		$this->Smarty = new Smarty();

		if(defined('MODULE_NAME')) $path[] = MODULE_NAME;
		if(is_exist(trim(C('temp_default'),'/')))
			$path[] = trim(C('temp_default'),'/');
		if(defined('DIRECTORY_NAME')) $path[] = DIRECTORY_NAME;
		if(defined('CONTROLLER_NAME')) $path[] = CONTROLLER_NAME;

		$cache_dir = RUNTIME_DIR.'html/'.implode('/',$path);

		//检查缓存目录
			if(!is_writable($cache_dir)){
				show_error('Directory ( '.$cache_dir.' ) does not have write permission');
			}

		//初始化 Smarty 配置
			$this->Smarty->compile_dir = $cache_dir;	//设置缓存目录
			$this->Smarty->template_dir = dirname($file_path);	//设置模版目录

			if(C('temp_demo/left_delimiter'))
				$left_delimiter = C('temp_demo/left_delimiter');
			else
				$left_delimiter = C('left_delimiter');

			$this->Smarty->left_delimiter = $left_delimiter;

			if(C('temp_demo/right_delimiter'))
				$right_delimiter = C('temp_demo/right_delimiter');
			else
				$right_delimiter = C('right_delimiter');

			$this->Smarty->right_delimiter = $right_delimiter;



		$this->Smarty->php_handling=SMARTY_PHP_PASSTHRU ;


	}


	public function __call($key,$value){
		list($v1,$v2) = $value;
		if(!method_exists($this->Smarty,$key))
			show($key.' Methods there is no');


		list($v1,$v2,$v3) = $value[0];

		$this->Smarty->$key($v1,$v2);

	}

	public function _execute($key,$va){

	}

	public function display($value){
		if(!method_exists($this->Smarty,'display'))
			show('display() Methods there is no');

		spl_autoload_unregister('autoloader::autoload');
		$result = $this->Smarty->display($value);
		spl_autoload_register('autoloader::autoload');

		return $result;


	}

}