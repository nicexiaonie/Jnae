<?php

class indexController extends Controller{


	public function _initialize(){

	}


	public function index(){

		$m = M('news');
		show($_COOKIE);

		echo 22;
	}






}