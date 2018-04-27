<?php

/**
 * Created by PhpStorm.
 * User: PMTB
 * Date: 11/14/2017
 * Time: 11:54 AM
 */
class CityModel
{
    public static function getCityInfo($id){
        $sql = "SELECT id, `name` FROM city WHERE id=$id";
        $res = DB::fetch($sql);
        return $res;
    }

    public static function getAllCity(){
        $sql = "SELECT * FROM city ORDER BY `name` ASC";
        $res = DB::fetch_all($sql);
        return $res;
    }
}