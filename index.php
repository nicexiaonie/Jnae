<?php


//设置项目根目录
define('ROOT',__DIR__.'/');


define('DEBUG',true);

//加载
require('./Package/Start.php');


$app = new app();
$app -> run();







