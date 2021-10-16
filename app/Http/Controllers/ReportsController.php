<?php

namespace App\Http\Controllers;

use App\Functions\Algoritms;
use App\Functions\jdf;
use App\Functions\Settings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{

    private $algoritm;
    private $jdf;
    private $settings;

    function __construct(){
        $this->algoritm=new Algoritms();
        $this->jdf=new jdf();
        $this->settings=new Settings();
    }

    public function GetReports(Request $request){
        if ($this->algoritm->discreate_mikhay($request->post('mikhay'))) {

            $user = User::where('users.id', '=', $this->algoritm->discreate_id($request->post('user_id')));

            $user_row = $this->algoritm->check_exist_row($user);

            if (isset($_POST['page']) && !empty($_POST['page'])) {
                $page = $_POST['page'];
            } else {
                $page = 0;
            }

            if ($user_row->type == 'norm'){
                //اگر کاربر عادی باشد باید اطلاعات پزشک نمایش داده شود. در غیر این صورت اطلاعات کاربر باید نمایش داده شود.

                $turns_report = DB::table('reports')
                    ->select(DB::raw('reports.id,users.full_name as title,doctors.cat_id as title2,reports.day,reports.date,reports.time,reports.price,reports.type,reports.status,reports.time_insert,turns_user.type as visit,"null" as url,"null" as latitude,"null" as longitude'))
                    ->where('reports.user_id', '=', $user_row->id)
                    ->where('reports.type', '=', 'turn')
                    ->join('turns_user', 'turns_user.id', '=', 'reports.second_id')
                    ->join('users', 'turns_user.doctor_id', '=', 'users.id')
                    ->join('doctors', 'doctors.user_id', '=', 'users.id');


            }else{

                $turns_report = DB::table('reports')
                    ->select(DB::raw('reports.id,users.full_name as title,"null" as title2,reports.day,reports.date,reports.time,reports.price,reports.type,reports.status,reports.time_insert,turns_user.type as visit,"null" as url,"null" as latitude,"null" as longitude'))
                    ->where('reports.user_id', '=', $user_row->id)
                    ->where('reports.type', '=', 'turn')
                    ->join('turns_user', 'turns_user.id', '=', 'reports.second_id')
                    ->join('users', 'turns_user.user_id', '=', 'users.id');

            }


            $workshops_report = DB::table('reports')
                ->select(DB::raw('reports.id,workshops.title as title,workshops.author as title2,reports.day,reports.date,reports.time,reports.price,reports.type,reports.status,reports.time_insert,"null" as visit,workshops.url,latitude,longitude'))
                ->where('reports.user_id', '=', $user_row->id)
                ->where('reports.type', '=', 'workshop')
                ->join('workshops', 'workshops.id', '=', 'reports.second_id');

            $products_report =  DB::table('reports')
                ->select(DB::raw('reports.id,products.title as title,products.author as title2,reports.day,reports.date,reports.time,reports.price,reports.type,reports.status,reports.time_insert,"null" as visit,products.url,"null" as latitude,"null" as longitude'))
                ->where('reports.user_id', '=', $user_row->id)
                ->where('reports.type', '=', 'product')
                ->join('products', 'products.id', '=', 'reports.second_id');


            $final_report = $turns_report
                ->union($workshops_report)
                ->union($products_report)
                ->orderBy('time_insert', 'desc')
                ->paginate($this->settings->get_limit(), ['*'], 'page', $page + 1);

            $res['pages'] = $final_report->lastPage() - 1;
            $list = $final_report->items();

            foreach ($list as $row) {

                switch ($row->type) {
                    case 'workshop':
                        $photo = 'reports_pic/workshop.png';
                        break;

                    case 'turn':
                        $photo = 'reports_pic/turn_user.png';
                        break;

                    case 'product':
                        $photo = 'reports_pic/product.png';
                        break;

                    case 'referer':
                        $photo = 'reports_pic/turn_user.png';
                        break;
                }

                $row->photo = $this->settings->get_pic_url() . $photo;

            }

            $res['list'] = $list;


            return json_encode($res);
        }
    }

    public function GetReportPage(Request  $request){
        if ($this->algoritm->discreate_mikhay($request->post('mikhay'))) {

            $user = User::where('users.id', '=', $this->algoritm->discreate_id($request->post('user_id')));

            $user_row = $this->algoritm->check_exist_row($user);

            $report_id=$request->post('id');
//            echo $user_row->type;
            if ($user_row->type == 'norm'){
//ok
                $turns_report = $check_not_buy_last = DB::table('reports')
                    ->select(DB::raw('reports.id,users.full_name as title,doctors.address,turns_user.type,doctors.cat_id as title2,reports.day,reports.date,reports.price,reports.time,reports.price,reports.type,reports.status,reports.time_insert,turns_user.type as visit,description,refer,doctors.whatsapp_phone'))
                    ->where('reports.id', '=', $report_id)
                    ->where('reports.type', '=', 'turn')
                    ->join('turns_user', 'turns_user.id', '=','reports.second_id')
                    ->join('users', 'turns_user.doctor_id', '=', 'users.id')
                    ->join('doctors', 'doctors.user_id', '=','users.id');

            }else{
//,doctors.address,doctors.whatsapp_phone
                $turns_report = $check_not_buy_last = DB::table('reports')
                    ->select(DB::raw('reports.id,users.full_name as title,"null" as title2,reports.day,reports.date,reports.price,reports.time,reports.price,reports.type as type2,reports.status,reports.time_insert,turns_user.type as type,description,refer'))
                    ->where('reports.id', '=', $report_id)
                    ->where('reports.type', '=', 'turn')
                    ->join('turns_user', 'turns_user.id', '=', 'reports.second_id')
                    ->join('users', 'turns_user.user_id', '=', 'users.id');


            }
            $turns_report=$turns_report->first();

//            $test['id'] = 1;
//            $test['title'] = 1;
//            $test['title2'] = 1;
//            $test['address'] = 1;
//            $test['price'] = 1;
//            $test['time'] = 1;
//            $test['date'] = 1;
//            $test['day'] = 'شنبه';
//            $test['type'] = 'voice_call';
//            $test['visit'] = 1;
//            $test['status'] = 'جاری';
//            $test['refer'] = 1;
//            $test['whatsapp_phone'] = '09139758529';


            return json_encode($turns_report);


        }
    }

    public function GetRefererDoctor(Request $request){
        if ($this->algoritm->discreate_mikhay($request->post('mikhay'))) {

            $user = User::where('users.id', '=', $this->algoritm->discreate_id($request->post('user_id')));

            $user_row = $this->algoritm->check_exist_row($user);

            if ($request->has('page') && !empty($request->post('page'))){
                $page=$request->post('page');
            }else{
                $page=0;
            }

            switch ($request->post('type')){
                case 'get_all':
                    $row = DB::table('users')
                        ->select('users.id', 'full_name', 'city.title as city', 'cat_id', 'collage', 'star', 'photo', 'number_star as count')
                        ->join('city', 'users.city', '=', 'city.id')
//                ->where('cat_id', '=', $request->post('category'))
                        ->join('doctors', 'users.id', '=', 'user_id');

                    break;

                case 'search':
                    $row = DB::table('users')
                        ->select('users.id', 'full_name', 'city.title as city', 'cat_id', 'collage', 'star', 'photo', 'number_star as count')
                        ->join('city', 'users.city', '=', 'city.id')
                        ->where('full_name', 'like', '%'.$request->post('name').'%')
//                ->where('cat_id', '=', $request->post('category'))
                        ->join('doctors', 'users.id', '=', 'user_id');

                    break;
            }
            $row = $row->paginate($this->settings->get_limit(),['*'], 'page', $page + 1);

            $res['pages'] = $row->lastPage()-1;
            $list =$row->items();

            foreach ($list as $row){

                $row->photo= $this->settings->get_pic_url().$row->photo;

                $row->id = $this->algoritm->create_id($row->id);

            }
            $res['list'] =$list;
            return json_encode($res);

        }
    }

}
