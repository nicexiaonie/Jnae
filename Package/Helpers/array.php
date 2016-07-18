<?php

/**
 * array_depth
 *
 * 获取数组深度
 *
 * @param	array
 * @return	int
 */
function array_depth($array) {
	if(!is_array($array)) return 0;
	$max_depth = 1;
	foreach ($array as $value) {
		if (is_array($value)) {
			$depth = array_depth($value) + 1;

			if ($depth > $max_depth) {
				$max_depth = $depth;
			}
		}
	}
	return $max_depth;
}


if ( ! function_exists('element'))
{
	/**
	 * Element
	 *
	 * 检查指定的key是否存在于数组中  默认null
	 *
	 * @param	string
	 * @param	array
	 * @param	mixed
	 * @return	mixed	depends on what the array contains
	 */
	function element($item, array $array, $default = NULL)
	{
		return array_key_exists($item, $array) ? $array[$item] : $default;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('random_element'))
{
	/**
	 * 随机返回一个键   如果值为字符串则直接返回
	 *
	 * @param	array
	 * @return	mixed	depends on what the array contains
	 */
	function random_element($array)
	{
		return is_array($array) ? $array[array_rand($array)] : $array;
	}
}

if ( ! function_exists('elements'))
{
	/**
	 * Elements
	 *
	 * 从一个数组中获取一个或者多个值
	 *
	 * @param	array
	 * @param	array
	 * @param	mixed
	 * @return	mixed	depends on what the array contains
	 */
	function elements($items, array $array, $default = NULL)
	{
		$return = array();

		is_array($items) OR $items = array($items);

		foreach ($items as $item)
		{
			$return[$item] = array_key_exists($item, $array) ? $array[$item] : $default;
		}

		return $return;
	}
}

if ( ! function_exists('array_change_key_cases'))
{
	/**
	 * 	改变数组中键的大小写
	 *	$items	数组
	 * 	$case 	bool
	 * 		true	大写
	 * 		false	小写
	 */
	function array_change_key_cases($items, $case = false)
	{
		$return = array();
		foreach ($items as $key=>$item)
			if(is_array($item))
				$return[$key] = array_change_key_cases($item,$case);
			else
				$return[$key] = ($item);
		$return = array_change_key_case($return,$case);
		return $return;
	}
}

if ( ! function_exists('array_filters'))
{
	/**
	 * 	数组去空
	 *	$items	array
	 */
	function array_filters($items)
	{
		array_walk($items,function(&$value){
			if(is_array($value))
				$value = array_filters($value);
		});
		$items = array_filter($items);
		return $items;
	}
}


