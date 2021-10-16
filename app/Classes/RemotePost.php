<?php
namespace App\Classes;

class RemotePost
{
    public $UserName;
    public $Password;
    public $Footer;
    function __construct(){
        $this->UserName="Advertising1389";
        $this->Password="Adver_isfahan";
    }

    function SendCode($mobileNumber,$footer){
        $Url="http://smspanel.Trez.ir/AutoSendCode.ashx";
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,$Url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
        http_build_query(array('Username' => $this->UserName,
        'Password'=>$this->Password,
        'Mobile'=>$mobileNumber,
        'Footer'=>$footer)));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close ($ch);
        return $server_output;

    }

    function IsCodeValid($mobileNumber,$code){
         $Url="http://smspanel.Trez.ir/CheckSendCode.ashx";
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,$Url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
        http_build_query(array('Username' => $this->UserName,
        'Password'=>$this->Password,
        'Mobile'=>$mobileNumber,
        'Code'=>$code)));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close ($ch);
        return $server_output;

    }

    function SendCustomMessage($mobileNumber,$message)
    {
//        $Url="http://smspanel.Trez.ir/SendMessageWithCode.ashx";
//
//
//
//        $ch = curl_init();
//
//        curl_setopt($ch, CURLOPT_URL,$Url);
//        curl_setopt($ch, CURLOPT_POST, 1);
//        curl_setopt($ch, CURLOPT_POSTFIELDS,
//        http_build_query(array('Username' => $this->UserName,
//        'Password'=>$this->Password,
//        'Mobile'=>$mobileNumber,
//        'Message'=>$message)));
//
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//
//        $server_output = curl_exec($ch);
//
//        curl_close ($ch);
//        return $server_output;
        $res=new \soapclient('http://smspanel.trez.ir/FastSend.asmx?WSDL');
        $result=$res->SendMessageWithCode(array(
            'Username'=>$this->UserName,
            'Password'=>$this->Password,
            'ReciptionNumber'=>$mobileNumber,
            'Code'=>$message
        ));
        if($result->SendMessageWithCodeResult>2000){
            return 'پیامک ارسال گردید - '.$result->SendMessageWithCodeResult;
        }else{
            return 'خطا در ارسال پیامک - '.$result->SendMessageWithCodeResult;
        }


    }

}


?>
