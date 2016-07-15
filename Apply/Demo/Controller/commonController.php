<?php

namespace Demo\Controller;

use Demo\Controller\Index\IndexController;

class CommonController extends IndexController{


	public function _initialize(){
		show('success:('.__FILE__.')');
		parent::_initialize();
	}


	public function index(){

		$m = M('news');

		show($m->select());

	}







}