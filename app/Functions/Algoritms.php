<?php
namespace App\Functions;

class Algoritms
{

    function discreate_id($id){
        $h13_12 = base_convert($id, 13, 12);
        $h12_16 = base_convert($h13_12, 12, 16);
        $h16_5 = base_convert($h12_16, 16, 5);
        $h5_10 = base_convert($h16_5, 5, 10);
        $ids=$h5_10/19;
        return $ids;
    }

    function create_id($id){
        $use=$id*19;
        $h10_5=base_convert($use,10,5);
        $h5_16=base_convert($h10_5,5,16);
        $h16_12=base_convert($h5_16,16,12);
        $h12_13=base_convert($h16_12,12,13);
        return $h12_13;
    }

    function discreate_mikhay($mikhay){
        $mikl='hi';
        if($mikhay== $mikl){
            return true;
        }else{
            exit();
        }
    }

    function check_exist_row($row){
        if ($row->count() ==1){
            return $row->first();
        }else{
            exit();
        }
    }
}


?>
