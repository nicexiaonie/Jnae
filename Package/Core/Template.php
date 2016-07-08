<?php
/*
 *	此类是操作模版引擎临时类
 * 		你所对模版操作的数据临时存放在此类中，
 * 		在dispaly时进行加载模版驱动 处理之前操作的数据
 *
 */
namespace Core;

use Template\Template_driver;

class Template {

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

		if(!class_exists('template_driver',false)) load(PACKAGE_DIR.'Template/Template_driver.php');

		if(empty($template_driver)) $template_driver = new Template_driver($this);

		if(!empty($this->assign)){
			foreach($this->assign as $k=>$v){
				$template_driver->assign($k,$v);
			}
		}
		$template_driver -> display($value);

	}


}

?>