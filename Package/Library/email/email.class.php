<?php

class email{
	public $charset='utf8';		//邮件字符编码
	public $SMTPauth=1;		//开启认证
	public $Port=25;		//端口
	public $Host='smtp.qq.com';	//邮箱服务器
	public $Username='2889870020@qq.com';		//发送人邮箱账户
	public $Password='xiaonie365';		//发送人邮箱密码
	public $AddReplyTo=array(
			'url'=>'2889870020@qq.com',
			'name'=>'聂元培',
		);		//对方回复地址  姓名		对方回复框显示
	public $From='2889870020@qq.com';	//发送人地址
	public $Fromname='name';		//发送人名称
	

	

	function __construct (){
		if(setting('emailcharset')) $this->charset=setting('emailcharset');
		if(setting('loginemail')) $this->SMTPauth=setting('loginemail');
		if(setting('smtpport')) $this->Port=setting('smtpport');
		if(setting('smtphost')) $this->Host=setting('smtphost');
		if(setting('emailusername')) $this->Username=setting('emailusername');
		if(setting('emailpassword')) $this->Password=setting('emailpassword');
		if(setting('fromemail')) $this->From=setting('fromemail');
		if(setting('emailname')) $this->Fromname=setting('emailname');
		if(setting('replyemail')) $this->AddReplyTo['url']=setting('replyemail');
		if(setting('replyname')) $this->AddReplyTo['name']=setting('replyname');
		require(__DIR__.'/class.phpmailer.php');
		$this->PHPMailer = new PHPMailer(true);
	}
	public $AddAddress='1305449323@qq.com';	//收件人地址
	public $title='title';	//邮件标题
	public $body='content';	//	邮件内容
	function send($value=null){
		if(empty($value['email'])) return '缺少收件人地址';
		if(empty($value['title'])) return '缺少邮件标题';
		if(empty($value['body'])) return '缺少邮件内容';

		try{
			$this->PHPMailer->IsSMTP();
			$this->PHPMailer->CharSet=$this->charset;		//设置邮件的字符编码
			$this->PHPMailer->SMTPAuth = $this->SMTPauth;		//开启认证	
			$this->PHPMailer->Port=$this->Port;
			$this->PHPMailer->Host=$this->Host;		//邮箱服务器地址
			$this->PHPMailer->Username = $this->Username;		//用于发送邮件的账号
			$this->PHPMailer->Password = $this->Password;		//发送邮箱的密码
			$this->PHPMailer->AddReplyTo($this->AddReplyTo['url'],$this->AddReplyTo['name']);		//对方回复邮件的地址回复地址
			$this->PHPMailer->From = $this->From;		//发送人的地址
			$this->PHPMailer->FromName = $this->Fromname;		//发送人的名字  显示在对方邮件列表邮件名字


			$this->PHPMailer->AddAddress($value['email']);	//收件人地址
			$this->PHPMailer->Subject = $value['title'];  //收件人邮箱显示标题
			$this->PHPMailer->Body = $value['body'];	//发送邮件内容


			$this->PHPMailer->AltBody = "To view the message, please use an HTML compatible email viewer!";//当邮件不支持html时备用显示，可以省略
			//$this->PHPMailer->WordWrap = 80;	//设置每行字符串的长度
			//$mail->AddAttachment("f:/test.png"); //可以添加附件
			$this->PHPMailer->IsHTML(true);
			$this->PHPMailer->Send();
			return 'success';
		} catch (phpmailerException $e) {
			return "邮件发送失败：".$e->errorMessage();
		}
	}
}
