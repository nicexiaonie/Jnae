<?php
/**
 * array_depth	获取数组深度
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

/**
 * 	array_urldecode
 *	解码数组
 */
function array_urldecode(&$array){
	if(is_array($array)){
		foreach($array as $k=>$item){
			$array[$k] = array_urldecode($item);
		}
	}else{
		$array = urldecode($array);
	}
	return $array;
}

/**
 * 	改变数组值
 *	该函数返回数组。
 * 	该数组是将 subject 中全部的 search 都被 change 替换之后的结果。
 */
function array_change($search, $change,  $subject ){
	foreach($subject as $k=>$v){
		if(is_array($v)){
			$subject[$k] = array_change($search,$change,$v);
		}else{
			if($v === $search) $subject[$k] = $change;
		}
	}
	return $subject;
}

/**
 * array_intval	数组转int
 * @param	array
 * @return	int
 */
function array_intval(&$value){
	if(is_array($value)){
		$result = array();
		foreach($value as $k => $v){
			$result[$k] = intval($v);
		}
		return $result;
	}else{
		return intval($value);
	}
}

/**
 * array_strval	数组转string
 * @param	array
 * @return	string
 */
function array_strval(&$value){
	if(is_array($value)){
		$result = array();
		foreach($value as $k => $v){
			$result[$k] = strval($v);
		}
		return $result;
	}else{
		return strval($value);
	}
}

/**
 * array_column_count	数组计算
 * 	统计array二维数组中的[cloumn]键中的数据
 * @param	array
 * @param	string
 * @return	array
 */
function array_column_count($array,$cloumn){
	$result = array();
	foreach($array as $k=>$v){
		$result[$v[$cloumn]] ++;
	}
	return $result;
}

/**
 * array_count	一维数组统计
 * @param	array
 * @param	string
 * @return	array
 */
function array_count($array,$value){
	$result = 0;
	foreach($array as $k=>$v){
		if($v === $value) $result++;
	}
	return $result;
}

/**
 * array_repetition	重复一个数组
 * @param array "要重复的数组"
 * @param int '重复次数'
 * @return	array
 */
function array_repetition($array,$int = 1){
	$result = $array;
	for($i=0;$i<$int;$i++){
		foreach($array as $k=>$v){
			$result[] = $v;
		}
	}
	return $result;
}

/**
 * array_fetch	获取数组值
 * @param array '条件'
 * @param int '重复此时'
 * @param array '操作数组'
 * @return	array
 */
function array_fetch($search = array(),$key = '',$object = array()){
	foreach($object as $k=>$v){
		foreach($search as $search_k=>$search_v){
			if($v[$search_k] != $search_v){
				unset($object[$k]);
				break;
			}
		}
	}
	return empty($key) ? $object : array_column($object,$key);
}

/**
 * Element
 *
 * 检查指定的key是否存在于数组中  默认null
 *	存在则返回值，不存在则默认返回$default
 * @param	string
 * @param	array
 * @param	mixed
 * @return	mixed	depends on what the array contains
 */
function element($item, array $array, $default = NULL)
{
		return array_key_exists($item, $array) ? $array[$item] : $default;
}

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


