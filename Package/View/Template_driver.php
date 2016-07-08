<?php
/*
 * 	模版驱动类
 *
 * 		当进行display时，模版类会自动加载此类
 *
 * 		对模版进行检查 加载引擎驱动
 *
 * 		此类的方法为公共方法，如果方法不存在时  则调用对应引擎的驱动中的方法
 * 		（对应的引擎驱动的方法按需编写，如果不存在则直接操作引擎提供的方法，直至完成操作）
 *
 */
namespace View;

class template_driver{
	public $file_path;	//模版文件

	public function __construct(){

		$this->_get_instance = get_instance();


		$temp_suffix = C('template_suffix');

		//step1、确定模版所在位置  验证文件是否存在
			if(is_exist(C('template_path'))){
				if(defined('MODULE_NAME')) $path[] = MODULE_NAME;
				if(is_exist(trim(C('template_default'),'/')))
					$path[] = trim(C('template_default'),'/');
				if(defined('DIRECTORY_NAME')) $path[] = DIRECTORY_NAME;
				if(defined('CONTROLLER_NAME')) $path[] = CONTROLLER_NAME;
				if(defined('FUNCTION_NAME')) $path[] = FUNCTION_NAME;

				$file_path = ROOT
					.trim(C('template_path'),'/')
					.'/'
					.implode('/',$path)
					.$temp_suffix;
			}else{
				if(defined('MODULE_NAME')) $path[] = MODULE_NAME;
				$path[] = 'View';
				if(is_exist(trim(C('template_default'),'/')))
					$path[] = trim(C('template_default'),'/');
				if(defined('DIRECTORY_NAME')) $path[] = DIRECTORY_NAME;
				if(defined('CONTROLLER_NAME')) $path[] = CONTROLLER_NAME;
				if(defined('FUNCTION_NAME')) $path[] = FUNCTION_NAME;

				$file_path = APP_PATH
					.implode('/',$path)
					.$temp_suffix;
			}

			if(!is_file($file_path)) show_error('Template file ( '.$file_path.' ) does not exist');

			$this->file_path = $file_path;

		//step2、加载模版驱动
			if(is_exist(C('template_driver')))
				$dirver_name = (C('template_driver'));


			$driver_file = __DIR__.'/'.$dirver_name.'/'.$dirver_name.'_driver.php';
			if(!is_file($driver_file))
				show_error('Template driven file ( '.$driver_file.' ) does not exist');

			load($driver_file);	//加载文件

			$object_name = $dirver_name.'Driver';
			$this->driver = new $object_name($file_path);

	}

	public function display($value = null){
		return $this->driver->display(($this->file_path),$value);
	}


	public function __call($key,$value){
		return $this->driver->$key($value);
	}

	public function __destruct (){
		unset($this->driver);

	}


}