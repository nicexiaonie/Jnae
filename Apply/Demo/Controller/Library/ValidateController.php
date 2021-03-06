<?php
/*
 * 	验证类 Demo
 *
 */
namespace Demo\Controller\Library;

use Core\Controller;
use Library\Input;
use Library\Validate;
use View\View;

class ValidateController extends Controller{

	public function _initialize(){

	}


	public function index(){
		$post = Input::post();

		if(empty($post)){
			$view = View::init();
			$view->display();

		}else{

			$rule = array(
				//array('title','require',''),
				array('content','require','',1,0),

			);
			show($post);
			$obj = Validate::make($rule);

			$result = $obj->check($post);
			if($result){
				echo 'success';
			}else{
				echo $obj->error();
			}




		}



	}

	public function ip(){

		$data = array(
			'name'	=>	'小明',
		);
		$rule2 = array('name','ip');
		//获取验证实例
		$obj = Validate::make();
		//添加一条验证规则
		$obj->rule($rule2);
		$result = $obj->check($data);

		if($result){
			echo 'success';
		}else{
			echo $obj->error();
		}

	}

	public function in(){
		$data = array(
			'name'	=>	'小明',
		);
		$rule2 = array('name',array('小李','小华'),'',1,'in',0);

		$obj = Validate::make();	//获取验证实例

		$obj->rule($rule2);		//添加一条验证规则
		$result = $obj->check($data);
		if($result){
			echo 'success';
		}else{
			echo $obj->error();
		}
	}

	public function accepted(){
		$data = array(
			'name'	=>	'小明',
		);
		$rule2 = array('name','accepted');
		$obj = Validate::make();	//获取验证实例
		$obj->rule($rule2);		//添加一条验证规则
		$result = $obj->check($data);
		if($result){
			echo 'success';
		}else{
			echo $obj->error();
		}
	}

	public function date(){
		$data = array(
			'name'	=>	'小明',
		);
		$rule2 = array('name','date');
		$obj = Validate::make();	//获取验证实例
		$obj->rule($rule2);		//添加一条验证规则
		$result = $obj->check($data);
		if($result){
			echo 'success';
		}else{
			echo $obj->error();
		}
	}








}