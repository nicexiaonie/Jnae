<?php

namespace Library;

class File{

	/**
	 * 文件路径
	 * @var string
	 */
	public $path;

	/**
	 * 目录
	 * @var string
	 */
	public $directory;

	/**
	 * 文件名
	 * @var string
	 */
	public $filename;

	/**
	 * 是否可读
	 * @var bool
	 */
	public $is_readable;

	/**
	 * 文件资源
	 * @var resource
	 */
	private $handle;

	/**
	 * 打开方式
	 * @var string
	 */
	private $handle_type;


	/**
	 * 架构函数
	 * 创建一个File对象
	 * param mixed $date  文件路径
	 * static
	 */
	public function __construct($path = null){

		$this->path = $path;
		$directory = $this->directory = $this->basename();
		$filename = $this->filename = $this->filename();

	}

	/**
	 * 	setHandle
	 * 	设置文件打开方式，
	 * 	param string $path	文件路径
	 */
	public function setHandle($handle_type = 'r'){
		if($this->handle_type != $handle_type){
			if(!empty($this->handle)) fclose($this->handle);
			$this->handle = fopen($this->path,$handle_type);
			$this->handle_type = $handle_type;
		}
	}

	/**
	 * 	Instance
	 * 	获取对应实例，
	 * 	param string $path	文件路径
	 */
	public static function Instance($path){
		return new File($path);
	}

	/**
	 * 	获取所在目录
	 * 	return bool
	 */
	private function basename(){
		return dirname($this->path).'/';
	}

	/**
	 * 	获取带扩展的文件名
	 * 	return bool
	 */
	private function filename(){
		return basename($this->path);
	}

	/**
	 * 	文件是否存在
	 * 	return bool
	 */
	private function is_exists(){
		if(!file_exists($this->path)){

		}
	}

	/**
	 * 	向文件追加内容
	 * 	param string $content 追加内容
	 * 	param bool $n 是否换行
	 * 	return bool
	 */
	public function append($content,$n = false){
		if($n) $content .= "\n";
		$this->setHandle('a+');
		$length = fwrite($this->handle,$content);
		return $length;
	}

	/**
	 * 	向文件覆盖内容
	 * 	param string $content 追加内容
	 * 	param bool $n 是否换行
	 * 	return bool
	 */
	public function write($content,$n = false){
		if($n) $content .= "\n";
		$this->setHandle('w+');
		$length = fwrite($this->handle,$content);
		return $length;
	}

	/**
	 * 	从文件指针中读取一行
	 * 		执行一次 指针下移一个
	 * 	param string $length 默认读取1024字节
	 * 	return bool
	 */
	public function fgets($length = '1024'){
		$this->setHandle('r');
		if ($this->handle) {
			if (!feof($this->handle)) {
				$content = fgets($this->handle, $length);
			}
		}
		return $content;
	}

	/**
	 * 	读取指定行
	 * 	param int||array $line 行号
	 * 	param string $length 默认读取1024字节
	 * 	return bool
	 */
	public function getLine($line,$length = 1024){
		$this->setHandle('r');
		(is_array($line)) ? $lines = $line : $lines = array($line);
		$content = array();
		if ($this->handle) {
			$n = 1;
			while(!feof($this->handle)){
				if(empty($lines)) break;
				$text = fgets($this->handle,$length);
				if(in_array($n,$lines)){
					$content[$n] = $text;
					unset($lines[array_search($n,$lines)]);
				}
				$n++;
			}
		}
		foreach($lines as $v)
			$content[$v] = false;
		return (is_array($line)) ? $content : array_pop($content);
	}

	/**
	 * 	读取全部内容
	 * 	return bool
	 */
	public function fileArray(){
		$this->setHandle('r+');
		$content = file($this->path);
		return $content;
	}

	/**
	 *	析构方法:
	 *
	 */
	public function __destruct(){
		fclose($this->handle);
	}



}