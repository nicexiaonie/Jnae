<?php
namespace Library\Image;


class ImageMagickOutput{

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

		$this->resource = new \Imagick($filename);
		$this->type = strtolower($this->resource->getImageFormat());
		$this->width = $this->resource->getImageWidth();
		$this->height = $this->resource->getimageheight();
	}

	/**
	 * 缩放
	 * 	w	宽
	 * 	h	高
	 * 	e	缩放优先边
	 * 		0：长边（默认值）
	 * 		1：短边
	 * 		2：强制缩略
	 * 	p	倍数百分比	小于100，即是缩小，大于100即是放大。
	 * 	l	目标缩略图大于原图是否处理。如果值是1, 即不处理，是0，表示处理 0/1, 默认是0
	 * @param int $size
	 */
	public function Scale($param){
		if(empty($param)) return false;
		#解析参数
		$param =$this->parse($param);
		#计算缩略后大小
		$size = $this->calculate($param);
		$this->resource->thumbnailimage($size['width'],$size['height']);

	}
	/**
	 * 裁剪正方形
	 * @param int $size
	 */
	public function ClipQuadrate($size = 120,$l = 0){
		$width = $this->resource->getimagewidth();
		$height = $this->resource->getimageheight();
		if($l == 1){
			$hw = array($width,$height); sort($hw);
			$minSize = array_shift($hw);
			if($size > $minSize){
				$size = $minSize;
			}
		}
		$x = ($width - $size) /2;
		$y = ($height - $size) /2;
		$this->resource->extentImage($size,$size,$x,$y);
	}
	/**
	 * 裁剪矩形
	 * @param int $size
	 */
	public function ClipRectangle($width = 120,$height = 120){
		$OriginalWidth = $this->resource->getimagewidth();
		$OriginalHeight = $this->resource->getimageheight();
		$x = ($OriginalWidth - $width) /2;
		$y = ($OriginalHeight - $height) /2;
		$this->resource->extentImage($width,$height,$x,$y);

	}
	/**
	 * 裁剪一个圆
	 * @param int $size
	 */
	public function ClipCircle($rgb = null){
		$this->resource->setImageFormat('png');
		$this->resource->roundCorners(
			$this->resource->getImageWidth()/2,$this->resource->getImageHeight()/2
		);
		//填充边角颜色
		if(!empty($rgb)){
			$newimg = new \Imagick();
			$color_transparent = new \ImagickPixel('#'.$rgb);   //transparent 透明色
			$newimg->newimage($this->resource->getImageWidth(),$this->resource->getImageHeight(),$color_transparent,'png');
			$newimg ->compositeImage($this->resource,\Imagick::COMPOSITE_OVER,0,0);
			$this->resource = $newimg;
		}
	}

	/**
	 * 显示图片
	 */
	public function Output(){

		header('Content-type: image/jpeg');
		echo $this->resource;
		$this->resource->destroy();
	}

	/**
	 * 保存为图片
	 * @param null $filename
	 * @return bool
	 */
	public function Write($filename = null){
		if(!empty($filename)){
			return $this->resource->writeimage($filename);
		}
		return false;
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


}