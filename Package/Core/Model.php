<?php

namespace Core;
use \Databases\Db;
use \Library\Input;
use \Library\Validate;


class Model extends Db{

	private $error = '';

	public function __construct($db = null){
		Config::load('db');
		parent::__construct($db);
		if(method_exists($this,'_initialize'))
			$this->_initialize();

	}

	/**
	 * 	成员变量不存在调用此方法：
	 * 		调用APP对象
	 */
	public function __get($name){
		return ($this->_get_instance->$name);
	}

	/**
	 *	当方法不存在是调用：
	 * 		默认调用对应驱动扩展方法
	 * 		调用对应驱动方法
	 * 		调用控制器方法
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

	/**
	 * 获取表名 （小写）
	 * 	根据实例化的模型得到数据表名称
	 */
	private function _getClassName(){
		$className = get_class($this);
		$className = explode('\\',$className);
		$name = array_pop($className);
		$name = preg_replace('/Model$/','',$name);
		if(!empty($name)) $this->_db_driver->_set_table(strtolower($name));
	}

	/**
	 * 数据创建
	 * 	自动对数据进行过滤和验证
	 */
	public function create($data = null,$scene = 0){
		Loaders::helper('/array');

		$depth = array_depth($data);
		if($depth > 1){
			foreach($data as $k=>$v){
				$data[$k] = $this->create($v,$scene);
				if($data[$k] == false) return false;
			}
			return $data;
		}

		if(empty($data)) $data = Input::post();
		//过滤字段
		if(!empty($this->_fields) || is_array($this->_fields)){
			foreach($data as $k=>&$v){
				if(!in_array($k,$this->_fields)){
					unset($data[$k]);
				}
			}
		}

		//数据合法验证
		if(!empty($this->_validate) || is_array($this->_validate)){
			//获取验证实例
			$validata = Validate::make($this->_validate);
			if($validata->check($data,null,$scene) !== true){
				$this->error = $validata -> error();
				return false;
			}
		}

		return $data;
 	}

	/**
	 * 添加数据
	 *  重写驱动add方法，增加对数据进行过滤验证操作
	 *
	 */
	public function add($data = null){
		if(empty($this->_db_driver)) show_error('This method(add) does not exist!');
		$this->_getClassName();
		$data = $this->create($data,1);	//数据验证
		if($data == false) return false;
		$result = $this->_db_driver->add($data);
		if(!$result ||( is_array($result) && empty(array_filter($result)))){
			$this->error = $this->_db_driver->error();
			return false;
		}
		return $result;
	}

	/**
	 * 更新数据
	 *  重写驱动save方法，增加对数据进行过滤验证操作
	 *
	 */
	public function save($data = null){
		if(empty($this->_db_driver)) show_error('This method(add) does not exist!');
		$this->_getClassName();
		$data = $this->create($data,2);
		if($data == false) return false;
		$result = $this->_db_driver->save($data);
		if(!$result){
			$this->error = $this->_db_driver->error();
			return false;
		}
		return $result;
	}

	/**
	 * 	获取错误
	 */
	public function getError(){
		return $this->error;
	}


}

?>