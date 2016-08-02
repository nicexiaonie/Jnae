<?php
namespace Core;

use Core\Config;
use Library\Redis;


class Token  {

	protected static  $expire = 600;	//Token有效时间 S

	public static function _initialize(){

	}

	/**
	 * 	注册Token
	 *
	 * 	param string $disturb	干扰字符
	 */
	public static function register($disturb = ''){
		Loaders::helper('/string');
		$token = '';
		$expire = self::$expire;
		$token = md5(keyGen().$disturb);
		$token = md5($token.$expire);

		$redis = Redis::Instance();

		$key = 'Token_'.$token;

			$redis->set($key,$expire);
			$redis->expire($key,$expire);

		return $token;
	}

	public static function verify($token){
		$redis = Redis::Instance();
		$key = 'Token_'.$token;

		$is = $redis->get($key);
		if($is === self::$expire){
			return true;
		}else{
			return false;
		}
	}




}

?>
