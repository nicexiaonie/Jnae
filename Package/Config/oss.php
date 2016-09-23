<?php

/**
 * 	有效位置：
 * 			/[PACKAGE_DIR]/Config/
 * 			/[APP_PATH]/Config/
 * 			/[APP_PATH]/[模块]/Config/
 *
 */

return array(

	//默认驱动
	'DEFAULT'	=>	'OSS_1',
	//设置请求超时时间，单位秒，默认是5184000秒, 这里建议 不要设置太小，如果上传文件很大，消耗的时间会比较长
	'TIMEOUT'	=>	3600,
	//设置连接超时时间，单位秒，默认是10秒
	'CONNECTTIMEOUT'	=>	10,


	'OSS_1'	=>	array(
		 'accessKeyId'	=>	"@@@@@@@@@@@@@@",
		 'accessKeySecret'	=>	"@@@@@@@@@@@@",
		 'endpoint'	=>	"@@@@@@@@@@@@@@",
		 'bucket'	=>	'@@@@@@@@@@@@@@@@',
	),

);


