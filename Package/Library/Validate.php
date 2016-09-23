<?php

/*
 *
 *
 * array(
 * 		验证字段,
 * 			必须
 * 		验证规则,
 *			内置
 * 				require		必须
 * 				accepted	接受['1', 'on', 'yes']
 * 				date		是否是一个有效日期
 * 				alpha		只允许字母
 * 				alphaNum	只允许字母和数字
 * 				alphaDash	只允许字母、数字和下划线 破折号
 * 				activeUrl	是否为有效的网址
 * 				ip			是否为IP地址
 * 				url			是否为一个URL地址
 * 				float		是否为float
 * 				integer		是否为整形
 * 				email		是否为邮箱地址
 * 				array		是否为数组
 * 			同时作为附加规则的参数
 *
 * 		错误提示,
 * 			有默认值
 * 		[验证条件],
 * 			可选
 * 				0 存在字段就验证（默认）
					1 必须验证
					2 值不为空的时候验证
 * 		[附加规则],
 * 			可选
 *              in：验证是否在某个范围内，定义的验证规则必须是一个数组
 * 		[验证时间]
 * 			1 新增数据时候验证
			2 编辑数据时候验证
			0 全部情况下验证（默认,如果规则没此项则自动补0）
 * 	);
 *
 */

namespace Library;

class Validate{

	// 实例
	protected static $instance;

	// 验证类型别名
	protected $alias = [
		'>' => 'gt', '>=' => 'egt', '<' => 'lt', '<=' => 'elt', '=' => 'eq', 'same' => 'eq',
	];

	// 当前验证的规则
	protected $rules = array();

	// 验证提示信息
	protected $message = '';



	/**
	 * 架构函数
	 * @access public
	 * @param array $rules 验证规则
	 * @param array $message 验证提示信息
	 */
	public function __construct(array $rules = [])
	{
		if(!empty($rules)) $this->rules = array_merge($this->rules,$rules);
	}

	/**
	 * 实例化验证
	 * @access public
	 * @param array     $rules 验证规则
	 * @param array     $message 验证提示信息
	 * @return Validate
	 */
	public static function make($rules = [], $message = [])
	{
		if (is_null(self::$instance)) {
			self::$instance = new self($rules, $message);
		}
		return self::$instance;
	}

	/**
	 * 添加字段验证规则
	 * @access protected
	 * @param string|array  $name  字段名称或者规则数组
	 * @param mixed         $rule  验证规则
	 * @return Validate
	 *
	 * array(
	 * 		验证字段
	 * 		,验证规则,
	 * 		错误提示,
	 * 		[验证条件],
	 * 		[附加规则],
	 * 		[验证时间]
	 * 	);
	 */
	public function rule($rule = array())
	{
		$rules = $this->rules;
		$rules[] =  $rule;
		$this->rules = $rules;
		return $this;
	}

	/**
	 * 数据自动验证
	 * @access public
	 * @param array     $data  数据
	 * @param array     $rules  验证规则
	 * @param string    $scene 验证场景
	 * @return bool
	 */
	public function check($data,$rules=array(),$scene = 0){
		//读取规则
		if(empty($rules)) $rules = $this->rules;

		foreach($rules as $key => $item){
			//分析规则 并验证场景
			$item = $this->getRule($item,$scene);
			if(!$item) continue;

			// 获取数据 支持二维数组
			$value = $this->getDataValue($data, $item[0]);

			// 字段验证
			$result = $this->checkItem($value,$item);

			if($result !== true){
				if($result !== false){
					$this->message = '未知错误';
				}
				return false;
			}
		}
		return true;
	}
	/*
	 * 	获取字段值
	 */
	private function getDataValue($data,$field){
		if(is_array($data)){
			if(isset($data[$field])){
				return $data[$field];
			}
		}
		return null;
	}

	private function checkItem($value,$rule){
		$is_check = false;

		//step1、验证条件过滤
			if($rule[3] === 0)
				//存在字段就验证（默认）
				if($value !== null) $is_check = true;
			if($rule[3] === 1)
				//必须验证
				$is_check = true;
			if($rule[3] === 2)
				//值不为空的时候验证
				if(!empty($value)) $is_check = true;

			if(!$is_check) return true;

		//step2、验证规则
		 	if(is_string($rule[1])) {
				$result = $this->is($value,$rule[1]);
				if($result !== true) return false;
			}

		//step3、多规则验证
			$function_name = $rule[4];
			if(method_exists($this,$function_name))
				$result = $this->$function_name($value,$rule[1]);
				if($result !== true) return false;

		return true;
	}



	public function error(){
		return $this->message;
	}

	private function is($value,$rule){
		$result = true;
		switch ($rule) {
			case 'require':
				// 必须
				$result = !empty($value) || '0' == $value;
				break;
			case 'accepted':
				// 接受
				$result = in_array($value, ['1', 'on', 'yes']);
				break;
			case 'date':
				// 是否是一个有效日期
				$result = false !== strtotime($value);
				break;
			case 'alpha':
				// 只允许字母
				$result = $this->regex($value, '/^[A-Za-z]+$/');
				break;
			case 'alphaNum':
				// 只允许字母和数字
				$result = $this->regex($value, '/^[A-Za-z0-9]+$/');
				break;
			case 'alphaDash':
				// 只允许字母、数字和下划线 破折号
				$result = $this->regex($value, '/^[A-Za-z0-9\-\_]+$/');
				break;
			case 'activeUrl':
				// 是否为有效的网址
				$result = checkdnsrr($value);
				break;
			case 'ip':
				// 是否为IP地址
				$result = $this->filter($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6);
				break;
			case 'url':
				// 是否为一个URL地址
				$result = $this->filter($value, FILTER_VALIDATE_URL);
				break;
			case 'float':
				// 是否为float
				$result = $this->filter($value, FILTER_VALIDATE_FLOAT);
				break;

			case 'integer':
				// 是否为整形
				$result = $this->filter($value, FILTER_VALIDATE_INT);
				break;
			case 'email':
				// 是否为邮箱地址
				$result = $this->filter($value, FILTER_VALIDATE_EMAIL);
				break;

			case 'array':
				// 是否为数组
				$result = is_array($value);
				break;
			default:

		}

		return $result;
	}

	/**
	 * 使用filter_var方式验证
	 * @access protected
	 * @param mixed     $value  字段值
	 * @param mixed     $rule  验证规则
	 * @return bool
	 */
	protected function filter($value, $rule)
	{
		if (is_string($rule) && strpos($rule, ',')) {
			list($rule, $param) = explode(',', $rule);
		} elseif (is_array($rule)) {
			$param = isset($rule[1]) ? $rule[1] : null;
		} else {
			$param = null;
		}
		return false !== filter_var($value, is_int($rule) ? $rule : filter_id($rule), $param);
	}

	/**
	 * 使用正则验证数据
	 * @access protected
	 * @param mixed     $value  字段值
	 * @param mixed     $rule  验证规则 正则规则或者预定义正则名
	 * @return mixed
	 */
	protected function regex($value, $rule)
	{
		if (isset($this->regex[$rule])) {
			$rule = $this->regex[$rule];
		}
		if (0 !== strpos($rule, '/') && !preg_match('/\/[imsU]{0,4}$/', $rule)) {
			// 不是正则表达式则两端补上/
			$rule = '/^' . $rule . '$/';
		}
		return 1 === preg_match($rule, (string) $value);
	}



	/*
	 * 分析规则，
	 *
	 * @access protected
	 * @param string $scene
	 * @return array
	 *
	 * array(
	 * 		验证字段,
	 * 			必须
	 * 		验证规则,
	 * 			必须
	 * 		错误提示,
	 * 			有默认值
	 * 		[验证条件],
	 * 			可选
	 * 				0 存在字段就验证（默认）
					1 必须验证
					2 值不为空的时候验证
	 * 		[附加规则],
	 * 			可选
	 * 				confirm：验证表单中的两个字段是否相同，定义的验证规则是一个字段名
     *              in：验证是否在某个范围内，定义的验证规则必须是一个数组
     *              length：验证长度，定义的验证规则可以是一个数字（表示固定长度）或者数字范围（例如3,12 表示长度从3到12的范围）
     *              between：验证范围，定义的验证规则表示范围，可以使用字符串或者数组，例如1,31或者array(1,31)
	 * 		[验证时间]
	 * 			1 新增数据时候验证
				2 编辑数据时候验证
				0 全部情况下验证（默认）

	 * 	);
	 */
	protected function getRule($rule,$scene)
	{

		//step1、确定验证字段是否存在
			if(empty($rule[0])){
				return false;
			}else{
				$rule[0] .= '|';
				@list($rule[0],$attribute) = explode('|',$rule[0]);
				if(empty($attribute)) $attribute = $rule[0];
			}

		//step2、确定验证规则是否存在
			if(empty($rule[1])){
				return false;
			}

		//step3、根据验证规则，补全默认提示
			$this->message = null;
			if(!empty($rule[2])){
				$this->message = $rule[2];
			}


		//step4、确定验证条件 默认是0 数据存在即验证
			if(!isset($rule[3])){
				$rule[3] = 0;
			}else{
				if($rule[3] === false){
					$rule[3] = 0;
				}else if($rule[3] === ''){
					$rule[3] = 0;
				}
			}

		//step5、确定附加规则
			if($rule[4] === null || $rule[4] === true || $rule[4] === '' || $rule[4] === false){
				$rule[4] = false;
			}
			if(strlen($rule[4]) > 0){
				$rule[4] = $rule[4];
			}

		//step6、确定场景
			if(!isset($rule[5])){
				$rule[5] = 0 ;
			}else{
				if($rule[5] === false){
					$rule[5] = 0 ;
				}else if($rule[5] === true){
					$rule[5] = 0;
				}
			}

		//step7、确定提示消息
			if(empty($this->message)){
				if(!empty($rule[4])){
					$this->message = $this->typeMsg[$rule[4]];
				}elseif(!empty($rule[1])){
					$this->message = $this->typeMsg[$rule[1]];
				}else{
					$this->message = "未知错误";
				}
				$this->message = str_replace(':attribute',$attribute,$this->message);
				$this->message = str_replace(
					':rule',
					is_string($rule[1]) ?
						$rule[1] :
						implode(',',$rule[1]),
					$this->message);
			}

		if($scene !== $rule[5] && $rule[5] !== 0) return false;

		return $rule;
	}


	/**
	 * 验证是否在范围内
	 * @access protected
	 * @param mixed     $value  字段值
	 * @param mixed     $rule  验证规则
	 * @return bool
	 */
	protected function in($value, $rule)
	{
		return in_array($value, is_array($rule) ? $rule : explode(',', $rule));
	}





























	// 验证规则默认提示信息
	protected  $typeMsg = [
		'require'    => ':attribute不能为空',
		'number'     => ':attribute必须是数字',
		'float'      => ':attribute必须是浮点数',
		'boolean'    => ':attribute必须是布尔值',
		'email'      => ':attribute不是有效的邮箱',
		'array'      => ':attribute必须是数组',
		'accepted'   => ':attribute必须是yes、on或者1',
		'integer'    => ':attribute必须整数',
		'date'       => ':attribute格式不符合',
		'file'       => ':attribute不是有效的上传文件',
		'alpha'      => ':attribute只能是字母',
		'alphaNum'   => ':attribute只能是字母和数字',
		'alphaDash'  => ':attribute只能是字母、数字和下划线_及破折号-',
		'activeUrl'  => ':attribute不是有效的域名或者IP',
		'url'        => ':attribute不是有效的URL地址',
		'ip'         => ':attribute不是有效的IP地址',
		'dateFormat' => ':attribute必须使用日期格式 :rule',
		'in'         => ':attribute必须在 :rule 范围内',
		'notIn'      => ':attribute不能在 :rule 范围内',
		'between'    => ':attribute只能在 :1 - :2 之间',
		'notBetween' => ':attribute不能在 :1 - :2 之间',
		'length'     => ':attribute长度不符合要求 :rule',
		'max'        => ':attribute长度不能超过 :rule',
		'min'        => ':attribute长度不能小于 :rule',
		'after'      => ':attribute日期不能小于 :rule',
		'before'     => ':attribute日期不能超过 :rule',
		'expire'     => '不在有效期内 :rule',
		'allowIp'    => '不允许的IP访问',
		'denyIp'     => '禁止的IP访问',
		'confirm'    => ':attribute和字段 :rule 不一致',
		'egt'        => ':attribute必须大于等于 :rule',
		'gt'         => ':attribute必须大于 :rule',
		'elt'        => ':attribute必须小于等于 :rule',
		'lt'         => ':attribute必须小于 :rule',
		'eq'         => ':attribute必须等于 :rule',
		'unique'     => ':attribute已存在',
		'regex'      => ':attribute不符合指定规则',
		'method'     => '无效的请求类型',
		'token'      => '令牌数据无效',
		'fileSize'   => '上传文件大小不符',
		'fileExt'    => '上传文件后缀不符',
		'fileMime'   => '上传文件类型不符',
	];


}