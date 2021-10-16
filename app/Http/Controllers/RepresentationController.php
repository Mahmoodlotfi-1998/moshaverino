<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Functions\Algoritms;
use App\Functions\jdf;
use App\Models\Representation;

class RepresentationController extends Controller
{
    private $algoritm;
    private $jdf;
    function __construct(){
        $this->algoritm=new Algoritms();
        $this->jdf=new jdf();
    }

    public function Give(Request $request){

        if ($this->algoritm->discreate_mikhay($request->post('mikhay'))){

            $row=Representation::insertGetId([
                'full_name'=>$request->post('full_name'),
                'phone'=>$request->post('phone'),
                'city'=>$request->post('city'),
                'education'=>$request->post('education'),
                'description'=>$request->post('description'),
                'rezome_photo'=>$request->post('rezome_photo'),
                'education_cv'=>$request->post('education_cv'),
                'natural_photo'=>$request->post('natural_photo'),
                'time_insert'=>$this->jdf->jdate('Y/m/d'),
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
}
