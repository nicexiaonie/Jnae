<?php
/*
 *
 *
 */
class template {

	public $assign;

	public $temp_driver;

	public function  __construct(){

		return $this;

	}

	public function assign($key=null,$value=null){
		$assign = $this->assign;
		$assign[$key] = $value;
		$this->assign = $assign;
	}

	public function display($value = null){
		static $template_driver;
		if(empty($template_driver)) $template_driver = new template_driver($this);

		if(!empty($this->assign)){
			foreach($this->assign as $k=>$v){
				$template_driver->assign($k,$v);
			}

		}
		$template_driver -> display($value);

	}


}