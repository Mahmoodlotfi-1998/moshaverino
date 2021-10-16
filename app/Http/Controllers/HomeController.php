<?php

namespace App\Http\Controllers;

use App\Functions\Algoritms;
use App\Functions\jdf;
use App\Functions\Settings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    private $algoritms;
    private $jdf;
    private $settings;
    function __construct(){
        $this->algoritms=new Algoritms();
        $this->jdf=new jdf();
        $this->settings=new Settings();
    }

    public function UploadPhoto(Request $request){
        if ($this->algoritms->discreate_mikhay($request->post('mikhay'))) {
            if ($request->hasFile('image')) {
                //  Let's do everything here
                if ($request->file('image')->isValid()) {

                    $extension = $request->image->extension();
                    $output=time().$request->file('image')->getFilename().".".$extension;

                    if ($request->image->storeAs('/user', $output)){
                        $response['url'] = $output;
                        return json_encode($response);
                    }else{
                        $response['url'] = '0';
                        return json_encode($response);
                    }

                }
            }
        }
    }

    public function GetPhoto(Request $request,$name){
        $contents = Storage::get('user/'.$name);
        return response($contents)->header('Content-type','image/png');
    }

    public function GetGeneralPhoto(Request $request,$path,$name){
        $contents = Storage::get($path.'/'.$name);
        return response($contents)->header('Content-type','image/png');
    }

    public function PageAbout(){
        $law=DB::table('setting')
            ->where('key','=','about')
            ->first();
        return view('pages.about')->with(['data'=>$law->value]);
    }

    public function GetRegisterRequirement(){
        $education=DB::table('education')->get();

        return json_encode($education);
    }

    public function GetCity(){
        $centers_row=DB::table('city')
            ->where('parent_id','=','0')
            ->get();

        $i=0;
        $centers=[];
        foreach ($centers_row as $row){

            $center['parent']=$row->title;
            $city_row=DB::table('city')
                ->select('id','title as name')
                ->where('parent_id','=',$row->id)
                ->get();

            $center['child']=$city_row;
            array_push($centers,$center);
            $i++;
        }

        return json_encode($centers);
    }

    public function Filter(Request $request){

        if ($this->algoritms->discreate_mikhay($request->post('mikhay'))) {

            if ($request->has('page') && !empty($request->post('page'))){
                $page=$request->post('page');
            }else{
                $page=0;
            }

            $row=DB::table('users')
                ->select('users.id','full_name','city.title as city','cat_id','collage','star','photo','number_star as count')
                ->join('city','users.city','=','city.id')
                ->where('cat_id','=',$request->post('category'))
                ->join('doctors','users.id','=','user_id');

            if ($request->post('order') == 1){
                $row=$row->orderBy('star','desc');
            }

            if ($request->post('city') != 0){
                $row=$row->where('city','=',$request->post('city'));
            }

            if ($request->has('search') && !empty($request->post('search'))){
                $row=$row->where('full_name','like','%'.$request->post('search').'%');
            }

            $row=$row->paginate($this->settings->get_limit(),['*'], 'page', $page + 1);

            $res['pages'] = $row->lastPage()-1;
            $list =$row->items();

            foreach ($list as $row){

                $row->photo= $this->settings->get_pic_url().$row->photo;

                $row->id = $this->algoritms->create_id($row->id);

            }
            $res['list'] =$list;

            return json_encode($res);
        }

    }

    public function MainPage(Request $request){

        $slider_row=DB::table('main_slider')
            ->select('pic')
            ->get();

        foreach ($slider_row as $row){

            $row->pic= $this->settings->get_pic_url().$row->pic;

        }

        $doctors_row=DB::table('users')
            ->select('users.id','full_name','city.title as city','cat_id','collage','star','users.photo','number_star as count')
            ->join('doctors','users.id','=','user_id')
            ->join('city','users.city','=','city.id')
            ->orderBy('star','desc')
            ->limit(10)
            ->get();

        foreach ($doctors_row as $row){

            $row->photo= $this->settings->get_pic_url().$row->photo;
            $row->id = $this->algoritms->create_id($row->id);

        }


        if ($request->has('user_id') && !empty($request->post('user_id'))){
            $user = User::where('users.id', '=', $this->algoritms->discreate_id($request->post('user_id')));

            $user_row = $this->algoritms->check_exist_row($user);

            $turn_row = DB::table('turns_user')
                        ->select('turns_user.id as row_id','doctors.collage','users.photo','users.full_name')
                        ->join('users','turns_user.doctor_id','=','users.id')
                        ->join('doctors','turns_user.doctor_id','=','doctors.user_id')
                        ->where('turns_user.user_id','=',$user_row->id)
                        ->where('turns_user.status','=','end');

            if ($turn_row->count() > 0){
                $list=$turn_row->first();
                $list->photo=$this->settings->get_pic_url().$list->photo;
                $res['row']=$list;

            }else{
                $res['row']=['row_id'=>0];
            }

        }else{
            $res['row']=['row_id'=>0];
        }

        $res['slider']=$slider_row;
        $res['doctors']=$doctors_row;
        $res['version']='1.0.0';
        $res['url']='https://moshaverinoapp.ir/dl/app.apk';


        return json_encode($res);

    }

    public function DoctorInfo(Request $request){
        if ($this->algoritms->discreate_mikhay($request->post('mikhay'))) {

            $user = User::where('users.id', '=', $this->algoritms->discreate_id($request->post('user_id')));

            $user_row = $this->algoritms->check_exist_row($user);

            $user = User::where('users.id', '=', $this->algoritms->discreate_id($request->post('doctor_id')));

            $doctor_row = $this->algoritms->check_exist_row($user);

            $row = DB::table('users')
                ->select( 'full_name', 'city.title as city', 'cat_id', 'star', 'doctors.collage', 'address', 'photo', 'number_star as count','address')
                ->join('doctors', 'users.id', '=', 'user_id')
                ->join('city', 'users.city', '=', 'city.id')
                ->where('users.id', '=', $doctor_row->id)->first();

            $row->photo= $this->settings->get_pic_url().$row->photo;

            $check_save=DB::table('doctor_save')
                ->where('user_id','=',$user_row->id)
                ->where('doctor_id','=',$doctor_row->id);

            if ($check_save->count() == 1){
                $row->save=1;
            }else{
                $row->save=0;
            }

            $res['doctor_info']=$row;


            $setting['voice_call_price']=DB::table('setting')
                ->where('key','=','voice_call_price')->first()->value;
            $setting['video_call_price']=DB::table('setting')
                ->where('key','=','video_call_price')->first()->value;
            $setting['present_price']=DB::table('setting')
                ->where('key','=','present_price')->first()->value;

            $res['prices']=$setting;

            $row = DB::table('comments')
                ->select('full_name', 'comments.star', 'comments.description', 'photo')
                ->join('users', 'users.id', '=', 'user_id')
                ->where('comments.doctor_id', '=', $doctor_row->id)
                ->limit(10);

            if ($row->count() >0){

                $list=$row->get();
                foreach ($list as $row){
                    $row->photo= $this->settings->get_pic_url().$row->photo;
                }
                $res['comments']=$list;

            }else{
                $res['comments']=[];
            }
            return json_encode($res);
        }
    }

    public function GetDoctorComment(Request $request){

        if ($this->algoritms->discreate_mikhay($request->post('mikhay'))) {

            $user = User::where('users.id', '=', $this->algoritms->discreate_id($request->post('doctor_id')));

            $doctor_row = $this->algoritms->check_exist_row($user);

            if (isset($_POST['page']) && !empty($_POST['page'])){
                $page=$_POST['page'];
            }else{
                $page=0;
            }

            $row = DB::table('comments')
                ->select('full_name', 'comments.star', 'comments.description', 'photo')
                ->join('users', 'users.id', '=', 'user_id')
                ->where('comments.doctor_id', '=', $doctor_row->id)
                ->paginate($this->settings->get_limit(),['*'], 'page', $page + 1);

            $res['pages'] = $row->lastPage()-1;
            $list =$row->items();

            foreach ($list as $row) {
                $row->photo = $this->settings->get_pic_url() . $row->photo;
            }
            $res['list'] = $list;
            return json_encode($res);

        }
    }

    public function InsertDoctorComment(Request $request){
        if ($this->algoritms->discreate_mikhay($request->post('mikhay'))) {

            $user = User::where('users.id', '=', $this->algoritms->discreate_id($request->post('user_id')));

            $user_row = $this->algoritms->check_exist_row($user);



            $user = DB::table('turns_user')->where('id', '=', $request->post('row_id'));

            $user->update([
                'status'=>'set'
            ]);

            $turn_row = $this->algoritms->check_exist_row($user);
            if ($request->post('score') == 0){
                $res['status']='ok';
                return json_encode($res);
            }
            else if ($request->has('score') && !empty($request->post('score'))){
                $score=$request->post('score');

                $doctor_table=DB::table('doctors')->where('user_id','=',$turn_row->doctor_id)->first();
                $last_score=$doctor_table->star;
                $last_number=$doctor_table->number_star;

                $sum_last=$last_number*$last_score;

                $new_number=$last_number+1;
                $new_score_sum=$sum_last+$score;

                $new_score_avg=floor($new_score_sum/$new_number);

                $update_row=DB::table('doctors')
                    ->where('user_id','=',$turn_row->doctor_id)
                    ->update([
                        'star'=>$new_score_avg,
                        'number_star'=>$new_number
                ]);

                if ($update_row){
                    $insert_row=DB::table('comments')->insertGetId([
                        'user_id'=>$user_row->id,
                        'doctor_id'=>$turn_row->doctor_id,
                        'star'=>$score,
                        'description'=>$request->post('description'),
                    ]);

                    if ($insert_row){
                        $res['status']='ok';
                        return json_encode($res);
                    }else{
                        $res['status']='nook';
                        return json_encode($res);
                    }
                }
            }

        }

    }

    public function GetWalletPage(Request $request){
        if ($this->algoritms->discreate_mikhay($request->post('mikhay'))) {

            $user = User::where('users.id', '=', $this->algoritms->discreate_id($request->post('user_id')));

            $user_row = $this->algoritms->check_exist_row($user);

            if (isset($_POST['page']) && !empty($_POST['page'])){
                $page=$_POST['page'];
            }else{
                $page=0;
            }

            $res['wallet']=$user_row->wallet;

            $tr_row=DB::table('transaction')
                ->select('type','description','value','time','day','date')
                ->where('user_id','=',$user_row->id)
                ->paginate($this->settings->get_limit(),['*'], 'page', $page + 1);

            $res['pages'] = $tr_row->lastPage()-1;
            $res['list'] =$tr_row->items();

            return json_encode($res);

        }
    }

    public function DoctorSave(Request $request){
        if ($this->algoritms->discreate_mikhay($request->post('mikhay'))) {

            $user = User::where('users.id', '=', $this->algoritms->discreate_id($request->post('user_id')));

            $user_row = $this->algoritms->check_exist_row($user);

            switch ($request->post('action')){
                case 'insert':
                    $user = User::where('users.id', '=', $this->algoritms->discreate_id($request->post('doctor_id')));

                    $doctor_row = $this->algoritms->check_exist_row($user);
                    //todo get free_type
                    $select_row=DB::table('doctor_save')
                        ->where('user_id','=',$user_row->id)
                        ->where('doctor_id','=',$doctor_row->id);

                    if ($select_row->count() == 1){
                        $res['status']='ok';
                        return json_encode($res);
                    }


                    $insert_row=DB::table('doctor_save')->insertGetId([
                        'user_id'=>$user_row->id,
                        'doctor_id'=>$doctor_row->id
                    ]);

                    if ($insert_row){
                        $res['status']='ok';
                        return json_encode($res);
                    }else{
                        $res['status']='nook';
                        return json_encode($res);
                    }


                case 'delete':
                    $user = User::where('users.id', '=', $this->algoritms->discreate_id($request->post('doctor_id')));

                    $doctor_row = $this->algoritms->check_exist_row($user);

                    $select_row=DB::table('doctor_save')
                        ->where('user_id','=',$user_row->id)
                        ->where('doctor_id','=',$doctor_row->id);

                    if ($select_row->count() == 1){
                        if( $select_row->delete() ){
                            $res['status']='ok';
                            return json_encode($res);
                        }else{
                            $res['status']='nook';
                            return json_encode($res);
                        }
                    }
                    break;

                case 'get':

                    if (isset($_POST['page']) && !empty($_POST['page'])){
                        $page=$_POST['page'];
                    }else{
                        $page=0;
                    }

                    $row=DB::table('doctor_save')
                        ->select('users.id','full_name','city.title as city','cat_id','collage','star','users.photo','number_star as count')
                        ->where('doctor_save.user_id','=',$user_row->id)
                        ->join('users','users.id','=','doctor_save.doctor_id')
                        ->join('doctors','doctors.user_id','=','doctor_save.doctor_id')
                        ->join('city','users.city','=','city.id')->paginate($this->settings->get_limit(),['*'], 'page', $page + 1);

                    $res['pages'] = $row->lastPage()-1;
                    $list =$row->items();

                    foreach ($list as $row) {
                        $row->photo = $this->settings->get_pic_url() . $row->photo;
                        $row->id = $this->algoritms->create_id($row->id);
                    }
                    $res['list'] = $list;
                    return json_encode($res);
            }
        }
    }
}
