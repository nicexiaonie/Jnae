<?php
class template_driver{
	public $file_path;	//模版文件

	public function __construct(){

		$this->_get_instance = get_instance();

		if(is_exist(C('temp_demo/temp_driver')))
			$temp_suffix = C('temp_'.MODULE_NAME.'/temp_suffix');
		else
			$temp_suffix = C('temp_suffix');


		//step1、确定模版所在位置  验证文件是否存在
			if(is_exist($this->_get_instance->config->item('temp_path'))){
				if(defined('MODULE_NAME')) $path[] = MODULE_NAME;
				if(is_exist(trim($this->_get_instance->config->item('temp_default'),'/')))
					$path[] = trim($this->_get_instance->config->item('temp_default'),'/');
				if(defined('DIRECTORY_NAME')) $path[] = DIRECTORY_NAME;
				if(defined('CONTROLLER_NAME')) $path[] = CONTROLLER_NAME;
				if(defined('FUNCTION_NAME')) $path[] = FUNCTION_NAME;

				$file_path = ROOT
					.trim($this->_get_instance->config->item('temp_path'),'/')
					.'/'
					.implode('/',$path)
					.$temp_suffix;
			}else{
				if(defined('MODULE_NAME')) $path[] = MODULE_NAME;
				$path[] = 'view';
				if(is_exist(trim($this->_get_instance->config->item('temp_default'),'/')))
					$path[] = trim($this->_get_instance->config->item('temp_default'),'/');
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
			if(is_exist(C('temp_demo/temp_driver')))
				$dirver_name = C('temp_'.MODULE_NAME.'/temp_driver');
			else
				$dirver_name = C('temp_driver');


			$driver_file = __DIR__.'/'.$dirver_name.'/'.$dirver_name.'_driver.php';
			if(!is_file($driver_file))
				show_error('Template driven file ( '.$driver_file.' ) does not exist');

			require_once($driver_file);
			trace_add('load_file',$driver_file);	//记录加载文件

			$object_name = $dirver_name.'Driver';
			$this->driver = new $object_name($file_path);

	}

	public function display($value = null){
		return $this->driver->display(($this->file_path),$value);
	}


	public function __call($key,$value){
		return $this->driver->$key($value);
	}


}