<?php

class commonController extends Controller{


	public function _initialize(){
		show('success:('.__FILE__.')');
	}


	public function index(){

		$m = M('news');

		show($m->select());

	}







}