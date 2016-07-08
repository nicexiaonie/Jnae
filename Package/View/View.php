<?php
/*
 *	此类是操作模版引擎临时类
 * 		你所对模版操作的数据临时存放在此类中，
 * 		在dispaly时进行加载模版驱动 处理之前操作的数据
 *
 */
namespace View;



class View {

	public static $assign;

	public $temp_driver;

	public function  __construct(){

		return $this;

	}

	public static  function assign($key=null,$value=null){
		$assign = self::$assign;
		$assign[$key] = $value;
		self::$assign = $assign;
	}

	public static function display($value = null){

		static $template_driver;

		if(!class_exists('template_driver',false)) load(PACKAGE_DIR.'View/Template_driver.php');

		if(empty($template_driver)) $template_driver = new Template_driver(self);

		if(!empty(self::$assign)){
			foreach(self::$assign as $k=>$v){
				$template_driver->assign($k,$v);
			}
		}
		$template_driver -> display($value);

	}


}

?>