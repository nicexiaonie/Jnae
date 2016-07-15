<?php
return array(

	//默认驱动
	'DEFAULT'	=>	'OSS_1',
	//设置请求超时时间，单位秒，默认是5184000秒, 这里建议 不要设置太小，如果上传文件很大，消耗的时间会比较长
	'TIMEOUT'	=>	3600,
	//设置连接超时时间，单位秒，默认是10秒
	'CONNECTTIMEOUT'	=>	10,


	'OSS_1'	=>	array(
		 'accessKeyId'	=>	"WuuIsDxRw2xTcLfN",
		 'accessKeySecret'	=>	"JSFVmbDw0WLKOga3SYUKqd0ElMGSjK",
		 'endpoint'	=>	"aliyun.oss.xiaonie365.net",
		 'bucket'	=>	'xiaonie365',
	),
	'OSS_2'	=>	array(
		'accessKeyId'	=>	"WuuIsDxRw2xTcLfN",
		'accessKeySecret'	=>	"JSFVmbDw0WLKOga3SYUKqd0ElMGSjK",
		'endpoint'	=>	"xiaonie365-a.oss-cn-beijing.aliyuncs.com",
		'bucket'	=>	'xiaonie365-a',
	),

);


