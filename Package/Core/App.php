<?php
use \Core\Config;
use \Core\Loaders;
use \Core\Uri;
use \Core\Trace;

class App {

	private static $instance;	//单例

	/*
	 * 	初始化：
	 *
	 */
	public function run(){

		header("Content-type: text/html; charset=utf-8");
		ini_set('display_errors', true);
		error_reporting(E_ALL & ~E_NOTICE);



		if(TRACE) Trace::start();

		Config::_initialize();
		Loaders::_initialize();

		//初始化URI类
		$this->uri = new Uri();
		if(method_exists($this->uri,'_initialize'))
			$this->uri->_initialize($this);

		$this->prepare();	//进行准备工作

		self::$instance =& $this;

		define('IS_AJAX',(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false);

		//开始执行
		$this->execute();


	}

	/*
	 * 	整理准备工作：
	 * 		1、添加配置目录
	 *
	 */
	public function prepare(){
		//step1、添加配置文件目录
			unset($config_dir);
			$config_dir[] = rtrim(APP_PATH,'/');
			if($this->uri->module_name)
				$config_dir[] = $this->uri->module_name;
			$config_dir[] = 'Config';
			$this->config->_config_paths[] = implode($config_dir,'/');

		//加载配置文件
			Config::load('config');

		//step3、处理自动装载
			Config::load('autoloader');
			$auto_helpers = Config::get('autoloader/helpers');
			//加载辅助函数
			if(count($auto_helpers) > 0)
				array_walk($auto_helpers,function($v,$key){
					Loaders::helper($v);
				});

	}
	/*
	 *
	 * 	开始执行控制器
	 *
	 */
	public function execute(){
		//step1、确定控制器路径
			if(!empty($this->uri->module_name))
				$path[] = ($this->uri->module_name);
			$path[] = 'Controller';
			if(!empty($this->uri->directory_name))
				$path[] = $this->uri->directory_name;
			if(!empty($this->uri->controller_name))
				$path[] = $this->uri->controller_name;

		//step2、初始化Controller  并执行
			$class = '\\'.implode('\\',$path).'Controller';

			if(!class_exists($class)){
				show_error('Controller('.$class.') does not exist');
			}
			$Object = new $class();
			$Object->_execute();


		if(TRACE) Trace::finish();
	}

	public function __destruct (){

		Config::destruct();	//处理配置缓存



	}

	/*
	 * 	获取对象
	 */
	public static function  get_instance(){
		return (self::$instance);
	}

}