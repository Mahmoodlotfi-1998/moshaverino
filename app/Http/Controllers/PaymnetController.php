<?php

namespace App\Http\Controllers;

use App\Functions\Algoritms;
use App\Functions\jdf;
use App\Functions\Settings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymnetController extends Controller
{
    private $algoritms;
    private $jdf;
    private $settings;
    private $zarin_merchent='aeda6178-f593-4d1a-9cdf-52287c0c37e2';
    private $schema='dordorpay';
    private $package='com.appemon.moshaverino';

    function __construct(){
        $this->algoritms=new Algoritms();
        $this->jdf=new jdf();
        $this->settings=new Settings();
    }

    public function CheckParameters(){

        if(isset($_GET['s_id'])){
            $subscription = DB::table('l_m_subscription')
                ->where('id','=',intval($_GET['s_id']));
//            print_r( $subscription);
            if ($subscription->count()>0){
                $subscriptio=$subscription->first();
                return $subscriptio;
            }
        }
        return 0;
    }

    public function CheckParametersServices(){

        if(isset($_GET['s_id'])){
            $services=LM_Services::where('id',$this->algoritms->discreate_id($_GET['s_id']));
            $services=$this->algoritms->check_exist_row($services);
            return $services;

        }
        return 0;
    }

    public function CheckUser(){
        if(isset($_GET['ur_id'])){
            $user=User::where('id',$this->algoritms->discreate_id($_GET['ur_id']));
            $user=$this->algoritms->check_exist_row($user);
            return $user;
        }
    }

    public function CheckTr(){
        if(isset($_GET['ur_id'])){
            $user=DB::table('payments')->where('id',$this->algoritms->discreate_id($_GET['ur_id']));
            $user=$this->algoritms->check_exist_row($user);
            return $user;
        }
    }


    public function Payment(){
        ob_start();
        session_start();
        $res='';
        $back_app='<div class="contain_pay">
                        <div class="header-pay"><h1 class="matn">اپلیکیشن مشاورینو</h1></div>
                        <div class="confim"><h3 class="confim">مشکلی پیش آمد دوباره از اپلیکیشن وارد شوید.</h3></div>';

        $web_back_url='<a class="pay_back" href="https://zobs.ir/webapp">بازگشت به اپ</a>';
        $app_back_url='<a class="pay_back" href="intent://0#Intent;scheme='.$this->schema.';package='.$this->package.';end">بازگشت به اپ</a>';

        if ($_GET['source'] == 1){
            $back_app.=$web_back_url;
            $web=1;
        }else{
            $back_app.=$app_back_url;
            $web=0;
        }
        $back_app.='</div>';

        if (isset($_GET['action'])){

            switch ($_GET['action']){
                case 'begin':
                    $last_price=$_GET['p'];

                    $res='<div class="contain_pay">
                                                <div class="header-pay"><h1 class="matn">اپلیکیشن مشاورینو</h1></div>
                                                <div class="mony"><h1 style="font-size: 21px;">پرداخت ' . $last_price . 'تومان </h1></div>
                                                <div class="confim"><h3 class="confim">نسبت به پرداخت آن اطمینان دارید؟</h3></div>


                                                <a class="pay_btn" href="?source=' . $_GET['source'] .'&action=pay&ur_id=' . $_GET['ur_id'] .'&p='.$_GET['p'].'"><p style="margin-top: 5px;">پرداخت میکنم</p></a>';
                    if ($web){
                        $res.=$web_back_url;
                    }else{
                        $res.=$app_back_url;
                    }
                    $res.='</div>';
                    break;

                case 'pay':
                    $last_price=$_GET['p'];

                    $user=$this->CheckUser();


                    $payment=DB::table('payments')->insertGetId([
                        'user_id'=>$user->id,
                        'mount'=>$last_price,
                        'desc'=>' پرداختی کیف پول ',
                        'status'=>'pay',
                        'time'=>$this->jdf->jdate('H:i:s'),
                        'date'=>$this->jdf->jdate('Y/m/d')
                    ]);

                    if ($payment){
                        $client = new \SoapClient('https://sandbox.zarinpal.com/pg/services/WebGate/wsdl', ['encoding' => 'UTF-8']);

                        $result = $client->PaymentRequest([
                            'MerchantID' => $this->zarin_merchent,
                            'Amount' => $last_price,
                            'Description' => 'پرداخت کیف پول',
                            'CallbackURL' => $this->settings->get_base_url().'api/payment?source='.$web.'&action=end&&ur_id='.strval($this->algoritms->create_id($payment))
                        ]);

                        if ($result->Status == 100) {

                            return redirect('https://sandbox.zarinpal.com/pg/StartPay/'.$result->Authority);

                        }else{
                            $res='<div class="center-align">
                                                <h5 class="m orange-text">خطا در پرداخت</h5>
                                                <p class="grey-text tt">تراکنش ناموفق : '.pay_response($result->Status).' ('.$result->Status.')</p>';
                            if ($web){
                                $res.=$web_back_url;
                            }else{
                                $res.=$app_back_url;
                            }
                            $res.='</div>';
                        }

                    }else{
                        $res=$back_app;
                    }

                    break;

                case 'end':

                    $Authority=$_GET['Authority'];
                    $tr=$this->CheckTr();
                    $moment=$tr->mount;
//                            var_dump($tr);
                    if ($_GET['Status'] == 'OK') {
                        $client = new \SoapClient('https://sandbox.zarinpal.com/pg/services/WebGate/wsdl', ['encoding' => 'UTF-8']);
                        $result = $client->PaymentVerification(
                            [
                                'MerchantID' => $this->zarin_merchent,
                                'Authority' => $Authority,
                                'Amount' => strval($moment),
                            ]
                        );

                        if ($result->Status == 100) {

                            $update=DB::table('payments')
                                ->where('id', $tr->id)->update([
                                    'status' => "end",
                                    'bank_response' => print_r($result,true),
                                    'RefId' => $result->RefID,
                                    'bank_detail' => $Authority,
                                    'date' => $this->jdf->jdate('Y/m/d'),
                                    'time' => $this->jdf->jdate('H:i:s')

                                ]);

                            $user=DB::table('users')->where('id','=',$tr->user_id)->first();

                            $insert_tr=DB::table('transaction')->insertGetId([
                                'user_id'=>$tr->user_id,
                                'type'=>'inc',
                                'description'=>'افزایش کیف پول',
                                'value'=>$tr->mount,
                                'date' => $this->jdf->jdate('Y/m/d'),
                                'time' => $this->jdf->jdate('H:i:s'),
                                'day' => $this->jdf->jdate('l')
                            ]);

                            $update_use_wallet=DB::table('users')->where('id','=',$tr->user_id)
                                ->update([
                                    'wallet'=>intval($user->wallet)+intval($tr->mount)
                                ]);

                            if($update && $update_use_wallet && $insert_tr){

                                $res='<div class="center-align"><img src="https://img.icons8.com/color/96/000000/ok--v1.png" class="center-block" />
                                                <h5 class="m green-text">پرداخت موفق</h5>
                                                <p class="grey-text tt">پرداخت با موفقیت انجام شد ، کد رهگیری : <strong class="green-text ss">'.$result->RefID.'</strong></p>';
                                if ($web){
                                    $res.=$web_back_url;
                                }else{
                                    $res.=$app_back_url;
                                }
                                $res.='</div>';
                            }else{
                                $res='<div class="contain_pay"><img src="https://img.icons8.com/color/100/000000/error.png" class="center-block" />
                                            <h5 class="orange-text">خطا سیستمی در پرداخت</h5>
                                            <p class="grey-text tt">تراکنش ناموفق : '.$result->Status.' ('.$result->Status.')</p>';
                                if ($web){
                                    $res.=$web_back_url;
                                }else{
                                    $res.=$app_back_url;
                                }
                                $res.='</div>';
                            }

                        }else{

                            $res='<div class="contain_pay"><img src="https://img.icons8.com/color/100/000000/error.png" class="center-block" />
                                            <h5 class="orange-text">خطا در پرداخت</h5>
                                            <p class="grey-text tt">تراکنش ناموفق : '.$result->Status.' ('.$result->Status.')</p>';
                            if ($web){
                                $res.=$web_back_url;
                            }else{
                                $res.=$app_back_url;
                            }
                            $res.='</div>';

                        }

                    }else{
                        $res='<div class="center-align">
                                            <h5 class="m orange-text">خطا در پرداخت</h5>';
                        if ($web){
                            $res.=$web_back_url;
                        }else{
                            $res.=$app_back_url;
                        }
                        $res.='</div>';
                    }



                    break;


            }
            return view('pages.payment' , ['text'=> $res,'phone'=>'09379109962']);
        }

    }
}
