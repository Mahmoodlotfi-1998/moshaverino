<?php

namespace App\Http\Controllers;

use App\Classes\RemotePost;
use App\Functions\Algoritms;
use App\Functions\jdf;
use App\Functions\Settings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PhoneOtpController extends Controller
{
    private $algoritms;
    private $jdf;
    private $settings;

    function __construct(){
        $this->algoritms=new Algoritms();
        $this->jdf=new jdf();
        $this->settings=new Settings();
    }

    public function Manage_Request(Request $request)
    {
//        $algoritms=new Algoritms();
        if($this->algoritms->discreate_mikhay($request->post('mikhay'))){
            $mobile_number=$request->post('phone');
            switch ($request->post('action')){
                case 'send_otp':
                    $otp = rand(1000, 9999);
                    $message ="به مشاورینو خوش آمدید.
کد تایید شما : ";

                    $remotePost=new RemotePost();
                    $message =$message.$otp;
                    $otp_row=DB::table('otp')->where('phone',$mobile_number);
                    try {

                        if($otp_row->count() ==1){
                            $otp_row=$otp_row->update([
                                'otp'=>$otp
                            ]);
                            if($otp_row){
                                $remotePost->SendCustomMessage($mobile_number,$message);
                                $response['status'] = 'ok';
                                $response['send_type'] = 'login';
                                return json_encode($response);
                            }else{
                                $response['status'] = 'nook';
                                return json_encode($response);
                            }
                        }else{
                            if (DB::table('otp')->insert(['phone'=>$mobile_number,'otp'=>$otp])){
                                $remotePost->SendCustomMessage($mobile_number,$message);
                                $response['status'] = 'ok';
                                $response['send_type'] = 'reg';
                                return json_encode($response);
                            }else{
                                $response['status'] = 'nook';
                                return json_encode($response);
                            }
                        }
                    }catch(Exception $e){
                        die('Error: '.$e->getMessage());
                    }

                    break;

                case 'verify_otp':
                    $otp=$request->post('otp');

                    $otp_row=DB::table('otp')->where('phone',$mobile_number)->where('otp',$otp);

                    if($otp_row->count() ==1){
                        $user_row=User::select('id','full_name','phone','city','photo','type','wallet')->where('phone',$mobile_number);
                        if($user_row->count() ==1){
                            $feach_row=$user_row->first();
                            $feach_row['user_id']=strval($this->algoritms->create_id($feach_row['id']));
                            $feach_row->photo= $this->settings->get_pic_url().$feach_row->photo;
                            unset($feach_row['id']);
                            $list=['status'=>'ok','user'=>$feach_row];
                            return json_encode($list);
                        }else{
                            $list=['status'=>'ok','user'=>['user_id'=>0]];
                            return json_encode($list);
                        }

                    }else{
                        return json_encode(array("status"=>"nook"));

                    }

                    break;
            }
        }

    }
}
