<?php

/**
 * 	有效位置：
 * 			/[PACKAGE_DIR]/Config/
 * 			/[APP_PATH]/Config/
 *
 */


return array(

	/*
	 * url模式
	 */
	# localhost/Demo/Index/Index/two?aa=11
	'protocol' => 'PATH_INFO',

	# localhost/index.php?m=Demo&d=Index&c=Index&f=two
	#'protocol' => 'QUERY_STRING',

	//protocol = 'REQUEST_URI'

	//默认模块名
	'module_default' => 'Demo',
	//默认目录名
	'directory_default' => 'Index',
	//默认控制器名
	'controller_default' => 'Index',
	//默认操作名
	'function_default' => 'index',

	'module_trigger'  =>   'm',
	'directory_trigger'   =>   'd',
	'controller_trigger'  =>   'c',
	'function_trigger'    =>   'f',


	'IS_BASE_URL'	=>	false,	//生成URL是否带基础地址
	'URL_SUFFIX'=>'.html',



	'module_test'	=>	array(
		'is_group'	=>	false,
	),
	'module_demo'	=>	array(
		'is_group'	=>	true,
	),

);


