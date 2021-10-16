<?php

namespace App\Http\Controllers;

use App\Functions\Algoritms;
use App\Functions\jdf;
use App\Functions\Settings;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Workshop;
use Illuminate\Support\Facades\DB;

class WorkshopController extends Controller
{

    private $algoritm;
    private $jdf;
    private $settings;
    function __construct(){
        $this->algoritm=new Algoritms();
        $this->jdf=new jdf();
        $this->settings=new Settings();
    }

    public function GiveWorkshop(Request $request){
        if ($this->algoritm->discreate_mikhay($request->post('mikhay'))) {

            if ($request->has('user_id') && !empty($request->post('user_id'))){
                $user = User::where('users.id', '=', $this->algoritm->discreate_id($request->post('user_id')));

                $user_row = $this->algoritm->check_exist_row($user);
            }else{
                $user_row = new \stdClass();
                $user_row->id =0;
                $user_row->wallet =0;
            }

            if (isset($_POST['page']) && !empty($_POST['page'])){
                $page=$_POST['page'];
            }else{
                $page=0;
            }

            $row= Workshop::where('status' , '=', '1')
                ->orderBy('id')
                ->paginate($this->settings->get_limit(),['*'], 'page', $page + 1);

            $res['pages'] = $row->lastPage()-1;
            $list =$row->items();

            foreach ($list as $row){

                $row->pic= $this->settings->get_pic_url().$row->pic;

                $check_not_buy_last=DB::table('reports')
                    ->where('second_id','=',$row->id)
                    ->where('user_id','=',$user_row->id)
                    ->where('type','=','workshop');

                if ($check_not_buy_last->count() == 1){
                    $row->lock=1;
                }else{
                    $row->lock=0;
                }

            }
            $res['wallet'] =$user_row->wallet;
            $res['list'] =$list;
            return json_encode($res);

        }
    }

    public function GetNewWorkshop(Request $request){
        if ($this->algoritm->discreate_mikhay($request->post('mikhay'))) {

            $user = User::where('users.id', '=', $this->algoritm->discreate_id($request->post('user_id')));

            $user_row = $this->algoritm->check_exist_row($user);


            $workshop=DB::table('workshops')->where('id','=',$request->post('workshop_id'));

            $workshop_row=$this->algoritm->check_exist_row($workshop);

            if ($user_row->wallet < $workshop_row->price){
                $response['status'] = 'not_enough_wallet';
                return json_encode($response);
            }

            $update_wallet=DB::table('users')->where('id','=',$user_row->id)->update([
                'wallet'=>$user_row->wallet-$workshop_row->price
            ]);

            if (!$update_wallet){
                $response['status'] = 'nook';
                return json_encode($response);
            }

            $check_not_buy_last=DB::table('reports')
                ->where('second_id','=',$workshop_row->id)
                ->where('user_id','=',$user_row->id)
                ->where('type','=','workshop');

            if ($check_not_buy_last->count() == 1){
                $response['status'] = 'you_are_get_this_workshop';
                return json_encode($response);
            }

            $insert_row=DB::table('reports')->insertGetId([
                'user_id' => $user_row->id,
                'second_id' => $workshop_row->id,
                'type' => 'workshop',
                'price' => $workshop_row->price,
                'status'=> 'جاری',
                'date' => $this->jdf->jdate('Y/m/d'),
                'time' => $this->jdf->jdate('H:i:s'),
                'day' => $this->jdf->jdate('l')

            ]);

            $insert_tr=DB::table('transaction')->insertGetId([
                'user_id'=>$user_row->id,
                'type'=>'dec',
                'description'=>'خرید کارگاه',
                'value'=>$workshop_row->price,
                'date' => $this->jdf->jdate('Y/m/d'),
                'time' => $this->jdf->jdate('H:i:s'),
                'day' => $this->jdf->jdate('l')
            ]);

            if ($insert_row && $insert_tr){
                $response['status'] = 'ok';
                return json_encode($response);
            }else{
                $response['status'] = 'nook';
                return json_encode($response);
            }
        }
    }
}
