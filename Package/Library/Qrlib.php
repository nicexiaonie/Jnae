<?php

namespace Library;

require_once __DIR__.'/Qrlib/qrlib.php';

class Qrlib{




	/**
	 * 	创建png二维码
	 *
	 * 	param string	$text 二维码内容
	 * 	param string	$dir 图片创建目录
	 * 	param string	$errorlevel 错误修正级别
	 * 					L水平    7%的字码可被修正
						M水平    15%的字码可被修正
						Q水平    25%的字码可被修正
						H水平    30%的字码可被修正
	 * 	param int	$size 图片大小  $size*25px
	 * 	param int	$margin 留白
	 */
	public function create_png($text,$dir,$errorlevel = 'L',$size = 8,$margin = 1){
		$png_tmp_dir =$dir;	//图片保存位置
		$errorCorrectionLevel = $errorlevel;	//错误修正级别
		$matrixPointSize = $size;	//控制大小
		$filename = $png_tmp_dir.md5($text.'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
		 \QRcode::png($text, $filename, $errorCorrectionLevel, $matrixPointSize, $margin);
		return basename($filename);
	}



}
