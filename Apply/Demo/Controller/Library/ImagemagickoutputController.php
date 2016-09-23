<?php
namespace Demo\Controller\Library;

use Core\Controller;
use Library\Image\ImageMagickOutput;

class ImageMagickOutputController extends Controller{


	public function index(){

		echo <<<html
		<style>
			*{margin:0px;}
		</style>
		<html>
		<body style="background-color: #cccccc">
		<img src="/Demo/Library/ImageMagickOutput/ClipCircle" >
		</body>

		</html>

html;


	}

	public function ClipCircle(){
		$img = new ImageMagickOutput('./img/a2.jpg');

		$str = '300w_300h_1e_0l';
		$img->Scale($str);
		$img->ClipQuadrate(300);
		$img->ClipCircle();

		$img->Output();
	}
	public function ClipRectangle(){
		$img = new ImageMagickOutput('./img/a2.jpg');

		$str = '50p';
		$str = '300w_300h';
		//$img->output($str);
		$img->ClipRectangle(250,150);

		$img->Output();
	}
	public function ClipQuadrate(){
		$img = new ImageMagickOutput('./img/a2.jpg');

		$str = '50p';
		$str = '300w_300h';
		//$img->output($str);
		$img->ClipQuadrate(250,0);

		$img->Output();
	}

	public function output(){
		$img = new ImageMagickOutput('./img/a2.jpg');

		$str = '50p';
		$str = '300w_300h_0e';
		$img->Scale($str);
		//$img->ClipQuadrate(200,0);

		$img->Output();
	}
}