<?php

//包目录
define('PACKAGE_DIR',__DIR__.'/');
//运行目录
define('RUNTIME_DIR',ROOT.'runtime/');

//应用目录
define('APP_PATH',ROOT.'apply/');


require_once(PACKAGE_DIR.'helpers/common.php');
trace_add('load_file',PACKAGE_DIR.'helpers/common.php');	//记录加载文件

require(PACKAGE_DIR.'core/app.php');














