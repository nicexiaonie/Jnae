<?php
/*
 * 	模版驱动类
 *
 * 		当进行display时，模版类会自动加载此类
 *
 * 		对模版进行检查 加载引擎驱动
 *
 * 		此类的方法为公共方法，如果方法不存在时  则调用对应引擎的驱动中的方法
 * 		（对应的引擎驱动的方法按需编写，如果不存在则直接操作引擎提供的方法，直至完成操作）
 *
 */
namespace View;

use \Core\Config;
require_once __DIR__.'/Smarty/Smarty.class.php';

class Smarty{

	/**
	 *	驱动入口
	 */
	public function run(){
		//$smarty_file = __DIR__.'/Smarty/Smarty.class.php';

		$smarty = new \Smarty();

		//step1、设置参数
			$smarty->template_dir = View::$temp_dir;	//设置模版目录
			$smarty->compile_dir = View::$compile_dir;	//设置编译目录
			$smarty->cache_dir  = View::$cache_dir;	//设置缓存目录
			$smarty->caching = Config::get('template_cache');   //开启缓存
			$smarty->cache_lifetime = Config::get('template_cache_lifetime');  //缓存存活时间（秒）
			$smarty->compile_check = false;	//编译检查变量，如果开启此变量，smarty会检查模板文件是否改动过，
			$smarty->force_compile = Config::get('force_compile');	//强制重新编译
			$smarty->left_delimiter = Config::get('left_delimiter');
			$smarty->right_delimiter = Config::get('right_delimiter');

		//step2、注册函数
			$function = Config::get('view/register_function');
			if(!empty($function))
				foreach($function as $k=>$v){
					$smarty->register_function($k,$v);	//注册函数
				}




		$this->smarty = $smarty;

		return $this;
	}

	public function display($filename = null){
		if(empty($filename))
			$filename = View::$filename;

		//step1、分配style配置变量
			$config = Config::get('view/variable');
			foreach($config as $k=>$v){
				$this->smarty->assign($k,$v);
			}
		//step2、模版文件是否存在
			$temp_dir = View::$temp_dir;
			if(!is_file($temp_dir.$filename)){
				show_error('Template file('.$temp_dir.$filename.') does not exist');
			}
		$this->smarty->display($filename);
	}

	public function __call($key,$value){
		list($v1,$v2) = $value;
		return $this->smarty->$key($v1,$v2);
	}





}