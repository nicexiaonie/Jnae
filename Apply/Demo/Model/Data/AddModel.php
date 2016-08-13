<?php
namespace Demo\Model\Data;

use \Core\Model;
class AddModel extends Model{


	public function _initialize(){
		//echo 11;

	}

	/**
	 * 	表字段：
	 *  在进行添加等操作是 如果此变量存在，则自动过滤字段值
	 */
	protected $_fields = array(
		'id',
		'cid',
		'addtime',
		'status',
		'userid',
		'title'
	);

	/**
	 * 	自动验证：
	 *  	在进行添加等操作是 如果此变量存在，则用Validate类自动对数据进行验证
	 * 		Validate类中场景自定义，默认全部验证
	 * 		模型中验证时场景：
	 * 			0：任何状态下都进行验证 （默认）
	 * 			1  添加
	 * 			2  修改
	 * 	@param1 字段
	 * 	@param2 验证规则
	 * 	@param3 错误消息
	 * 	@param4 验证条件:	0 存在字段就验证（默认）1必须验证 2值不为空的时候验证
	 * 	@param5 附加规则
	 * 	@param6 验证场景
	 */
	protected $_validate = array(
		array('cid','require','',1,'',1),	//插入认证
		array('cisdfd','require','',1,'',2),	//插入认证
	);










}