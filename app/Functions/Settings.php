<?php
namespace App\Functions;

class Settings
{
    private $base_url='https://app.moshaverinoapp.ir/';
    private $pic_url='api/photo/';
    private $limit=10;

    function get_base_url(){

        return $this->base_url;
    }
    function get_pic_url(){

        return $this->base_url.$this->pic_url;
    }
    function get_limit(){

        return $this->limit;
    }

}

//php artisan make:model LM_table -m -c
//php artisan migrate



?>
