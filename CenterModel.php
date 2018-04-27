<?php

/**
 * Created by PhpStorm.
 * User: PMTB
 * Date: 10/3/2017
 * Time: 11:37 AM
 */
class CenterModel
{
    public static function getCenterInfo($id){
        $sql = "SELECT id, center_name, effect_id FROM center WHERE id=$id";
        $res = DB::fetch($sql);
        return $res;
    }

    public static function getAllCenter(){
	$user = $_SESSION['user'];
	$where = '';
	if ($user['role'] == 2){
	$where = "AND id = ".$user['center_id'];
}
        $sql = "SELECT agents.id AS id, agents.address as address FROM agents WHERE agents.is_active=1 $where ORDER BY address ASC";
        $res = DB::fetch_all($sql);
        return $res;
    }
}