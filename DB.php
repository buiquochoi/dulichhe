<?php
class DB{
	static $db_connect_id	=false;			// connection id of this database
	static $db_result		=false;			// current result of an query
	static $db_num_queries 	= 0;
	// Debug
	static $num_queries 	= 0;			// number of queries was done
	static $query_debug 	= "";
	static $query_time;
	
	static $replicate_query = true;			// mac dinh cho tat ca query, neu co quey khong dung replicate : false, xu ly xong phai tra ve true.
	static $slave_connect 	= false;		// connection id of this database
	static $master_connect 	= false;		// current result of an query
    static $errorCode = FALSE;//ngannv add trả về mã lỗi sql
    static $showErrorMsg = TRUE;//ngannv add trả về mã lỗi sql
	
	function DB(){}
	
	//22.05.08 Nova
	static function db_connect($sqlserver, $sqluser, $sqlpassword, $dbname){
		$db_connect_id = @mysql_connect($sqlserver, $sqluser, $sqlpassword,true);
		if (isset($db_connect_id)and $db_connect_id){
			if (!$dbselect = @mysql_select_db($dbname)){
				@mysql_close($db_connect_id);
				$db_connect_id = $dbselect;
			}
			//set default charset DB 
			//added by Nova / 08.11.08
			if(DB_CHARSET == 'UTF8'){
				mysql_query ('SET NAMES UTF8',$db_connect_id);
			}
		}

		if(!$db_connect_id){
			$time = date("H:i:s d-m-Y");
			error_log("Could not connect to server ($time)\n",3,"update_teacher.log");
			die();
			return false;
		}
		
		return $db_connect_id;
	}

	static function query($query , $call_pos = ''){
		self::$db_result = false;
		
		if (!empty($query)){
			if( _DB_REPLICATE AND preg_match( "/^(?:\()?select/i", $query )   AND self::$replicate_query){
				if(!self::$slave_connect){
					self::$slave_connect = self::db_connect( DB_SLAVE_SERVER , DB_SLAVE_USER , DB_SLAVE_PASSWORD , DB_SLAVE_NAME );
				}
				$connection_switch = self::$slave_connect;
			}
			else{		
				if(!self::$master_connect){
					self::$master_connect = self::db_connect( DB_MASTER_SERVER , DB_MASTER_USER , DB_MASTER_PASSWORD , DB_MASTER_NAME); 
				}
				$connection_switch = self::$master_connect;
			}
			self::$db_connect_id = $connection_switch;
	
			if(!(self::$db_result = @mysql_query($query, self::$db_connect_id))){
                self::$errorCode=mysql_errno(self::$db_connect_id) ;

                //if(isset($_REQUEST["ebug"]) && intval($_REQUEST["ebug"]) > 0){
			}
			self::$db_num_queries++;
			

			//if (isset($_REQUEST["ebug"]) && intval($_REQUEST["ebug"]) > 0) {
		}
		
		return self::$db_result;
	}
	
	// function  close
	// Close SQL connection
	// should be called at very end of all scripts
	// ------------------------------------------------------------------------------------------
	static function close($con_id=false){
		if($con_id){
			$result = @mysql_close($con_id);
			return $result;
		}
		else{
			if (isset(self::$db_result) && self::$db_result){
				@mysql_free_result(self::$db_result);
				self::$db_result=false;
			}
				
			if (isset(self::$master_connect) && self::$master_connect){
				@mysql_close(self::$master_connect);
				self::$master_connect = false;
			}
			
			if (isset(self::$slave_connect) && self::$slave_connect){
				@mysql_close(self::$slave_connect);
				self::$slave_connect = false;
			}
		}
		return true;
	}
	
	static function count($table, $condition=false,$call_pos=''){
		return self::fetch('SELECT COUNT(*) AS total FROM `'.$table.'`'.($condition?' WHERE '.$condition:''),'total',0,$call_pos);
	}
	
	//Lay ra mot ban ghi trong bang $table thoa man dieu kien $condition
	//Neu bang duoc cache thi lay tu cache, neu khong query tu CSDL
	static function select($table, $condition,$call_pos=''){
		if($result = self::select_id($table, $condition,$call_pos='')){
			return $result;
		}
		else{
			return self::exists('SELECT * FROM `'.$table.'` WHERE '.$condition.' LIMIT 0,1',$call_pos);
		}
	}
	
	static function select_id($table, $condition,$call_pos=''){
	
		if($condition and !preg_match('/[^a-zA-Z0-9_#-\.]/',$condition)){
			return self::exists_id($table,$condition,$call_pos);
		}
		else{
			return false;
		}
	}
	
	//Lay ra tat ca cac ban ghi trong bang $table thoa man dieu kien $condition sap xep theo thu tu $order
	//Neu bang duoc cache thi lay tu cache, neu khong query tu CSDL
	static function select_all($table, $condition=false, $order = false,$call_pos=''){
		if($order){
			$order = ' ORDER BY '.$order;
		}
		if($condition){
			$condition = ' WHERE '.$condition;
		}
		self::query('SELECT * FROM `'.$table.'` '.$condition.' '.$order,$call_pos);
		return self::fetch_all();
	}
        
	static function select_limit($table, $condition=false, $limit= false, $order = false,$call_pos=''){
		if($order){
			$order = ' ORDER BY '.$order;
		}
		if($condition){
			$condition = ' WHERE '.$condition;
		}
		if($limit){
			$limit = ' LIMIT '.$limit;
		}
		self::query('SELECT * FROM `'.$table.'` '.$condition.' '.$order. ' ' .$limit);
		return self::fetch_all();
	}
	
	

	//Tra ve ban ghi query tu CSDL bang lenh SQL $query neu co
	//Neu khong co tra ve false
	//$query: cau lenh SQL se thuc hien
	static function exists($query,$call_pos=''){
		self::query($query,$call_pos);
		if(self::num_rows()>=1){
			return self::fetch();
		}
		return false;
	}
	
	static function query_debug(){
		return self::$query_debug;
	}
	
	//Tra ve ban ghi trong bang $table co id la $id
	//Neu khong co tra ve false
	//$table: bang can truy van
	//$id: ma so ban ghi can lay
	static function exists_id($table,$id,$call_pos=''){
		if($table && $id){
			return  self::exists('SELECT * FROM `'.$table.'` WHERE id="'.$id.'" LIMIT 0,1',$call_pos);
		}
		return false;
	}
	
	static function insert($table, $values, $replace=false,$call_pos=''){
		if($replace){
			$query='REPLACE';
		}
		else{
			$query='INSERT INTO';
		}
		
		$query.=' `'.$table.'`(';
		$i=0;
		if(is_array($values)){
			foreach($values as $key=>$value){
				if(($key===0) or is_numeric($key)){
					$key=$value;
				}
				if($key){
					if($i<>0){
						$query.=',';
					}
					$query.='`'.$key.'`';
					$i++;
				}
			}
			$query.=') VALUES(';
			$i=0;
			
			foreach($values as $key=>$value){
				if(is_numeric($key) or $key===0){
					$value=Url::get($value);
				}
				
				if($i<>0){
					$query.=',';
				}

				if($value==='NULL'){
					$query.='NULL';
				}
				else{
					$query.='\''.self::escape($value).'\'';
				}
				$i++;
			}
			$query.=')';
			
			if(self::query($query,$call_pos)){
				$id = self::insert_id();		
				return $id;
			}
		}
	}
	
	static function delete($table, $condition,$call_pos=''){
		$query='DELETE FROM `'.$table.'` WHERE '.$condition;

		if(self::query($query,$call_pos)){
			return true;
		}
	}
	
	static function delete_id($table, $id,$call_pos=''){
		return self::delete($table, 'id="'.addslashes($id).'"',$call_pos);
	}
	
	static function update($table, $values, $condition,$call_pos=''){
		$query='UPDATE `'.$table.'` SET ';
		$i=0;
		
		if($values){
			foreach($values as $key=>$value){
				if($key===0 or is_numeric($key)){
					$key=$value;
					$value=Url::get($value);
				}
				
				if($i<>0){
					$query.=',';
				}
				
				if($key){
					if($value==='NULL'){
						$query.='`'.$key.'`=NULL';
					}
					else{
						$query.='`'.$key.'`=\''.self::escape($value).'\'';
					}
					$i++;
				}
			}
			$query.=' WHERE '.$condition;

			if(self::query($query,$call_pos)){
				return true;
			}
		}
	}
	
	static function update_id($table, $values, $id){
		return self::update($table, $values, 'id="'.$id.'"');
	}
	
	static function num_rows($query_id = 0){
		if (!$query_id){
			$query_id = self::$db_result;
		}

		if ($query_id){
			$result = @mysql_num_rows($query_id);

			return $result;
		}
		else{
			return false;
		}
	}
	
	static function affected_rows(){
		if (isset(self::$db_connect_id) and self::$db_connect_id){
			$result = @mysql_affected_rows(self::$db_connect_id);

			return $result;
		}
		else{
			return false;
		}
	}
	
    /*========================================================================*/
    // Fetch a row based on the last query
    // Added by Nova 12.06.08
    /*========================================================================*/
    static function fetch_row($query_id = "") {
    
    	if ($query_id == ""){
    		$query_id =  self::$db_result;
    	}
    	
        $record_row = mysql_fetch_array($query_id, MYSQL_ASSOC);
        return $record_row;
    }
    
	static function fetch($sql = false, $field = false, $default = false,$call_pos=''){
		if($sql){
			self::query($sql,$call_pos);
		}
		
		$query_id = self::$db_result;
		if ($query_id){
			if($result = @mysql_fetch_assoc($query_id)){
				if($field && isset($result[$field])){
					return $result[$field];
				}
				elseif ($default!==false){
					return $default;
				}
				return $result;
			}
			elseif ($default!==false){
				return $default;
			}
			return $default;
		}
		else{
			return false;
		}
	}

    static function fetch_all($sql=false,$call_pos='',$index=false){
        if($sql){
            self::query($sql,$call_pos);
        }
        $query_id = self::$db_result;

        if ($query_id){
            $result=array();
            if($index){
                while($row = @mysql_fetch_assoc($query_id)){
                    $result[$row[$index]] = $row;
                }
            }else{
                while($row = @mysql_fetch_assoc($query_id)){
                    if(isset($row['id']))
                        $result[$row['id']] = $row;
                    else
                        $result[] = $row;
                }
            }



            return $result;
        }
        else{
            return false;
        }
    }
	
	// lxquang them vao 06/06/2008
	// Sung dung trong quan ly message
	static function fetch_all_mess($sql=false,$call_pos=''){
		if($sql){
			self::query($sql,$call_pos);
		}
		$query_id = self::$db_result;

		if ($query_id){
			$result=array();
			while($row = @mysql_fetch_assoc($query_id)){
				$result[$row['mt_id']] = $row;
			}

			return $result;
		}
		else{
			return false;
		}
	}
	//end lxquang add
	
	static function fetch_all_array($sql=false,$call_pos=''){
		if($sql){
			self::query($sql,$call_pos);
		}
		$query_id = self::$db_result;

		if ($query_id){
			$result=array();
			while($row = @mysql_fetch_assoc($query_id)){
				$result[] = $row;
			}

			return $result;
		}
		else{
			return false;
		}
	}
	
	static function insert_id(){
		if (self::$db_connect_id){
			$result = mysql_insert_id(self::$db_connect_id);
			return $result;
		}
		else{
			return false;
		}
	}
	
	static function escape($sql){
		return addslashes($sql);
	}
/**
 * @static
 * @param $table
 * @param $dbfields
 * @param string $call_pos
 * @return bool|int
 * add by ngannv
 */
    static function insert_v2($table, $dbfields, $call_pos = '')
    {
        $query = "INSERT INTO $table (" . implode(', ', array_keys($dbfields)) . ")
			VALUES ( '" . implode("','", $dbfields) . "' )";
        if (self::query($query, $call_pos)) {
            return mysql_insert_id(self::$db_connect_id);
        }
        else {
            return false;
        }
    }
    /*add by ngannv*/
    static function getFields($table_name, $call_pos = '')
    {
        $fields = false;
        if (!$fields) {
            $q_select = "SHOW COLUMNS FROM $table_name";
            self::query($q_select, $call_pos);
            if (self::$db_result) {
                while ($r = mysql_fetch_assoc(self::$db_result)) {
                    $fields[$r['Field']]['key']         = $r['Key'];
                    $fields[$r['Field']]['extra']       = $r['Extra'];
                    $fields[$r['Field']]['name']        = $r['Field'];
                    $fields[$r['Field']]['data']        = '';
                    $strLength                          = strlen($r['Type']);
                    $len                                = stripos($r['Type'], '(') - $strLength;
                    $fields[$r['Field']]['type']        = substr($r['Type'], 0, $len);
                    $fields[$r['Field']]['type_length'] = substr($r['Type'], stripos($r['Type'], '(') + 1, -1);
                }
            }
        }
        $curTable['fields'] = $fields;
        return $curTable;

    }


	static function num_queries(){
		return self::$db_num_queries;
	}
}
//register_shutdown_function(array("DB","close"));