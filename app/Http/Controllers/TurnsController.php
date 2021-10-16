<?php

namespace App\Http\Controllers;

use App\Functions\Algoritms;
use App\Functions\jdf;
use App\Functions\Settings;
use App\Models\Doctors;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Hekmatinasser\Verta\Verta;

class TurnsController extends Controller
{
    private $algoritms;
    private $jdf;
    private $settings;

    function __construct(){
        $this->algoritms=new Algoritms();
        $this->jdf=new jdf();
        $this->settings=new Settings();

    }

    public function GetMoneyTurns(Request $request){
        if ($this->algoritms->discreate_mikhay($request->post('mikhay'))) {
            $user = User::where('users.id', '=', $this->algoritms->discreate_id($request->post('user_id')));

            $user_row = $this->algoritms->check_exist_row($user);

            $row=DB::table('turns')
                ->where('user_id','=',$user_row->id)
                ->where('type','=','money');
            if ($row->count() >0){
                $row=$row->first();

                if ($row->saturday == null){
                    $row->saturday="";
                }
                if ($row->sunday == null){
                    $row->sunday="";
                }
                if ($row->monday == null){
                    $row->monday="";
                }
                if ($row->tuesday == null){
                    $row->tuesday="";
                }
                if ($row->wednesday == null){
                    $row->wednesday="";
                }
                if ($row->thursday == null){
                    $row->thursday="";
                }
                if ($row->friday == null){
                    $row->friday="";
                }


            }else{
                $row=['id'=>0];
            }

            return json_encode($row);
        }
    }

    public function EditMoneyTurns(Request $request){
        if ($this->algoritms->discreate_mikhay($request->post('mikhay'))) {
            $user = User::where('users.id', '=', $this->algoritms->discreate_id($request->post('user_id')));

            $user_row = $this->algoritms->check_exist_row($user);

            $row=DB::table('turns')
                ->where('user_id','=',$user_row->id)
                ->where('type','=','money');

            if ($row->count() == 1){
                //update turns

                $update_row=$row->update([
                    'user_id'=>$user_row->id,
                    'saturday'=>trim($request->post('saturday')),
                    'sunday'=>trim($request->post('sunday')),
                    'monday'=>trim($request->post('monday')),
                    'tuesday'=>trim($request->post('tuesday')),
                    'wednesday'=>trim($request->post('wednesday')),
                    'thursday'=>trim($request->post('thursday')),
                    'friday'=>trim($request->post('friday')),
                ]);

                if ($update_row){
                    $response['status'] = 'ok';
                    return json_encode($response);
                }else{
                    $response['status'] = 'nook';
                    return json_encode($response);
                }

            }else{
                //insert to turns

                $insert_row=DB::table('turns')->insertGetId([
                    'user_id'=>$user_row->id,
                    'saturday'=>$request->post('saturday'),
                    'sunday'=>$request->post('sunday'),
                    'monday'=>$request->post('monday'),
                    'tuesday'=>$request->post('tuesday'),
                    'wednesday'=>$request->post('wednesday'),
                    'thursday'=>$request->post('thursday'),
                    'friday'=>$request->post('friday'),
                    'type'=>'money',
                ]);

                if ($insert_row){
                    $response['status'] = 'ok';
                    return json_encode($response);
                }else{
                    $response['status'] = 'nook';
                    return json_encode($response);
                }

            }
        }
    }

    public function GetFreeTurns(Request $request){
        if ($this->algoritms->discreate_mikhay($request->post('mikhay'))) {
            $user = User::where('users.id', '=', $this->algoritms->discreate_id($request->post('user_id')));

            $user_row = $this->algoritms->check_exist_row($user);

            $row=DB::table('turns')
                ->where('user_id','=',$user_row->id)
                ->where('type','=','free');

            if ($row->count() >0){
                $row=$row->first();
                if ($row->saturday == null){
                    $row->saturday="";
                }
                if ($row->sunday == null){
                    $row->sunday="";
                }
                if ($row->monday == null){
                    $row->monday="";
                }
                if ($row->tuesday == null){
                    $row->tuesday="";
                }
                if ($row->wednesday == null){
                    $row->wednesday="";
                }
                if ($row->thursday == null){
                    $row->thursday="";
                }
                if ($row->friday == null){
                    $row->friday="";
                }
            }else{
                $row=['id'=>0];
            }
            return json_encode($row);
        }
    }

    public function EditFreeTurns(Request $request){
        if ($this->algoritms->discreate_mikhay($request->post('mikhay'))) {
            $user = User::where('users.id', '=', $this->algoritms->discreate_id($request->post('user_id')));

            $user_row = $this->algoritms->check_exist_row($user);

            $row=DB::table('turns')
                ->where('user_id','=',$user_row->id)
                ->where('type','=','free');

            if ($row->count() == 1){
                //update turns

                $update_row=$row->update([
                    'user_id'=>$user_row->id,
                    'saturday'=>$request->post('saturday'),
                    'sunday'=>$request->post('sunday'),
                    'monday'=>$request->post('monday'),
                    'tuesday'=>$request->post('tuesday'),
                    'wednesday'=>$request->post('wednesday'),
                    'thursday'=>$request->post('thursday'),
                    'friday'=>$request->post('friday'),
                ]);

                if ($update_row){
                    $response['status'] = 'ok';
                    return json_encode($response);
                }else{
                    $response['status'] = 'nook';
                    return json_encode($response);
                }

            }else{
                //insert to turns

                $insert_row=DB::table('turns')->insertGetId([
                    'user_id'=>$user_row->id,
                    'saturday'=>$request->post('saturday'),
                    'sunday'=>$request->post('sunday'),
                    'monday'=>$request->post('monday'),
                    'tuesday'=>$request->post('tuesday'),
                    'wednesday'=>$request->post('wednesday'),
                    'thursday'=>$request->post('thursday'),
                    'friday'=>$request->post('friday'),
                    'type'=>'free',
                ]);

                if ($insert_row){
                    $response['status'] = 'ok';
                    return json_encode($response);
                }else{
                    $response['status'] = 'nook';
                    return json_encode($response);
                }

            }
        }
    }

    public function GetOpenTimeMoney(Request $request)
    {
        date_default_timezone_set('Asia/Tehran');

        if ($this->algoritms->discreate_mikhay($request->post('mikhay'))) {

            $user = User::where('users.id', '=', $this->algoritms->discreate_id($request->post('doctor_id')));

            $doctor_row = $this->algoritms->check_exist_row($user);

            //get all doctor turns in next week
            $total_times = DB::table('turns')
                ->where('user_id', '=', $doctor_row->id)
                ->where('type', '=', 'money')->first();

            //get time and day for check turns
            $today_day = date('l');
            $this_time= date('H');

            //find today and set for delete expire time
            switch ($today_day){
                case 'Saturday':
                    $array=$total_times->saturday;
                    break;
                case 'Sunday':
                    $array=$total_times->sunday;
                    break;
                case 'Monday':
                    $array=$total_times->monday;
                    break;
                case 'Tuesday':
                    $array=$total_times->tuesday;
                    break;
                case 'Wednesday':
                    $array=$total_times->wednesday;
                    break;
                case 'Thursday':
                    $array=$total_times->thursday;
                    break;
                case 'Friday':
                    $array=$total_times->friday;
                    break;
            }

            //convert db column to array
            $sat_array=explode(',',$total_times->saturday);
            $sun_array=explode(',',$total_times->sunday);
            $mon_array=explode(',',$total_times->monday);
            $tues_array=explode(',',$total_times->tuesday);
            $wen_array=explode(',',$total_times->wednesday);
            $thur_array=explode(',',$total_times->thursday);
            $fri_array=explode(',',$total_times->friday);

            //delete expire time (for example if this time is 14 delete all turns that begin time is less than this time
            $today_times_array = explode(',',$array);
            $count=count($today_times_array);
            for ($i=0;$i<$count;$i++) {
                $begin = explode('-',$today_times_array[$i])[0];
                if (intval($begin) < intval($this_time)){
                    unset($today_times_array[$i]);
                }
            }

            //set repaired time(that delete in last for) to today turns
            switch ($today_day){
                case 'Saturday':
                    $sat_array=$today_times_array;
                    break;
                case 'Sunday':
                    $sun_array=$today_times_array;
                    break;
                case 'Monday':
                    $mon_array=$today_times_array;
                    break;
                case 'Tuesday':
                    $tues_array=$today_times_array;
                    break;
                case 'Wednesday':
                    $wen_array=$today_times_array;
                    break;
                case 'Thursday':
                    $thur_array=$today_times_array;
                    break;
                case 'Friday':
                    $fri_array=$today_times_array;
                    break;
            }

            $day_array=['Saturday'=>1,'Sunday'=>2,'Monday'=>3,'Tuesday'=>4,'Wednesday'=>5,'Thursday'=>6,'Friday'=>7];
            $today_day = date('l');


            //create data array for show in android
            $date_array=array();
            for ($i=7;$i>0;$i--){
                //find the 7 day date
                $diff=7-$i;
                $next_date = explode('-',date('Y-m-d', strtotime(' +'.$diff.' day')));
                $jdf_time = $this->jdf->gregorian_to_jalalii($next_date[0],$next_date[1],$next_date[2]);

                //convert 9 to 09
                $jdf_time[0] =($jdf_time[0]>9)?$jdf_time[0]:'0'.$jdf_time[0];
                $jdf_time[1] =($jdf_time[1]>9)?$jdf_time[1]:'0'.$jdf_time[1];
                $jdf_time[2] =($jdf_time[2]>9)?$jdf_time[2]:'0'.$jdf_time[2];

                $next_jdf_date =implode('/',$jdf_time);

                $timestamp = strtotime(implode('-',$next_date));

                $day = date('l', $timestamp);

                $date_array[7-$i]['date']=$next_jdf_date;
                $date_array[7-$i]['value']=$day;
                switch ($day){
                    case 'Saturday':
                        //check the time not get with another user
                        $check_array=$sat_array;
                        $j=0;
                        foreach ($check_array as $item) {
                            $user_turn_row=DB::table('turns_user')
                                ->where('doctor_id','=',$doctor_row->id)
                                ->where('day','=',lcfirst($day))
                                ->where('date','=',$next_jdf_date)
                                ->where('time','=',$item)
                                ->where('free_type','=','money');

                            if ($user_turn_row->count() == 1){
                                array_splice($check_array,$j,1);
                            }
                            $j++;
                        }
//                        $count_array=count($check_array);
//                        for ($j=0;$j<$count_array;$j++){
//
//                            $user_turn_row=DB::table('turns_user')
//                                            ->where('doctor_id','=',$doctor_row->id)
//                                            ->where('day','=',lcfirst($day))
//                                            ->where('date','=',$next_jdf_date)
//                                            ->where('time','=',$check_array[$j])
//                                            ->where('free_type','=','money');
//
//                            if ($user_turn_row->count() == 1){
//                                unset($check_array[$j]);
//                            }
//                        }

                        $date_array[7-$i]['name']="شنبه";
                        $date_array[7-$i]['child']=$check_array;
                        break;
                    case 'Sunday':
                        //check the time not get with another user
                        $check_array=$sun_array;
                        $j=0;
                        foreach ($check_array as $item) {
                            $user_turn_row=DB::table('turns_user')
                                ->where('doctor_id','=',$doctor_row->id)
                                ->where('day','=',lcfirst($day))
                                ->where('date','=',$next_jdf_date)
                                ->where('time','=',$item)
                                ->where('free_type','=','money');

                            if ($user_turn_row->count() == 1){
                                array_splice($check_array,$j,1);
                            }
                            $j++;
                        }

                        $date_array[7-$i]['name']="یک شنبه";
                        $date_array[7-$i]['child']=$check_array;
                        break;
                    case 'Monday':
                        //check the time not get with another user
                        $check_array=$mon_array;
                        $count_array=count($check_array);

                        $j=0;
                        foreach ($check_array as $item) {
                            $user_turn_row=DB::table('turns_user')
                                ->where('doctor_id','=',$doctor_row->id)
                                ->where('day','=',lcfirst($day))
                                ->where('date','=',$next_jdf_date)
                                ->where('time','=',$item)
                                ->where('free_type','=','money');

                            if ($user_turn_row->count() == 1){
                                array_splice($check_array,$j,1);
                            }
                            $j++;
                        }


                        $date_array[7-$i]['name']="دو شنبه";
                        $date_array[7-$i]['child']=$check_array;
                        break;
                    case 'Tuesday':
                        //check the time not get with another user
                        $check_array=$tues_array;
                        $j=0;
                        foreach ($check_array as $item) {
                            $user_turn_row=DB::table('turns_user')
                                ->where('doctor_id','=',$doctor_row->id)
                                ->where('day','=',lcfirst($day))
                                ->where('date','=',$next_jdf_date)
                                ->where('time','=',$item)
                                ->where('free_type','=','money');

                            if ($user_turn_row->count() == 1){
                                array_splice($check_array,$j,1);
                            }
                            $j++;
                        }

                        $date_array[7-$i]['name']="سه شنبه";
                        $date_array[7-$i]['child']=$check_array;
                        break;
                    case 'Wednesday':
                        //check the time not get with another user
                        $check_array=$wen_array;
                        $j=0;
                        foreach ($check_array as $item) {
                            $user_turn_row=DB::table('turns_user')
                                ->where('doctor_id','=',$doctor_row->id)
                                ->where('day','=',lcfirst($day))
                                ->where('date','=',$next_jdf_date)
                                ->where('time','=',$item)
                                ->where('free_type','=','money');

                            if ($user_turn_row->count() == 1){
                                array_splice($check_array,$j,1);
                            }
                            $j++;
                        }

                        $date_array[7-$i]['name']="چهار شنبه";
                        $date_array[7-$i]['child']=$check_array;
                        break;
                    case 'Thursday':
                        //check the time not get with another user
                        $check_array=$thur_array;
                        $j=0;
                        foreach ($check_array as $item) {
                            $user_turn_row=DB::table('turns_user')
                                ->where('doctor_id','=',$doctor_row->id)
                                ->where('day','=',lcfirst($day))
                                ->where('date','=',$next_jdf_date)
                                ->where('time','=',$item)
                                ->where('free_type','=','money');

                            if ($user_turn_row->count() == 1){
                                array_splice($check_array,$j,1);
                            }
                            $j++;
                        }

                        $date_array[7-$i]['name']="پنج شبنه";
                        $date_array[7-$i]['child']=$check_array;
                        break;
                    case 'Friday':
                        //check the time not get with another user
                        $check_array=$fri_array;
                        $j=0;
                        foreach ($check_array as $item) {
                            $user_turn_row=DB::table('turns_user')
                                ->where('doctor_id','=',$doctor_row->id)
                                ->where('day','=',lcfirst($day))
                                ->where('date','=',$next_jdf_date)
                                ->where('time','=',$item)
                                ->where('free_type','=','money');

                            if ($user_turn_row->count() == 1){
                                array_splice($check_array,$j,1);
                            }
                            $j++;
                        }

                        $date_array[7-$i]['name']="جمعه";
                        $date_array[7-$i]['child']=$check_array;
                        break;
                }



            }
            return json_encode($date_array);


        }
    }

    public function CheckNewTurn(Request $request){
        if ($this->algoritms->discreate_mikhay($request->post('mikhay'))) {
            $user = User::where('users.id', '=', $this->algoritms->discreate_id($request->post('user_id')));

            $user_row = $this->algoritms->check_exist_row($user);

            $user = User::where('users.id', '=', $this->algoritms->discreate_id($request->post('doctor_id')));

            $doctor_row = $this->algoritms->check_exist_row($user);

            $setting=DB::table('setting');

            //require info
            $day_array=['Saturday'=>1,'Sunday'=>2,'Monday'=>3,'Tuesday'=>4,'Wednesday'=>5,'Thursday'=>6,'Friday'=>7];
            $today_day = date('l');
//            $today_date = date('Y/m/d');

            //find the date of send day
            $dif_days=  7-($day_array[ucfirst($today_day)] - $day_array[ucfirst($request->post('day'))]);

            $next_date = explode('-',date('Y-m-d', strtotime(' +'.$dif_days.' day')));


            $jdf_time = $this->jdf->gregorian_to_jalalii($next_date[0],$next_date[1],$next_date[2]);

            //convert 9 to 09
            $jdf_time[0] =($jdf_time[0]>9)?$jdf_time[0]:'0'.$jdf_time[0];
            $jdf_time[1] =($jdf_time[1]>9)?$jdf_time[1]:'0'.$jdf_time[1];
            $jdf_time[2] =($jdf_time[2]>9)?$jdf_time[2]:'0'.$jdf_time[2];

            $next_jdf_date =implode('/',$jdf_time);

            //check no row with this info for other user that get turn
            $check_row=DB::table('turns_user')
                ->where('doctor_id','=',$doctor_row->id)
                ->where('day','=',$request->post('day'))
                ->where('date','=',$next_jdf_date)
                ->where('time','=',$request->post('time'))
                ->where('free_type','=','money');

            if ($check_row->count() == 1){
                $response['status'] = 'get_this_time';
                return json_encode($response);
            }


            //check exist this time in turns doctor table
            $check_row=DB::table('turns')
                ->where('user_id','=',$doctor_row->id)
                ->where('type','=','money');

            switch (ucfirst($request->post('day'))){
                case 'Saturday':
                    $check_row=$check_row->where('saturday','like','%'.$request->post('time').'%');
                    break;
                case 'Sunday':
                    $check_row=$check_row->where('sunday','like','%'.$request->post('time').'%');
                    break;
                case 'Monday':
                    $check_row=$check_row->where('monday','like','%'.$request->post('time').'%');
                    break;
                case 'Tuesday':
                    $check_row=$check_row->where('tuesday','like','%'.$request->post('time').'%');
                    break;
                case 'Wednesday':
                    $check_row=$check_row->where('wednesday','like','%'.$request->post('time').'%');
                    break;
                case 'Thursday':
                    $check_row=$check_row->where('thursday','like','%'.$request->post('time').'%');
                    break;
                case 'Friday':
                    $check_row=$check_row->where('friday','like','%'.$request->post('time').'%');
                    break;
            }


            if ($check_row->count() < 1){
                $response['status'] = 'not_exist_turns';
                return json_encode($response);
            }

            $price_type_turn=$setting->where('key','=',$request->post('type').'_price')->first()->value;




            $response['status'] = 'ok';
            $response['price'] = $price_type_turn;
            $response['wallet'] = $user_row->wallet;
            return json_encode($response);

        }
    }

    public function GetNewTurn(Request $request){
        if ($this->algoritms->discreate_mikhay($request->post('mikhay'))) {

            $user = User::where('users.id', '=', $this->algoritms->discreate_id($request->post('user_id')));

            $user_row = $this->algoritms->check_exist_row($user);

            $user = User::where('users.id', '=', $this->algoritms->discreate_id($request->post('doctor_id')));

            $doctor_row = $this->algoritms->check_exist_row($user);

            $setting=DB::table('setting');

            //require info
            $day_array=['Saturday'=>1,'Sunday'=>2,'Monday'=>3,'Tuesday'=>4,'Wednesday'=>5,'Thursday'=>6,'Friday'=>7];
            $today_day = date('l');
//            $today_date = date('Y/m/d');

            //find the date of send day
            $dif_days=  7-($day_array[ucfirst($today_day)] - $day_array[ucfirst($request->post('day'))]);

            $next_date = explode('-',date('Y-m-d', strtotime(' +'.$dif_days.' day')));


            $jdf_time = $this->jdf->gregorian_to_jalalii($next_date[0],$next_date[1],$next_date[2]);

            //convert 9 to 09
            $jdf_time[0] =($jdf_time[0]>9)?$jdf_time[0]:'0'.$jdf_time[0];
            $jdf_time[1] =($jdf_time[1]>9)?$jdf_time[1]:'0'.$jdf_time[1];
            $jdf_time[2] =($jdf_time[2]>9)?$jdf_time[2]:'0'.$jdf_time[2];

            $next_jdf_date =implode('/',$jdf_time);

            //check no row with this info for other user that get turn
            $check_row=DB::table('turns_user')
                ->where('doctor_id','=',$doctor_row->id)
                ->where('day','=',$request->post('day'))
                ->where('date','=',$next_jdf_date)
                ->where('time','=',$request->post('time'))
                ->where('free_type','=','money');

            if ($check_row->count() == 1){
                $response['status'] = 'get_this_time';
                return json_encode($response);
            }


            //check exist this time in turns doctor table
            $check_row=DB::table('turns')
                ->where('user_id','=',$doctor_row->id)
                ->where('type','=','money');

            switch (ucfirst($request->post('day'))){
                case 'Saturday':
                    $check_row=$check_row->where('saturday','like','%'.$request->post('time').'%');
                    break;
                case 'Sunday':
                    $check_row=$check_row->where('sunday','like','%'.$request->post('time').'%');
                    break;
                case 'Monday':
                    $check_row=$check_row->where('monday','like','%'.$request->post('time').'%');
                    break;
                case 'Tuesday':
                    $check_row=$check_row->where('tuesday','like','%'.$request->post('time').'%');
                    break;
                case 'Wednesday':
                    $check_row=$check_row->where('wednesday','like','%'.$request->post('time').'%');
                    break;
                case 'Thursday':
                    $check_row=$check_row->where('thursday','like','%'.$request->post('time').'%');
                    break;
                case 'Friday':
                    $check_row=$check_row->where('friday','like','%'.$request->post('time').'%');
                    break;
            }

            if ($check_row->count() < 1){
                $response['status'] = 'not_exist_turns';
                return json_encode($response);
            }
            $price_type_turn=$setting->where('key','=',$request->post('type').'_price')->first()->value;

            if ($user_row->wallet < $price_type_turn){
                $response['status'] = 'not_enough_wallet';
                return json_encode($response);
            }

            $update_wallet=DB::table('users')->where('id','=',$user_row->id)->update([
                'wallet'=>$user_row->wallet-$price_type_turn
            ]);

            if (!$update_wallet){
                $response['status'] = 'nook';
                return json_encode($response);
            }

            $insert_row=DB::table('turns_user')->insertGetId([
                'user_id'=>$user_row->id,
                'doctor_id'=>$doctor_row->id,
                'day'=>$request->post('day'),
                'time'=>$request->post('time'),
                'type'=>$request->post('type'),
                'free_type'=>$request->post('free_type'),
                'status'=>'get',
                'date'=>$next_jdf_date,

            ]);

            if ($insert_row){

                $insert_row2=DB::table('reports')->insertGetId([
                    'user_id' => $user_row->id,
                    'second_id' => $insert_row,
                    'type' => 'turn',
                    'price' => $price_type_turn,
                    'status'=> 'جاری',
                    'date' => $this->jdf->jdate('Y/m/d'),
                    'time' => $this->jdf->jdate('H:i:s'),
                    'day' => $this->jdf->jdate('l')

                ]);
                $insert_row3=DB::table('reports')->insertGetId([
                    'user_id' => $doctor_row->id,
                    'second_id' => $insert_row,
                    'type' => 'turn',
                    'price' => $price_type_turn,
                    'status'=> 'جاری',
                    'date' => $this->jdf->jdate('Y/m/d'),
                    'time' => $this->jdf->jdate('H:i:s'),
                    'day' => $this->jdf->jdate('l')

                ]);


                $insert_tr=DB::table('transaction')->insertGetId([
                    'user_id'=>$user_row->id,
                    'type'=>'dec',
                    'description'=>'خرید نوبت',
                    'value'=>$price_type_turn,
                    'date' => $this->jdf->jdate('Y/m/d'),
                    'time' => $this->jdf->jdate('H:i:s'),
                    'day' => $this->jdf->jdate('l')
                ]);

                if ($insert_row2 && $insert_tr && $insert_row3){
                    $response['status'] = 'ok';
                    return json_encode($response);
                }else{
                    $response['status'] = 'nook';
                    return json_encode($response);
                }
            }else{
                $response['status'] = 'nook';
                return json_encode($response);
            }

        }
    }

    public function SetReferer(Request $request){
        if ($this->algoritms->discreate_mikhay($request->post('mikhay'))) {

            $user = User::where('users.id', '=', $this->algoritms->discreate_id($request->post('user_id')));

            $user_row = $this->algoritms->check_exist_row($user);

//            $user = User::where('users.id', '=', $this->algoritms->discreate_id($request->post('doctor_id')));
//
//            $doctor_row = $this->algoritms->check_exist_row($user);

            $report_id=$request->post('id');
            $refer_id=$request->post('refer_id');

            $report_row=DB::table('reports')->where('id','=',$report_id)->first();

            $update=DB::table('turns_user')->where('id','=',$report_row->second_id)->update([
                'refer'=>$refer_id
            ]);

            if ($update){
                $response['status'] = 'ok';
                return json_encode($response);
            }else{
                $response['status'] = 'nook';
                return json_encode($response);
            }

        }
    }

    public function EndTrun(Request $request){
        if ($this->algoritms->discreate_mikhay($request->post('mikhay'))) {

            $user = User::where('users.id', '=', $this->algoritms->discreate_id($request->post('user_id')));

            $user_row = $this->algoritms->check_exist_row($user);

            $report_id=$request->post('id');
            $description=$request->post('description');

            $report_row=DB::table('reports')->where('id','=',$report_id)->update([
                'description'=>$description,
                'status'=>'end'
            ]);

            if ($report_row){
                $response['status'] = 'ok';
                return json_encode($response);
            }else{
                $response['status'] = 'nook';
                return json_encode($response);
            }

        }
    }

}
