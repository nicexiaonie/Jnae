<?php
namespace Demo\Controller\Data;
use Core\Controller;
use Core\Loaders;
use View\View;
use Library\Cache;
use Library\Redis;

class DataController extends Controller{


	public function _initialize(){
		$_POST['cid'] = 4;
		$_POST['addtime'] = time();
		$_POST['status'] = 0;
		$_POST['userid'] = 1;
		$_POST['title'] = '-----';
		//$_POST['asaf'] = '标题';

	}


	/**
	 * 添加多条数据
	 */
	public function addMore(){
		$m = M('add');
		$data[] = $_POST;
		$data[] = $_POST;
		$result = $m->add($data);
		if($result){
			echo '成功';
		}else{
			echo $m->_sql();
		}
	}

	/**
	 *	修改一条数据 (模型)
	 */
	public function save(){
		$m = D('Auto/Add');
		$result = $m->where(array('id'=>10))->save();
		if($result){
			echo '成功';
		}else{
			show($m->getError());
		}
	}

	/**
	 * 添加一条数据(模型)
	 */
	public function add(){
		$m = D('Data/Add');

		$result = $m->add();
		if($result){
			echo '成功';
		}else{
			show($m->getError());
		}
	}











}