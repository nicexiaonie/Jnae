<?php
namespace Demo\Controller\Index;
use Core\Controller;

class IndexController extends Controller{


	public function _initialize(){
		show('success:('.__FILE__.')');
		//parent::_initialize();

	}

	public function index(){


		$m = D('login');
		echo 11;
		//$this->display();

	}








}