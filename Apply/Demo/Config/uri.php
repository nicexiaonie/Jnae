<?php
return array(
	//url模式
	'protocol' => 'PATH_INFO',
	//protocol = 'QUERY_STRING'
	//protocol = 'REQUEST_URI'

	//默认模块名
	'module_default' => 'demo',
	//默认目录名
	'directory_default' => 'index',
	//默认控制器名
	'controller_default' => 'index',
	//默认操作名
	'function_default' => 'index',

	'module_trigger'  =>   'm',
	'directory_trigger'   =>   'd',
	'controller_trigger'  =>   'c',
	'function_trigger'    =>   'f',

	'module:index'	=>	array(
		'is_group'	=>	true
	),
	'module:demo'	=>	array(
		'is_group'	=>	false
	),

);


