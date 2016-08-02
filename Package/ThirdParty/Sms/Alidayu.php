<?php


namespace ThirdParty\Sms;

require_once(__DIR__.'/Alidayu/TopSdk.php');
class Alidayu{

    public  function send_sms($phone,$sign_name,$temp_code,$sms_param=array()){
        $c = new \TopClient();
        $c->appkey = '23365847';
        $c->secretKey = 'f9b2eb61b8bf45880fcf711fb0e8bd15';
        $req = new \AlibabaAliqinFcSmsNumSendRequest;
        $req->setExtend("123456");
        $req->setSmsType("normal");
        $req->setSmsFreeSignName($sign_name);
        if($sms_param){
            $sms_param = json_encode($sms_param);
            $req->setSmsParam($sms_param);
        }
        $req->setRecNum($phone);
        $req->setSmsTemplateCode($temp_code);
	$resp = $c->execute($req);

	//var_dump($resp);
        if(isset($resp['result']) && $resp['result']['success'] == true){
            $return['status'] = 0;
        }else{
            $return['status'] = $resp['code'];
            $return['msg'] = $resp['msg'];
        }
        return $return;
    }
    
    public  function send_call($phone,$temp_code,$sms_param=array()){
        $c = new TopClient;
        $c->appkey = '23333263';
        $c->secretKey = '5af6d6bb41ab22b803b961d26d68547d';
        $req = new AlibabaAliqinFcTtsNumSinglecallRequest;
        $req->setExtend("123456");
        if($sms_param){
            $sms_param = json_encode($sms_param);
            $req->setTtsParam($sms_param);
        }
        $req->setCalledNum($phone);
        $req->setCalledShowNum("4008221620");
        $req->setTtsCode($temp_code);
        $resp = $c->execute($req);
        if(isset($resp['result']) && $resp['result']['success'] == true){
            $return['status'] = 1;
            $return['msg'] = "语音验证码已发送成功，请注意接听";
        }else{
            $return['status'] = 0;
            $return['msg'] = "语音验证码发送失败，请重新获取";
        }
        return $return;
    }
}
