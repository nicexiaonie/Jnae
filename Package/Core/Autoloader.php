<?php
class Autoloader{
	public static $param = array(
	);
	private static $suffix = '.php';

	public static function _initialize(){
		self::$param[]= PACKAGE_DIR;	//包目录
		self::$param[]= APP_PATH;	//应用目录
		//self::$param[]= PACKAGE_DIR.'Core/';	//包目录->核心目录
		//self::$param[]= PACKAGE_DIR.'library/';	//包目录->库
		//self::$param[]= PACKAGE_DIR.'databases/';	//包目录->数据库驱动目录
		//self::$param[]= PACKAGE_DIR.'Component';	//包目录->构件目录



	}
	public static function init(){
		self::add_param(APP_PATH.MODULE_NAME.'/Controller/');	//公共目录->模块->控制器
		self::add_param(APP_PATH.MODULE_NAME.'/Controller/'.DIRECTORY_NAME.'/');	//公共目录->模块->分目录->控制器
		self::add_param(APP_PATH.'Models/');	//公共目录->公共模型
		self::add_param(APP_PATH.MODULE_NAME.'/Model/');	//公共目录->公共模型


//show(self::$param);
	}

	public static function add_param($path = null){
		if($path){
			if(is_dir($path))
				self::$param[]= $path;	//应用目录->公共模型
		}
	}

  /**
     * 类库自动加载，写死路径，确保不加载其他文件。
     * @param string $class 对象类名
     * @return void
     */
    public static function autoload($className = false) {
		$className = implode(explode('\\',$className),'/');
		//如果存在 /  则为命名空间加载
		if(strpos($className,'/')){
			$classPath = array_filter(explode('/',$className));
			$className = array_pop($classPath);
			$classPath = implode($classPath,'/').'/'.$className;
		}else{
			$classPath = $className;
		}

		$dir_list = self::$param;
		$sign = false;
		foreach($dir_list as $k=>$v){
			$file = rtrim($v,'/').'/'.$classPath.self::$suffix;
			//show($file);
			if(is_file($file)){
				//show($file);
				$sign = true;
				return load($file);
			}
		}

		if(!$sign) show_error('This class( '.$className.' ) does not exist ');
    }
}

autoloader::_initialize();
spl_autoload_register('Autoloader::autoload');


?>