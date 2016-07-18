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

use Core\Config;

class template_driver{
	public $TempDir;	//模版目录
	public $driver;
	public function __construct(){
		//step2、加载模版驱动
			if(is_exist(Config::get('template_driver')))
				$dirver_name = Config::get('template_driver');
			$driver_file = __DIR__.'/'.$dirver_name.'/'.$dirver_name.'_driver.php';
			if(!is_file($driver_file))
				show_error('Template driven file ( '.$driver_file.' ) does not exist');
			load($driver_file);	//加载文件
			$object_name = $dirver_name.'Driver';
			$this->driver = new $object_name();

	}
	/*
	 * 	设置模版目录
	 *
	 */
	public function setTempDir($tempDir = null){
		$this->driver->Smarty->template_dir = $tempDir;
		$this->TempDir = $tempDir;
	}

	public function display($value = null){
		if(!is_file($this->TempDir.$value)){
			show_error('Template('.$this->TempDir.$value.') file does not exist');
		}
		return $this->driver->display($value);
	}

	public function __call($key,$value){
		return $this->driver->$key($value);
	}

}