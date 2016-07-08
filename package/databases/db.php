<?php
/*
 *
 *
 */
abstract class db {

	public $_db_driver = null;	//驱动


	/*
	 *
	 * 加载数据库驱动
	 */
	public function __construct($db = null){
		$this->_get_instance = get_instance();	//引入app

		//step1、获取数据库配置
			if(empty($db)){
				//show('load config');
				$default_db = $this->_get_instance->config->item('DEFAULT_DB','db');
			}else{
				$default_db = $db;
			}
			$db_config = $this->_get_instance->config->item($default_db,'db');

		if($db_config){
			//step2、加载驱动文件
			$drivers_file = __DIR__.'/drivers/'.$db_config['driver'].'/'.$db_config['driver'].'_driver.php';
			if(is_file($drivers_file)){
				trace_add('load_file',$drivers_file);	//记录加载文件
				require_once($drivers_file);
			}else
				show_error("错误：{$drivers_file} 驱动不存在!");

			//step3、实例化驱动  并且连接数据库
			$class_name = $db_config['driver'].'_driver';
			$this->_db_driver = new $class_name($db_config);
			$this->_db_driver = $this->_db_driver->_connection();
		}


		//return $this->_db_driver;

	}


	/*
	 *	自动调用方法：
	 * 		默认调用对应驱动扩展方法
	 * 		调用对应驱动方法
	 * 		调用控制器方法
	 *
	 *
	 */
	public function __call($key,$value){

		//step1、决定操作哪个对象  优先级：1 驱动 2 db插件本身 3 app超级对象
			$object = (object)array();
			if(method_exists($this->_db_driver,$key))
				$object = $this->_db_driver;
			else if(method_exists($this->_db_driver->db,$key))
				$object = $this->_db_driver->db;
			else if(method_exists($this->_get_instance,$key))
				$object = $this->_get_instance;
			else {
				throw new Exception("错误：{$key} 方法不存在！");
			}

		//整理调用发法使用的参数
		list($value1,$value2,$value3,$value4) = $value;

		//step2、调用对象方法  增加前置操作  后置操作
			$before_function_name = '_before_'.$key;
			if(method_exists($object,$before_function_name))
				$object->$before_function_name();

			if(method_exists($object,$key))
				$result = $object->$key(
					$value1,
					$value2,
					$value3
				);

			$after_function_name = '_after_'.$key;
			if(method_exists($object,$after_function_name))
				$object->$after_function_name();

		//针对驱动返回this返回的是驱动本身问题 所做的修改
		if($result === 'this') return $this;

		if($key != '_sql') trace_add('sql',$this->_sql());	//记录SQL

		return $result;
	}


}