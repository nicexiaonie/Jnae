<?php

//包目录
define('PACKAGE_DIR',__DIR__.'/');
//运行目录
define('RUNTIME_DIR',ROOT.'Runtime/');

//应用目录
define('APP_PATH',ROOT.'Apply/');

require_once(PACKAGE_DIR.'Helpers/common.php');	//加载公共函数库
trace_add('load_file',PACKAGE_DIR.'Helpers/common.php');	//记录加载文件

load(PACKAGE_DIR.'Core/Autoloader.php');


load(PACKAGE_DIR.'Core/App.php');



















