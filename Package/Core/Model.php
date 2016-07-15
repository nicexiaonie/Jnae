<?php

namespace Core;
use \Databases\Db;

class Model extends Db{
	public function __construct($db = null){

		parent::__construct($db);

	}

	/*
	 * 	成员变量不存在调用此方法：
	 * 		调用APP对象
	 */
	public function __get($name){
		return ($this->_get_instance->$name);
	}

	/*
	 *	当方法不存在是调用：
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

		//记录SQL
		if($key != '_sql') trace_add('sql',$this->_sql());

		return $result;
	}




}

?>