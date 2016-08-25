<?php
/**
 * 	关于钩子的配置管理
 *		有效位置：
 * 			/[PACKAGE_DIR]/Config/
 * 			/[APP_PATH]/Config/
 * 			/[APP_PATH]/[模块]/Config/
 *
 * 		app_init    应用初始化标签位
 * 		app_begin	应用开始标签位
 *		action_begin    控制器开始标签位
 * 		view_begin  视图输出开始标签位
 * 		view_end    视图输出结束标签位
 * 		action_end  控制器结束标签位
 * 		app_end         应用结束标签位
 */
return array(

	# 是否开启钩子
	'hook'	=>	true,

	# 应用开始标签位
	'app_begin'	=>	array(
		//'Hook\\Common'
	),



);