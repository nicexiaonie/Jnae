<?php
use \Core\Config;	//配置
use \Core\Loaders;	//装载
use \Core\Uri;	//装载
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
//
		$this->config = new Config();	//加载配置

		//初始化配置类
		if(method_exists($this->config,'_initialize'))
			$this->config->_initialize();

		$this->load = new Loaders();
		//初始化装载类
		if(method_exists($this->load,'_initialize'))
			$this->load->_initialize($this);

		$this->uri = new Uri();
		//初始化URI类
		if(method_exists($this->uri,'_initialize'))
			$this->uri->_initialize($this);

		autoloader::init();

		$this->prepare();	//进行准备工作

		self::$instance =& $this;

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
			$this->config->load('config');

		//step3、处理自动装载
			if($this->config->load('autoloader',true)){
				$autoload = $this->config->item(null,'autoloader');

				//加载辅助函数
				$auto_helpers = $autoload['helpers'];
				if(count($auto_helpers) > 0)
					array_walk($auto_helpers,function($v,$key){
						$this->load->helper($v);
					});
			}
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
			$Object = new $class();
			$Object->_execute();


		if(TRACE) Trace::finish();
	}

	public function __destruct (){
		/*
		unset($this->controller_object);
		unset($this->config);
		unset($this->uri);
		unset($this->load);
		*/

	}

	/*
	 * 	获取对象
	 */
	public static function  get_instance(){
		return (self::$instance);
	}

}