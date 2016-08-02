<?php
//php aes加密类
namespace Library\Crypt;
//使用方法
//$aes = new AESMcrypt($bit = 16, $key = '235467rsegyuk', $iv = '234', $mode = 'ecb');
class Aes {

	/**
	 * 干扰串
	 * @var string
	 * @access private
	 */
	private static $key = 'Jnae';

	/**
	 * 位数
	 * @var int 128、192、256
	 * @access private
	 */
	private static $bit = 128;


	/**
	 * 算法的名称字符串。
	 * 由bit得到
	 * @var
	 * @access private
	 */
	private static $cipher;


	private static $iv = '123456';

	/**
	 * 算法的名称字符串。
	 * @var string
	 * @access private
	 */
	private static $mode = 'ecb';

	/**
	 * 加密参数初始化
	 * 默认返回 1970-01-01 11:30:45 格式
	 * @access public
	 * @param string $bit  位数
	 * @param string $key  干扰
	 * @param string $iv  iv
	 * @param string $mode  ecb、cbc、cfb、ofb、nofb
	 * @return void
	 */
	public static function init($bit = 128, $key = 'Jnae', $iv='123456', $mode='ecb') {
		self::$bit = $bit;
		self::$key = $key;
		self::$mode = $mode;
		switch(self::$bit) {
			case 192:self::$cipher = MCRYPT_RIJNDAEL_192; break;
			case 256:self::$cipher = MCRYPT_RIJNDAEL_256; break;
			default: self::$cipher = MCRYPT_RIJNDAEL_128;
		}
		switch(self::$mode) {
			case 'ecb':self::$mode = MCRYPT_MODE_ECB; break;
			case 'cfb':self::$mode = MCRYPT_MODE_CFB; break;
			case 'ofb':self::$mode = MCRYPT_MODE_OFB; break;
			case 'nofb':self::$mode = MCRYPT_MODE_NOFB; break;
			default: self::$mode = MCRYPT_MODE_CBC;
		}
		self::$iv = mcrypt_create_iv(mcrypt_get_iv_size(self::$cipher, self::$mode), self::$iv);
	}

	/**
	 * 加密
	 * @access public
	 * @param string $data 要加密文本
	 * @return string 密文
	 */
	public static function encrypt($data) {
		$data = trim(base64_encode(mcrypt_encrypt(
			self::$cipher,
			self::$key,
			$data,
			self::$mode,
			self::$iv
		)));
		return $data;
	}

	/**
	 * 解密
	 * @access public
	 * @param string $data 要解密的密文
	 * @return string
	 */
	public static function decrypt($data) {
		$data = mcrypt_decrypt(
			self::$cipher,
			self::$key,
			base64_decode($data),
			self::$mode,
			self::$iv
		);
		return $data;
	}
}









