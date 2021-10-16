<?php

namespace App\Http\Controllers;

use App\Functions\Algoritms;
use App\Functions\jdf;
use App\Functions\Settings;
use App\Models\Doctors;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    private $algoritms;
    private $jdf;
    private $settings;

    function __construct(){
        $this->algoritms=new Algoritms();
        $this->jdf=new jdf();
        $this->settings=new Settings();

    }

    public function Register(Request $request)
    {

        if ($this->algoritms->discreate_mikhay($request->post('mikhay'))) {
            $row_insert=0;
            switch ($request->post('action')){
                case 'norm':
                    if (!$request->has('photo')){
                        $photo=0;
                    }else{
                        $photo=$request->post('photo');
                    }
                    $row_insert=User::insertGetId([
                        'phone'=>$request->post('phone'),
                        'full_name'=>$request->post('full_name'),
                        'natural_code'=>$request->post('natural_code'),
                        'city'=>$request->post('city'),
                        'education'=>$request->post('education'),
                        'gender'=>$request->post('gender'),
                        'photo'=>$photo,
                        'type'=>'norm',
                        'time_insert'=>$this->jdf->jdate('Y/m/d'),
                    ]);

                    break;

                case 'doctor':
                    $row_insert=User::insertGetId([
                        'phone'=>$request->post('phone'),
                        'full_name'=>$request->post('full_name'),
                        'natural_code'=>$request->post('natural_code'),
                        'city'=>$request->post('city'),
                        'education'=>$request->post('education'),
                        'gender'=>$request->post('gender'),
                        'photo'=>$request->post('photo'),
                        'type'=>'doctor',
                        'time_insert'=>$this->jdf->jdate('Y/m/d'),
                    ]);

                    if ($row_insert){
                        $doctor=Doctors::insertGetId([
                            'user_id'=>$row_insert,
                            'cat_id'=>$request->post('expert'),
                            'address'=>$request->post('address'),
                            'collage'=>$request->post('collage'),
                            'whatsapp_phone'=>$request->post('whatsapp_phone'),
                            'shaba'=>$request->post('shaba'),
                            'rezome_photo'=>$request->post('rezome_photo'),
                            'education_cv'=>$request->post('education_cv'),
                            'natural_photo'=>$request->post('natural_photo'),
                        ]);
                        if (!$doctor){
                            $response['status'] = 'nook';
                            return json_encode($response);
                        }
                    }

                    break;
            }

            if ($row_insert){
                $response['status'] = 'ok';
                $response['photo'] = $this->settings->get_pic_url().$request->post('photo');
                $response['user_id'] = $this->algoritms->create_id($row_insert);
                return json_encode($response);
            }else{
                $response['status'] = 'nook';
                return json_encode($response);
            }




        }
    }

    public function NormInfo(Request $request)
    {
        if ($this->algoritms->discreate_mikhay($request->post('mikhay'))) {

            $user = User::
            select(
                'users.id'
                ,'full_name'
                ,'natural_code'
                ,'city'
                ,'education'
                ,'gender'
                ,'photo'
                ,'city.title as city_name'
                ,'education.title as education_name'
            )
            ->where('users.id', '=', $this->algoritms->discreate_id($request->post('user_id')))
            ->join('city','users.city','=','city.id')
            ->join('education','users.education','=','education.id');

            $user_row = $this->algoritms->check_exist_row($user);

            $user_row->photo_string=$user_row->photo;
            $user_row->photo= $this->settings->get_pic_url(). $user_row->photo;

            return json_encode($user_row);
        }
    }

    public function NormEdit(Request $request)
    {
        if ($this->algoritms->discreate_mikhay($request->post('mikhay'))) {

            $user = User::where('id', '=', $this->algoritms->discreate_id($request->post('user_id')));

            $user_row = $this->algoritms->check_exist_row($user);

            $check=$user->update([
                'full_name'=>$request->post('full_name'),
                'city'=>$request->post('city'),
                'natural_code'=>$request->post('natural_code'),
                'education'=>$request->post('education'),
                'gender'=>$request->post('gender'),
                'photo'=>$request->post('photo'),
            ]);

            if($check){
                $response['status'] = 'ok';
                $response['photo'] = $this->settings->get_pic_url().$request->post('photo');

                return json_encode($response);
            }else{
                $response['status'] = 'nook';
                return json_encode($response);
            }
        }
    }

    public function DoctorInfo(Request $request)
    {
        if ($this->algoritms->discreate_mikhay($request->post('mikhay'))) {

            $user = User::where('id', '=', $this->algoritms->discreate_id($request->post('user_id')));

            $user_row = $this->algoritms->check_exist_row($user);

            $row=DB::table('users')
                ->select(
                    'full_name'
                    ,'cat_id'
                    ,'natural_code'
                    ,'city'
                    ,'address'
                    ,'collage'
                    ,'education'
                    ,'gender'
                    ,'photo'
                    ,'city.title as city_name'
                    ,'education.title as education_name'
                    ,'whatsapp_phone'
                    ,'shaba'
                    ,'rezome_photo'
                    ,'education_cv'
                    ,'natural_photo'
                )
                ->where('users.id','=',$user_row->id)
                ->join('doctors','users.id','=','user_id')
                ->join('city','users.city','=','city.id')
                ->join('education','users.education','=','education.id')
                ->first();


            $row->photo_string= $row->photo;
            $row->rezome_photo_string= $row->rezome_photo;
            $row->education_cv_string= $row->education_cv;
            $row->natural_photo_string= $row->natural_photo;

            $row->photo= $this->settings->get_pic_url(). $row->photo;
            $row->rezome_photo= $this->settings->get_pic_url(). $row->rezome_photo;
            $row->education_cv= $this->settings->get_pic_url(). $row->education_cv;
            $row->natural_photo= $this->settings->get_pic_url(). $row->natural_photo;

            return json_encode($row);

        }
    }

    public function DoctorEdit(Request $request)
    {
        if ($this->algoritms->discreate_mikhay($request->post('mikhay'))) {

            $user = User::where('id', '=', $this->algoritms->discreate_id($request->post('user_id')));

            $user_row = $this->algoritms->check_exist_row($user);

            $row=DB::table('users')
                ->where('users.id','=',$user_row->id)
                ->join('doctors','users.id','=','user_id');

            $check=$row->update([
                'full_name'=>$request->post('full_name'),
                'city'=>$request->post('city'),
                'address'=>$request->post('address'),
                'collage'=>$request->post('collage'),
                'natural_code'=>$request->post('natural_code'),
                'education'=>$request->post('education'),
                'gender'=>$request->post('gender'),
                'cat_id'=>$request->post('expert'),
                'whatsapp_phone'=>$request->post('whatsapp_phone'),
                'shaba'=>$request->post('shaba'),
                'rezome_photo'=>$request->post('rezome_photo'),
                'photo'=>$request->post('photo'),
                'education_cv'=>$request->post('education_cv'),
                'natural_photo'=>$request->post('natural_photo'),
            ]);

            if($check){
                $response['status'] = 'ok';
                $response['photo'] = $this->settings->get_pic_url().$request->post('photo');

                return json_encode($response);
            }else{
                $response['status'] = 'nook';
                return json_encode($response);
            }
        }
    }



}
