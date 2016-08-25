<?php
/**
 *	app_init    应用初始化标签位
 * 	app_begin	应用开始标签位
 *	action_begin    控制器开始标签位
 * 	view_begin  视图输出开始标签位
 * 	view_end    视图输出结束标签位
 * 	action_end  控制器结束标签位
 * 	app_end         应用结束标签位
 *
 *
 *
 */

namespace Core;

use Core\Config;


class Hook {

	/**
	 * 是否启用
	 * @var bool
	 */
	static $hook_start;

	/**
	 * 标签位
	 * @var bool
	 */
	static $tags;

	public static function _initialize(){

		Config::load('hook');
		$config = Config::get('hook/');
		if($config['hook']){
			self::$hook_start = $config['hook'];unset($config['hook']);
			self::$tags = $config;
		}
	}


	public static function listen($tag,&$params = null){
		if(!self::$hook_start) return ;
		$tags = array();
		if(!empty(self::$tags[$tag])) $tags = self::$tags[$tag];
		foreach($tags as $name){
			$result	=	self::exec($name, $tag,$params);

			if(false === $result) {
				// 如果返回false 则中断插件执行
				return ;
			}
		}
		return ;
	}

	/**
	 * 执行某个插件
	 * @param string $name 插件名称
	 * @param string $tag 钩子标签位
	 * @param Mixed $params 传入的参数
	 * @return void
	 */
	static public function exec($name, $tag,&$params=NULL) {
		$class = '\\'.$name;

		$Object = new $class();

		return $Object->run($params);
	}

}