<?php
return array(


	//自动渲染
	'view_auto' => false,
	//加载文件的后缀  此处
	'suffix' => '.phpa',
	//控制器后缀
	'controller_suffix' => 'Controller.php',



	//模版驱动
	'template_driver' => 'Smarty',
	//'template_driver' => 'Twig',
	//模版主题  默认无
	'template_default' => 'Default',
	//魔版路径 默认在当前模块下
	'template_path' => '',
	//模版文件后缀
	'template_suffix' => '.html',
	//是否开启页面静态缓存
	'template_cache' => false,
	//静态缓存周期
	'template_cache_lifetime' => 110,
	//是否每次都进行重新编译
	'force_compile' => true,
	'left_delimiter' => '{',
	'right_delimiter' => '}',
	/*
	 * 模版文件名大小写  默认由URL决定
	 * 	ucwords	首字母大写
	 * 	strtolower	全部小写
	 *
	 */
	'template_unlimit'	=>	'strtolower',


	'VAR_AUTO_STRING' =>	true,	//自动强制转字符串
	/*
	 * 	输入过滤函数
	 * 	htmlspecialchars
	 * 	strip_tags
	 *
	 */
	'DEFAULT_FILTER'	=>	'htmlspecialchars',





	/*
	 * 	Session
	 */
	'session'	=>	array(
		'auto_start'	=>	true,	//自动开启
		/*
		 * session存储方式:
		 * 		null	系统默认
		 * 		Redis
		 *
		 */
		'type'	=>	'Redis',
		'prefix'	=>	'jnae',
		'expire'	=>	0,	//session有效时间
		//'id'	=>	'243567',	//session_id	直接设置sessionid
		//已变量的方式获取session_id 与$_REQUEST中 var_session_id做为键
		//'var_session_id'	=>	'5',
		/*
		 *用在 cookie 或者 URL 中的会话名称， 例如：PHPSESSID。
		 * 只能使用字母和数字作为会话名称，建议尽可能的短一些，
		 * 并且是望文知意的名字（对于启用了 cookie 警告的用户来说，方便其判断是否要允许此 cookie）。
		 * 如果指定了 name 参数， 那么当前会话也会使用指定值作为名称。
		 */
		'name'	=>	'PHPSESSID',
		//'path'	=>	'/alidata/www/session/',	//session路径
		/*
		 * sessionid在客户端采用的存储方式，置1代表使用cookie记录客户端的sessionid，
		 * 同时，$_COOKIE变量里才会有$_COOKIE[‘PHPSESSIONID’]这个元素存在
		 */
		//'use_cookies'	=>	1,
		/*
		 * 它是session在客户端的缓存方式，有nocache,private,private_no_expire,public这几种
		 */
		//'cache_limiter'	=>	'',
		//'cache_expire'	=>	180,	//返回当前缓存的到期时间



	),


	/*
	 * Cookie
	 */
	'cookie_expire'	=>	0,
	'cookie_prefix'	=>	'jnae',
	//表明,cookie只能传输一个安全的HTTPS连接的客户端。设置为TRUE时,cookie只能设置是否存在一个安全的连接。在服务器端,在程序员只发送这种cookie安全连接(如对$ _SERVER[“HTTPS”])。
	'cookie_secure'	=>	false,	//cookie安全   开启则CSRF失效
	//cookie_httponly 当真正的cookie将只能通过HTTP协议。这意味着cookie不会访问的脚本语言,比如JavaScript。这个设置可以有效地帮助降低身份盗窃XSS攻击(虽然不是所有浏览器都支持的)。在PHP 5.2.0补充道。真或假
	'cookie_httponly'	=>	false,
	//cookie的领域是可用的。使cookie上可用的所有子域example.com(包括example.com本身),那么你将它设置为“.example.com”。尽管有些浏览器会接受没有最初的cookie。,包括»RFC 2109需要它。设置域“www.example.com”或“.www.example.com”将使cookie仅可在www子域名。
	'cookie_domain'	=>	false,	//
	//服务器上的路径在cookie上可用。如果设置为‘/’,cookie将整个域内是可用的。如果设置为/ foo /,cookie只会在/ foo /目录和所有子目录/ foo / bar /等领域。默认值为当前目录的cookie被设定在
	'cookie_path'	=>	false,	//cookie安全   开启则CSRF失效


	/*
	 * 	CSRF跨站请求伪造配置
	 *
	 *
	 */
	'csrf_protection'	=>	false,	//是否开启	//会增加开销
	'csrf_expire'	=>	'122',		//csrf的有效时间
	'csrf_token_name'	=>	'123',	//POST的key
	'csrf_cookie_name'	=>	'124',	//csrf前缀	csrf的KEY由cookie前缀加csrf前缀
	'csrf_exclude_uris'	=>	array(),	//忽略的uri




	/*
	 * 	分页
	 */
	'VAR_PAGE'	=>	'p',	//分页参数名称


	/*
	 * 	缓存相关配置
	 */
	'cache'	=>	array(
		'cache_dir'	=>	'Data/',	//相对于RUNTIME目录
		'type'	=>	'File',		//默认缓存类型：File Redis
	),




);




