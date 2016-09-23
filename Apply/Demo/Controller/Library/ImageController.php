<?php

namespace Demo\Controller\Library;

use Core\Controller;

use Library\Image\ImageCircle;
use Library\Image\ImageOutputGD;


class ImageController extends Controller{


	public function _initialize(){


	}

	public function index(){

		echo <<<html
		<style>

		</style>
		<html>
		<body style="background-color: #cccccc">
		<img src="/Demo/Library/Image/rectangle" >
		</body>

		</html>

html;


	}

	public function save(){
		//$img = ImageOutput::Instance('./img/2.gif');


		//$img = ImageOutput::Instance('./img/f.jpg');
		$img = new ImageOutputGD('./img/a2.jpg');
		//$img = ImageOutput::Instance('./img/1.png');


		$img->ClipQuadrate(250);

		$img->Write('./Runtime/aaaaa',IMAGETYPE_JPEG);
	}


	/**
	 * w	宽
	 * 	h	高
	 * 	e	缩放优先边
	 * 		0：长边（默认值）
	 * 		1：短边
	 * 		2：强制缩略
	 * 	p	倍数百分比	小于100，即是缩小，大于100即是放大。
	 * 	l	目标缩略图大于原图是否处理。如果值是1, 即不处理，是0，表示处理 0/1, 默认是0
	 * 	bgc	指定填充的背景颜色。默认为白色填充	如：100-100-100bgc
	 */
	public function output(){

		//$img = ImageOutput::Instance('./img/f.jpg');
		$img = new ImageOutputGD('./img/a2.jpg');
		//$img = ImageOutput::Instance('./img/1.png');
		$str = '300w_300h';
		$img->Scale($str);


		$img->Output();
	}

	/**
	 * 裁剪正方形
	 */
	public function quadrate(){

		$img = new ImageOutputGD('./img/a2.jpg');

		/*
		 * param1 正方形尺寸
		 * param2 正方形大于原图是否处理。如果值是1, 即不处理，是0，表示处理,默认是0
		 */
		$img->ClipQuadrate(250);

		$img->Output();
	}

	/**
	 * 裁剪矩形
	 */
	public function rectangle(){

		$img = new ImageOutputGD('./img/a2.jpg');

		/*
		 * param1 宽
		 * param2 高
		 */
		$img->ClipRectangle(250,150);

		$img->Output();
	}


	/**
	 * 裁剪一个圆
	 */
	public function circle(){

		//$img = ImageOutputGD::Instance('./img/a1.jpg');
		//$img = ImageOutput::Instance('./img/2.gif');
		//$img = ImageOutput::Instance('./img/1.png');
		$img = new ImageOutputGD('./img/a1.jpg');

		/*
		 * param1 直径
		 * param2 填充颜色 默认 ffffff:白色
		 */
		//$img->ClipQuadrate(250);
		$img->ClipCircle(250);

		$img->Output();
	}





}