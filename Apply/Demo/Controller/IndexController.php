<?php

use Core\Controller;
use View\View;
use Library\Session;
use Core\Exception;

class IndexController extends Controller{

	//use \Component\Jump;
	public function _initialize(){
		//parent::_initialize();
	}

	public function index(){

		echo Session::get('aa');

	}









}