<?php
abstract class controller{
	private  $_get_instance = '';
	private $_template_object;


	public function __construct($app){
		$this->_get_instance = get_instance();
		$this->_template_object = new template();
	}

	//执行
	public function _execute(){
		if(method_exists($this,'_initialize'))
			$this->_initialize();
		$function_name = $this->uri->function_name;
		$before_function_name = '_before_'.$function_name;
		if(method_exists($this,$before_function_name))
			$this->$before_function_name();
		if(method_exists($this,$function_name))
			$this->$function_name();
		$after_function_name = '_after_'.$function_name;
		if(method_exists($this,$after_function_name))
			$this->$after_function_name();

		if($this->config->item('view_auto')){
			$this->display();
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
	 * 		模版驱动
	 * 		APP超级对象
	 *
	 */
	public function __call($key,$value){
		$object = (object)array();
		if(method_exists($this->_template_object,$key))
			$object = $this->_template_object;
		else if(method_exists($this->_get_instance,$key))
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



}