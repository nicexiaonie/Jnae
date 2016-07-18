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

	public static $assign;
	public static $tempDir;

	public static  function assign($key=null,$value=null){
		$assign = self::$assign;
		$assign[$key] = $value;
		self::$assign = $assign;
	}
	public static function display($value = null){
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

		if(empty(Self::$tempDir)) Self::setTempDir();
		$template_driver -> setTempDir(Self::$tempDir);	//设置目录
		$template_driver -> display($temp_file);
	}

	/*
	 * 设置模版目录
	 */
	public static function setTempDir($tempDir = null){
		if(empty($tempDir)){
			//默认获取模版目录
			if(defined('MODULE_NAME')) $path[] = MODULE_NAME;
			if(!is_exist(Config::get('template_path'))){
				$path[] = 'View';
			}
			if(is_exist(trim(Config::get('template_default'),'/')))
				$path[] = trim(Config::get('template_default'),'/');
			if(defined('DIRECTORY_NAME')) $path[] = DIRECTORY_NAME;
			if(defined('CONTROLLER_NAME')) $path[] = CONTROLLER_NAME;
			$tempDir = APP_PATH
				.implode('/',$path).'/';
		}
		return Self::$tempDir = $tempDir;
	}


}

?>