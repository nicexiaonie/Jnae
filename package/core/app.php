<?php
class app {



	//调试模式
	private $DEBUG = false;




	private static $instance;

	public function __construct(){

	}


	/*
	 * 	初始化：
	 *
	 */
	public function run(){

		header("Content-type: text/html; charset=utf-8");
		ini_set('display_errors', true);
		error_reporting(E_ALL & ~E_NOTICE);

		//自动加载
		require PACKAGE_DIR.'core/autoloader.php';
		trace_add('load_file',PACKAGE_DIR.'core/autoloader.php');	//记录加载文件

		if(TRACE) trace::start();

		$this->config = new config();	//加载配置
		//初始化配置类
		if(method_exists($this->config,'_initialize'))
			$this->config->_initialize();


		$this->load = new loaders();
		//初始化装载类
		if(method_exists($this->load,'_initialize'))
			$this->load->_initialize($this);

		$this->uri = new uri();
		//初始化URI类
		if(method_exists($this->uri,'_initialize'))
			$this->uri->_initialize($this);


		$this->prepare();	//进行准备工作

		self::$instance =& $this;
		//开始执行
		$this->execute();


	}

	public function init_config(){

	}
	/*
	 * 	整理准备工作：
	 * 		1、添加配置目录
	 *
	 */
	public function prepare(){
		//step1、添加配置文件目录
			//添加公共配置目录
			$config_dir[] = rtrim(APP_PATH,'/');
			$config_dir[] = 'config';
			$this->config->_config_paths[] = implode($config_dir,'/');

			//添加模块配置目录
			unset($config_dir);
			$config_dir[] = rtrim(APP_PATH,'/');
			if($this->uri->module_name)
				$config_dir[] = $this->uri->module_name;
			$config_dir[] = 'config';
			$this->config->_config_paths[] = implode($config_dir,'/');

		//加载配置文件
			$this->config->load('config');

		//step2、添加自动加载类目录
			//公共模型自动加载
			autoloader::add_param(APP_PATH.'models/');	//应用目录->公共模型
			autoloader::add_param(PACKAGE_DIR.'template/');	//应用目录->模版驱动

			if(!empty($this->uri->module_name)) $path[] = $this->uri->module_name;
			$path[] = 'controller';
			autoloader::add_param(APP_PATH.implode('/',$path).'/');	//应用目录->控制器公共目录


		//step3、处理自动加载项
			$autoload = $this->config->item('autoload');
			//加载辅助函数
			array_walk($autoload['helpers'],function($v){
				$this->load->helper($v);
			});
			//加载配置

			if(!empty($autoload['config']))
				array_walk($autoload['config'],function($v){
					$this->config->load($v);
				});





	}

	public function execute(){
		//step1、获取控制器路径
			if(!empty($this->uri->module_name)) $path[] = $this->uri->module_name;
			$path[] = 'controller';
			if(!empty($this->uri->directory_name)) $path[] = $this->uri->directory_name;
			if(!empty($this->uri->controller_name)) $path[] = $this->uri->controller_name;
			$controller_suffix = $this->config->item('controller_suffix');
			if(empty($controller_suffix)) $controller_suffix = '.php';
			$coller_path = APP_PATH.implode('/',$path).$controller_suffix;
			if(!is_file($coller_path)){
				exit('Controller file('.$coller_path.') does not exist');
			}

			autoloader::add_param(dirname($coller_path).'/');	//应用目录->当前控制器目录

		//step2、初始化Controller  并执行

			require_once($coller_path);
			trace_add('load_file',$coller_path);	//记录加载文件

			$controller_name = $this->uri->controller_name.'Controller';
			$controller_object = new $controller_name($this);
			$controller_object->_execute();
			$this->controller_object = $controller_object;

	}


	public function __destruct (){
		unset($this->controller_object);
		unset($this->config);
		unset($this->uri);
		unset($this->load);
		if(TRACE) trace::finish();
	}


	/*
	 * 	获取对象
	 */
	public static function  get_instance(){
		return (self::$instance);
	}



	
	

}





