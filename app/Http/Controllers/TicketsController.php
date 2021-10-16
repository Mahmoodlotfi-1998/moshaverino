<?php

namespace App\Http\Controllers;

use App\Functions\Algoritms;
use App\Functions\jdf;
use App\Functions\Settings;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Tickets;

class TicketsController extends Controller
{
    private $algoritm;
    private $jdf;
    private $settings;
    function __construct(){
        $this->algoritm=new Algoritms();
        $this->jdf=new jdf();
        $this->settings=new Settings();
    }

    public function AddTicket(Request $request){
        if ($this->algoritm->discreate_mikhay($request->post('mikhay'))){

            $user=User::where('id','=',$this->algoritm->discreate_id($request->post('user_id')));

            $user_row=$this->algoritm->check_exist_row($user);

            $row=Tickets::insertGetId([
                'user_id'=>$user_row->id,
                'title'=>$request->post('title'),
                'description'=>$request->post('description'),
            ]);

            if ($row){
                $res['status']='ok';
                return json_encode($res);
            }else{
                $res['status']='nook';
                return json_encode($res);
            }

        }
    }

    public function GiveTicket(Request $request){
        if ($this->algoritm->discreate_mikhay($request->post('mikhay'))) {

            $user = User::where('id', '=', $this->algoritm->discreate_id($request->post('user_id')));

            $user_row = $this->algoritm->check_exist_row($user);

            if (isset($_POST['page']) && !empty($_POST['page'])){
                $page=$_POST['page'];
            }else{
                $page=0;
            }

            $row=Tickets::select('title','description')
                ->where('user_id','=',$user_row->id)
                ->paginate($this->settings->get_limit(),['*'], 'page', $page + 1)
                ;

            $res['pages'] = $row->lastPage()-1;
            $res['list'] =$row->items();

            return json_encode($res);

        }
    }
}
