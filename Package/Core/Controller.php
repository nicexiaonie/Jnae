<?php
namespace Core;

use View\View;

abstract class Controller{


	private  $_get_instance;

	public function __construct(){
		$this->_get_instance = get_instance();

	}

	//执行
	public function _execute(){

		if(method_exists($this,'_initialize'))
			$this->_initialize();
		$function_name = $this->uri->function_name;

		if(!method_exists($this,$function_name))
			show_error('This function/action('.$function_name.') does not exist');

		$before_function_name = '_before_'.$function_name;
		if(method_exists($this,$before_function_name))
			$this->$before_function_name();

		$content = $this->$function_name();

		$after_function_name = '_after_'.$function_name;
		if(method_exists($this,$after_function_name))
			$this->$after_function_name();

		if(Config::get('view_auto')){
			View::display();
		}
	}

	/*
	 *
	 *
	 */
	public function __get($name){
		return ($this->_get_instance->$name);
	}

	/*
	 * 	当方法不存在是调用  优先级
	 * 		APP超级对象
	 *
	 */
	public function __call($key,$value){
		$object = (object)array();

		if(method_exists($this->_get_instance,$key))
			$object = $this->_get_instance;
		else
			show_error('This method('.$key.') does not exist');
		list($value1,$value2,$value3,$value4) = $value;
		if(method_exists($object,$key))
			$result = $object->$key(
				$value1,
				$value2
			);
		return $this;
	}

	public function  __destruct(){

	}



}