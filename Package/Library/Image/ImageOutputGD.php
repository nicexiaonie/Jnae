<?php
namespace Library\Image;

use Core\Config;

class ImageOutputGD{

	/**
	 * 图片文件名（路径）
	 * @var string
	 */
	private $filename = '';

	/**
	 * 图片资源
	 * @var resource
	 */
	private $resource;

	/**
	 * 图片类型  .jpg
	 * @var string
	 */
	private $type = '';

	/**
	 * 图片宽度
	 * @var int
	 */
	private $width = 0;

	/**
	 * 图片高度
	 * @var int
	 */
	private $height = 0;



	/**
	 * @param $filename
	 */
	public function __construct($filename){
		$this->filename = $filename;
		$this->type = $this->getType();
		$this->open();
		$this->getWidth();
		$this->getHeight();
	}

	/**
	 * 打开图片
	 * @return resource
	 */
	private function open(){
		$resource = null;
		$resource = imagecreatefromstring(file_get_contents($this->filename));
		/*
		switch($this->type){
			case '.jpg':
			case '.jpeg':
				$resource = imagecreatefromjpeg($this->filename);
				break;
			case '.png':
				$resource = imagecreatefrompng($this->filename);
				break;
			case '.gif':
				$resource = imagecreatefromgif($this->filename);
				break;
			case '.wbmp':
				$resource = imagecreatefromwbmp($this->filename);
				break;
			case '.xbm':
				$resource = imagecreatefromxbm($this->filename);
				break;
			case '.xpm':
				$resource = imagecreatefromxpm($this->filename);
				break;
			case '.gd':
				$resource = imagecreatefromgd($this->filename);
				break;
			case '.gd2':
				$resource = imagecreatefromgd2($this->filename);
				break;
			default :
				break;
		}
		*/
		return $this->resource = $resource;
	}

	/**
	 * 获取图片宽
	 * @return int
	 */
	private function getWidth(){
		return $this->width = imagesx($this->resource);
	}

	/**
	 * 获取图片高
	 * @return int
	 */
	private function getHeight(){
		return $this->height = imagesy($this->resource);
	}
	/**
	 * 获取图片类型
	 * @return string
	 */
	private function getType($filename = null){
		if(empty($filename)) $filename = $this->filename;
		return strrchr($filename,'.');
	}

	/**
	 * 解析参数
	 * @param $param
	 * @return array
	 * 	w	宽
	 * 	h	高
	 * 	e	缩放优先边
	 * 		0：长边（默认值）
	 * 		1：短边
	 * 		2：强制缩略
	 * 	p	倍数百分比	小于100，即是缩小，大于100即是放大。
	 * 	l	目标缩略图大于原图是否处理。如果值是1, 即不处理，是0，表示处理 0/1, 默认是0
	 * 	bgc	指定填充的背景颜色。默认为白色填充	如：100-100-100bgc
	 */

	private function parse($param){
		$result = array();
		$allow_var = array(
			'w' => 0,
			'h' => 0,
			'e' => 0,
			'p' => 100,
			'l' => 0,
			'bgc' => 2,
		);
		$param = explode('_',$param);
		foreach($param as $item){
			preg_match('/\D*$/',$item,$var_name);
			if(!empty($var_name)){
				$var_name = array_pop($var_name);
				if(in_array($var_name,$allow_var)){
					$result[$var_name] = (int)preg_replace('/\D*$/','',$item);
				}
			}
		}
		foreach(array_diff(array_keys($allow_var),array_keys($result)) as $key=>$value){
			$result[$value] = (int)$allow_var[$value];
		}
		return $result;
	}
	public function Write($filename,$type = IMAGETYPE_JPEG){
		$filename = $filename.image_type_to_extension($type);
		switch($type){
			case IMG_JPEG:
				imagejpeg($this->resource,$filename);
				break;
			case IMG_GIF:
				imagegif($this->resource,$filename);
				break;
			case IMG_PNG:
				imagepng($this->resource,$filename);
				break;
			default:
				break;
		}

	}

	/**
	 * 计算目标宽高
	 * @param $param
	 * @return mixed
	 */
	private function calculate($param){
		#目标缩略图大于原图是否处理  0处理 1不处理
		$result['width'] = $this->width;
		$result['height'] = $this->height;
		//show($param);
		if($param['l'] === 1){
			#目标缩略图是否大于原图
			if($param['w'] > $this->width || $param['h'] > $this->height){
				return $result;
			}
		}

		#长边
		if($param['e'] === 0){
			if($this->width >= $this->height && $param['w'] > 0){
				$scale = $this->width/$param['w'];
				$result['width'] = $this->width/$scale;
				$result['height'] = $this->height/$scale;

			}elseif( $param['h'] > 0){
				$scale = $this->height/$param['h'];
				$result['width'] = $this->width/$scale;
				$result['height'] = $this->height/$scale;
			}
		#短边缩略
		}elseif($param['e'] === 1){
			if($this->width >= $this->height && $param['h'] > 0){
				$scale = $this->height/$param['h'];
				$result['width'] = $this->width/$scale;
				$result['height'] = $this->height/$scale;
			}elseif($param['w'] > 0){
				$scale = $this->width/$param['w'];
				$result['width'] = $this->width/$scale;
				$result['height'] = $this->height/$scale;
			}
		#强制缩略
		}elseif($param['e'] === 2){

			if($param['w'] > 0) $result['width'] = $param['w'];
			if($param['h'] > 0) $result['height'] = $param['h'];
		}

		#等比例
		if($param['p'] > 0 && $param['p'] <= 1000){
			$result['width'] = $result['width']/100*$param['p'];
			$result['height'] = $result['height']/100*$param['p'];
		}

		return $result;
	}

	/**
	 * 150w_500h_0e
	 * @param null $param
	 */
	public function Scale($param = null){
		if(!empty($param)){
			#解析参数
			$param =$this->parse($param);
			#计算缩略后大小
			$size = $this->calculate($param);
			#重置图片宽高
			$this->resize($size['width'],$size['height']);
		}

	}

	/**
	 * 裁剪正方形
	 * @param int $size
	 */
	public function ClipQuadrate($size = 120,$l = 0){
		$width = $this->getWidth();
		$height = $this->getHeight();
		$hw = array($width,$height);sort($hw);
		$minSize = array_shift($hw);
		if($size > $minSize && $l == 0){
			$size = $minSize;
		}
		$x = ($width - $size) /2;
		$y = ($height - $size) /2;
		//将裁剪区域复制到新图片上，并根据源和目标的宽高进行缩放或者拉升
		$new_image = imagecreatetruecolor($size, $size);
		imagecopyresampled($new_image, $this->resource, 0, 0, $x, $y, $size, $size, $size, $size);
		$this->resource = $new_image;
	}

	/**
	 * 裁剪长方形
	 * @param int $size
	 */
	public function ClipRectangle($width = 120,$height = 120){
		$x = ($this->getWidth() - $width) /2;
		$y = ($this->getHeight() - $height) /2;
		//将裁剪区域复制到新图片上，并根据源和目标的宽高进行缩放或者拉升
		$new_image = imagecreatetruecolor($width, $height);
		imagecopyresampled($new_image, $this->resource, 0, 0, $x, $y, $width, $height, $width, $height);
		$this->resource = $new_image;
	}

	/**
	 * 裁剪一个圆
	 * @param int $size
	 */
	public function ClipCircle($size,$rgb = 'ffffff'){
		if(!is_array($rgb)){
			//有的人喜欢带#号
			$rgb = trim($rgb, '#');
			//处理缩写形式
			if (strlen($rgb)==3){
				$_tmp = $rgb[0].$rgb[0].$rgb[1].$rgb[1].$rgb[2].$rgb[2];
				$rgb = $_tmp;
			}
			$rgb = $this->createRGB($rgb); //16进制值 ffff00
		}

		$shadow = $this->createshadow($size,$rgb); //遮罩图片
		imagecopymerge($this->resource,$shadow,0, 0, 0, 0,$this->width,$this->height,100);
		//销毁资源
		imagedestroy($shadow);

	}

	/**
	 * 创建一个圆形遮罩
	 * Enter description here ...
	 * @param array 10进制颜色数组
	 */
	private function createshadow($size,$rgb)
	{
		$width = $this->getWidth();
		$height = $this->getHeight();
		#size为空或大于最小边时  则默认为最大内切圆
		$wh = array($width,$height); sort($wh);$xy = array_shift($wh);
		if($size > $xy || empty($size)) $size = $xy;

 		$img = imagecreatetruecolor($width, $height);
		imageantialias($img,true); //开启抗锯齿
		$color_bg = imagecolorallocate($img, $rgb[0], $rgb[1], $rgb[2]); //背景色
		$color_fg = imagecolorallocate($img, 0, 0, 0); //前景色，主要用来创建圆
		imagefilledrectangle($img, 0, 0, $width, $height, $color_bg);
		imagefilledarc($img, $width/2, $height/2, $size, $size, 0, 0, $color_fg, IMG_ARC_EDGED );
		imagecolortransparent($img, $color_fg); //将前景色转换为透明

		return $img;
	}
	/**
	 * 将16进制颜色转为10进制颜色值数组（RGB）
	 * Enter description here ...
	 * @param $str 16进制串（如：ff9900）
	 */
	private function createRGB($str)
	{
		$rgb = array();
		if(strlen($str) != 6){
			$rgb[] = 0xff;
			$rgb[] = 0xff;
			$rgb[] = 0xff;
			return $rgb; //默认白色
		}

		$rgb[] = $this->getIntFromHexStr(substr($str, 0, 2));
		$rgb[] = $this->getIntFromHexStr(substr($str, 2, 2));
		$rgb[] = $this->getIntFromHexStr(substr($str, 4, 2));

		return $rgb;

	}
	/**
	 * 将字符形式16进制串转为10进制
	 * Enter description here ...
	 * @param $str
	 */
	private function getIntFromHexStr($str)
	{
		$format = '0123456789abcdef';

		$sum = 0;

		for($i=strlen($str)-1, $c=0, $j=0; $i>=$c; $i--,$j++){
			$index = strpos($format, $str[$i]);//strpos从0计算
			$sum+=$index * pow(16,$j);
		}

		return $sum;
	}

	/**
	 * 输出图片
	 */
	public function Output($type = null){
		if(empty($type)) $type = $this->type;
		switch($type){
			case '.jpg':
				header("Content-Type: image/jpeg");
				imagejpeg($this->resource);
				break;
			case '.png':
				header("Content-Type: image/png");
				imagepng($this->resource);
				break;
			case '.gif':
				header("Content-Type: image/gif");
				imagegif($this->resource);
				break;
			default:
				header("Content-Type: image/jpeg");
				imagejpeg($this->resource);
				break;
		}
		exit;
	}

	/**
	 * 重置图片宽高
	 * @param $width
	 * @param $height
	 */
	private function resize($width,$height){

		switch($this->type){
			/* gif
			case '.gif':

				$images = $this->resource->coalesceImages();
				$canvas = new \Imagick();
				foreach($images as $k=>$frame){

					$img = new \Imagick();
					$img->readImageBlob($frame);
					$img->thumbnailImage( $width, $height, false );
					$canvas->addImage( $img );
					$canvas->setImageDelay( $img->getImageDelay() );

				}
				header ( 'Content-type: ' . strtolower ($canvas->getImageFormat ()) );
				echo $canvas->getImagesBlob();

				exit;


				break;
			gif
				*/
			default:
				$resource = imagecreatetruecolor($width, $height);  //创建一个彩色的底图
				imagealphablending($resource, true);
				imagesavealpha($resource, true);
				$trans_colour = imagecolorallocatealpha($resource, 0, 0, 0, 127);
				imagefill($resource, 0, 0, $trans_colour);

				imagecopyresampled($resource,$this->resource,0,0,0,0,$width,$height,$this->width,$this->height);
				$this->resource = $resource;
				break;
		}


	}
}