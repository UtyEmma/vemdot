<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


trait Generics{
    public function random_string ($type = 'alnum', $len = 8 ){
        switch ( $type ){
            case 'alnum'	:
            case 'numeric'	:
            case 'nozero'	:
            case 'new_alnum':
                switch ($type){
                    case 'alnum'	:	$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        break;
                    case 'numeric'	:	$pool = '0123456789';
                        break;
                    case 'nozero'	:	$pool = '123456789';
                        break;
                    case 'new_alnum' : $pool = 'abcdefghijklmnopqrstuvwxyz';
                        break;
                }
                $str = '';
                $mdstr = md5 ( uniqid ( mt_rand () ) );
                $mdstrstrlen = strlen($mdstr);
                for ( $i=0; $i < $len; $i++ ){
                    $str .= substr ( $pool, mt_rand ( 0, strlen ( $pool ) -1 ), 1 );
                }
                return $str.substr($mdstr, 0, $mdstrstrlen/2);
            break;
            case 'unique' : return md5 ( uniqid ( mt_rand () ) );
                break;
        }
    }

    //create a unique id
    public function createUniqueId($table_name, $column = 'unique_id'){

        /*$unique_id = Controller::picker();*/
        $unique_id = $this->random_string();

        //check for the database count from the database"unique_id"
        $rowcount = DB::table($table_name)->where($column, $unique_id)->count();

        if($rowcount == 0){

            if(empty($unique_id)){
                $this->createUniqueId($table_name, $column);
            }else{
                return $unique_id;
            }

        }else{
            $this->createUniqueId($table_name, $column);
        }

    }

    public  function createConfirmationNumbers($table_name, $column, $length = 5){

        $unique = $this->createRandomNumber($length);

        //check for the database count from the database"unique_id"
        $rowcount = DB::table($table_name)->where($column, $unique)->count();

        if($rowcount == 0){
            return $unique;
        }else{
            $this->createConfirmationNumbers($table_name, $column, $length);
        }
    }

    public  function createRandomNumber($length){
        $random = "";
        srand((double) microtime() * 1000000);

        $data = "123456123456789071234567890890";
       // $data .= "aBCdefghijklmn123opq45rs67tuv89wxyz"; // if you need alphabetic also

        for ($i = 0; $i < $length; $i++) {
            $random .= substr($data, (rand() % (strlen($data))), 1);
        }

        return $random;

    }

    //function that changes an associative array to an object
    function returnObject(array $array){
        return json_decode(json_encode($array));
    }

    function percentageDiff($value, $percent){
        return $value - ($value * ($percent / 100));
    }
}
