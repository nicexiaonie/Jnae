<?php

class commonController extends controller{


	public function _initialize(){
		echo 'common<br>';
	}


	public function index(){

		$m = M('news');

		show($m->select());

	}







}