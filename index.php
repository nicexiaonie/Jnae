<?php



//设置项目根目录
define('ROOT',__DIR__.'/');

define('TRACE',true);
define('DEBUG',true);
//define('DEBUG',false);

//加载
require('./Package/Start.php');


$app = new app();
$app -> run();







