<?php
class autoloader{
	public static $param = array(
		
	);
	private static $suffix = '.php';

	public static function _initialize(){
		self::$param[]= PACKAGE_DIR;	//包目录
		self::$param[]= PACKAGE_DIR.'core/';	//包目录->核心目录
		self::$param[]= PACKAGE_DIR.'databases/';	//包目录->数据库驱动目录


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

		$dir_list = self::$param;

		foreach($dir_list as $k=>$v){
			$file = $v.$className.self::$suffix;

			if(is_file($file)){
				trace_add('load_file',$file);
				return require_once($file);
			}
		}
		show_error('This class( '.$className.' ) does not exist ');
    }
}
autoloader::_initialize();
spl_autoload_register('autoloader::autoload');
?>