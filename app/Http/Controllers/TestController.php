<?php

namespace App\Http\Controllers;

use App\Functions\Algoritms;
use App\Functions\jdf;
use App\Functions\Settings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Classes\RemotePost;

class TestController extends Controller
{
    private $algoritm;
    private $jdf;
    private $settings;

    function __construct(){
        $this->algoritm=new Algoritms();
        $this->jdf=new jdf();
        $this->settings=new Settings();

    }

    public function NarsofalPhoneOtp(Request $request){

        $remotePost=new RemotePost();
        $remotePost->SendCustomMessage($request->post('phone'),$request->post('message'));
    }

    public function Test(Request $request){
        date_default_timezone_set('Asia/Tehran');

        $day_array=['Saturday'=>1,'Sunday'=>2,'Monday'=>3,'Tuesday'=>4,'Wednesday'=>5,'Thursday'=>6,'Friday'=>7];

        $today_day = date('l');
        $today_date = date('Y/m/d');

        $dif_days=  7-($day_array[ucfirst($today_day)] - $day_array[ucfirst($request->post('day'))]);

        $next_date = explode('-',date('Y-m-d', strtotime(' +'.$dif_days.' day')));


        $jdf_time = $this->jdf->gregorian_to_jalalii($next_date[0],$next_date[1],$next_date[2]);
        $jdf_time[0] =($jdf_time[0]>9)?$jdf_time[0]:'0'.$jdf_time[0];
        $jdf_time[1] =($jdf_time[1]>9)?$jdf_time[1]:'0'.$jdf_time[1];
        $jdf_time[2] =($jdf_time[2]>9)?$jdf_time[2]:'0'.$jdf_time[2];

//        $this_time= $this->jdf->jdate('H:m');
//        $t=time();
//        $x=date_create(date("Y-m-d H:i:s",$t));
        $this_time= date('H:i');

        echo $this_time;
//        echo implode('/',$jdf_time);
    }

    public function EndTurnReport(Request $request)
    {
        if ($this->algoritm->discreate_mikhay($request->post('mikhay'))) {

            $user = User::where('users.id', '=', $this->algoritm->discreate_id($request->post('user_id')));

            $user_row = $this->algoritm->check_exist_row($user);
            $report_id=$request->post('id');



        }
    }
}
