<?php

/**
 * Created by PhpStorm.
 * User: PMTB
 * Date: 10/4/2017
 * Time: 10:11 AM
 */
class Statistic
{
    public function getTransactions($query="",$search=""){
        $sql = "SELECT id,phone, `name`, parent, center, product_id, tuition_fee, created_time, code, status, connection_key, mode, presenter_name,presenter_phone FROM events_register";

        $sql .= $this->processConditions($search);

        $sql .= " ORDER BY created_time DESC";

        if($query['limit']){
            $sql .= " LIMIT ".$query['limit'];
        }


//        echo ($sql);
//        var_dump($search);
        $res = DB::fetch_all($sql);
        return $res;
    }


    public function getCodes($query=""){
        $sql = "SELECT code FROM codes WHERE status=0 AND event_code='DULICHHE2018'";

        if($query['limit']){
            $sql .= " LIMIT ".$query['limit'];
        }
        $res = DB::fetch_all($sql);
        return $res;
    }

    public function countTransactions($search=""){
        $sql = "SELECT IF(COUNT(id), COUNT(id),0) AS count_transaction FROM events_register";
        $sql .= $this->processConditions($search);
        $res = DB::fetch($sql);
        return $res['count_transaction'];
    }
    public function countCodes(){
        $sql = "SELECT IF(COUNT(id), COUNT(id),0) AS count_transaction FROM codes WHERE status=0 AND event_code='DULICHHE2018'";
        $res = DB::fetch($sql);
        return $res['count_transaction'];
    }

    private function processConditions($search){
        if($search){
            $conditions = array();
            if($search['event_code']){
                $event_code = trim($search['event_code']);
                $conditions[] = "event_code='$event_code'";
            }
            if($search['phone']){
                $phone = trim($search['phone']);
                $conditions[] = "phone LIKE '%$phone%'";
            }
            if($search['center']){
                $center = $search['center'];
                $conditions[] = "center = $center";
            }
            if($search['name']){
                $student_name = trim($search['name']);
                $conditions[] = "name LIKE '%$student_name%'";
            }
            if($search['code']){
                $code = trim($search['code']);
                $conditions[] = "code LIKE '%$code%'";
            }
	    if ($search['status']){
	        $status = (int)$search['status'];
		$conditions[] = "status = $status";
            }
            if($search['parent']){
                $parent_name = trim($search['parent']);
                $conditions[] = "parent LIKE '%$parent_name%'";
            }


            if($search['start_time'] && $search['end_time']){
                $start_day = DateTime::createFromFormat("d-m-Y",trim($search['start_time']));
                $start_time = $start_day->getTimestamp();
                $end_day = DateTime::createFromFormat("d-m-Y",trim($search['end_time']));
                $end_time = $end_day->getTimestamp();
                $conditions[] = "created_time BETWEEN $start_time AND $end_time";
            }
            if(!empty($conditions)){
               return " WHERE " . implode(" AND ",$conditions);
            }else{
                return "";
            }
        }else{
            return "";
        }
    }
}