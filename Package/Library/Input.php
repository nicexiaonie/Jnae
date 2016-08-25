<?php

namespace Library;

use \Library\Security;

class Input {

	/**
	 * 获取get参数
	 * @param string $index
	 * @param string $default
	 * @param bool	 $xss_clean
	 */
	static function get($index = false,$default = false, $xss_clean = false){

		$input = new Input();

		return $input->_fetch_from_array($_GET,$index,$default,$xss_clean);
	}

	/**
	 * 获取post参数
	 * @param string $index
	 * @param string $default
	 * @param bool	 $xss_clean
	 */
	static function post($index = false,$default = false, $xss_clean = false){

		$input = new Input();
		return $input->_fetch_from_array($_POST,$index,$default,$xss_clean);
	}

	/**
	 * 获取request参数
	 * @param string $index
	 * @param string $default
	 * @param bool	 $xss_clean
	 */
	static function request($index = false,$default = false, $xss_clean = false){
		$input = new Input();
		return $input->_fetch_from_array($_REQUEST,$index,$default,$xss_clean);
	}



	private function _fetch_from_array(& $param,$index,$default,$xss_clean){

		//处理获取多个值
		if (is_array($index)){
			$output = array();
			foreach ($index as $key)
			{
				$output[$key] = self::_fetch_from_array($param, $key,$default, $xss_clean);
			}
			return $output;
		}
		if(empty($index)){
			$value = $param;
		}elseif(isset($param[$index])){
			$value = $param[$index];
		}else{
			return $default;
		}

		if($xss_clean === true){
			$security = new Security();
			$value = $security->xss_clean($value);
		}

		return $value;
	}
}
