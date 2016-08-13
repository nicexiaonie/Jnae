<?php

namespace Library;

use Core\Config;

class Redis{
	protected static $handler = null;

	protected static $config  = [
		//'host'         => '127.0.0.1', // redis主机
		//'port'         => 6379, // redis端口
		//'password'     => '123456', // 密码
		//'expire'       => 3600, // 有效期(秒)
		//'timeout'      => 0, // 超时时间(秒)
		//'persistent'   => true, // 是否长连接
		//'session_name' => '', // sessionkey前缀
	];
	protected static $config_bak;

	/**
	 * 	获取Redis实例
	 *
	 */
	public static function Instance($Sign = null){

		//step1、读取配置，根据配置生成Sign
			if( empty(self::$config) ){
				if(empty(self::$config_bak)){
					Config::load('redis');
					self::$config_bak = Config::get('redis/');
				}
				empty($Sign) ?
					$key = self::$config_bak['DEFAULT_DB'] :
					$key = $Sign;
				self::$config = self::$config_bak[$key];
			}
			$config = self::$config;
			$key_code = md5(json_encode($config));

		//step2、如果实例存在则直接返回
			if(self::$handler[$key_code])
				return self::$handler[$key_code];

		//step3、实例化Redis，并连接
			$redis = new \Redis;
			$func = $config['persistent'] ? 'pconnect' : 'connect';
			$result = $redis->$func($config['host'], $config['port'], $config['timeout']);

			if(!$result){
				show_error('Redis connection fails<br>');
			}
			if ('' != $config['password'])
				$redis->auth($config['password']);

			self::$handler[$key_code] = $redis;
			return $redis;

	}

}
