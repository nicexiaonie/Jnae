<?php



class config  {
	public $_config_paths = array();

	public $config = array();
	private static $_instance;
	private $_suffix = '.ini';

	/*
	public function __construct(){

		echo 11;
	}
	*/
	public function _initialize(){

		$this->_config_paths[]= PACKAGE_DIR.'config/';	//配置目录
	}


	/*
     * @introduce:  根据医师类型 形成查询条件
     * @param1：	需要加载的文件
	 * @param2：	第二个参数设置为 TRUE ，这可以使每个配置文件的内容存储在一个单独的数组中，数组的索引就是配置文件的文件名
	 * @param3：	允许屏蔽当配置文件不存在时产生的错误信息:
     */
	static $file_path_tmp = array();
	public function load( $file = '' , $fail_gracefully = false){

		//show($this->_config_paths);
		//屏蔽重复加载
			if(!empty(self::$file_path_tmp[$file])) return true;
			self::$file_path_tmp[$file] = 1;

		$loaded = FALSE;
		$file= trim($file,'/');
		$config_name = explode('/',$file);
		$config_name = array_pop($config_name);	//获取配置文件名
		//step1、解析配置文件
			$config = array();
			foreach($this->_config_paths as $v){
				$file_path = rtrim($v,'/').'/'.$file.$this->_suffix;
				if(is_file($file_path)){
					$config[] = parse_ini_file($file_path,true);
					trace_add('load_config',$file_path);	//记录加载文件
					$loaded = true;
				}
			}
//show($config);
		$result = array();
		foreach($config as $k => $v){
			$v = array_filter($v);	//去空
			if(!empty($v)) $result = array_merge($result,$v);
		}

		//是否拆分配置
		//$config = $this->config;
		//if($use_sections){
			$this->$config_name = $result;
		//}else{
			//$config[$config_name] = array_merge($this->config,$result);
		//}
		//$this->config=$config;

		if($loaded){
			return true;
		}
		if($fail_gracefully){
			return false;
		}

		show_error('Configuration file does not exist: '.$file);
		
		
	}


	public function item($key = '' , $name = ''){

		empty($name) ? $config = $this->config : $config = $this->$name;
		if(empty($key)) return empty($config) ? false : $config;
		$key = explode('/',$key);

		foreach($key as $v){
			$config = $config[$v];
		}
		if(empty($config)){
			return false;
		}else{
			return $config;
		}
		return $config;
	}

}
