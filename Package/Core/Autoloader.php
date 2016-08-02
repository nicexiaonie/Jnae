<?php
class Autoloader{
	public static $param = array(
	);
	private static $suffix = '.php';

	public static function _initialize(){
		self::$param[]= PACKAGE_DIR;	//包目录
		self::$param[]= APP_PATH;	//应用目录

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
		foreach($dir_list as $k=>$v){
			$file = rtrim($v,'/').'/'.$classPath.self::$suffix;
			if(is_file($file)){
				return load($file);
			}
		}
    }
}

autoloader::_initialize();
spl_autoload_register('Autoloader::autoload');


?>