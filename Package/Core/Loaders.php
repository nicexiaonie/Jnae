<?php
namespace Core;

class Loaders{
	public static $is_helper = array();	//已加载的函数文件

	public static function _initialize(){
		Self::helper('/file');	//加载文件操作函数
		Self::helper('/versions');	//php内置函数版本兼容库
	}

	public static function load($className = false , $Object = true){
		if(!$className) return false;
	}
	/*	装载函数
	 * 	param1 string
	 * 		[demo]	//加载当前模块下的函数
	 * 		[/demo]	//加载框架下函数
	 * 		[admin/demo]	//加载某个模块下的函数
	 *
	 */
	public static function helper($className = false){
		if($className == '/common') return false;
		if(!$className) return false;
		$className = explode('/',$className);
		if(count($className) == 1){
			$file_path = APP_PATH.'Helpers/'.$className[0].'.php';
		}else if(count($className) == 2){
			if(empty($className[0])){
				$file_path = PACKAGE_DIR.'Helpers/'.$className[1].'.php';
			}else{
				$file_path = APP_PATH.$className[0].'/Helpers/'.$className[1].'.php';
			}
		}
		if(empty($file_path)) exit('Helpers filename does not empty');

		if(!is_file($file_path)){
			show_error('Helpers file('.$file_path.') does not exist');
			return false;
		}
		load($file_path);
		return true;
	}

	/*
	 * 	加载类库
	 */
	public static function library($className,$key = false){
		$className = trim($className,'/');
		//如果有 / 则解析文件路径
		if( stripos($className,'/') ){
			$dir = explode('/',$className);
			$className = array_pop($dir);
			$dir = implode('/',$dir);
			$classPath = $dir.'/'.$className;
		}else{
			$classPath = $className = $className;
		}
		load(PACKAGE_DIR.'Library/'.$classPath.'.php');
		if(!class_exists($className)) 1;
		$Object = new $className();
		$key ? $instanceName = $key : $instanceName = $className;
		return $Object;
	}
}

?>