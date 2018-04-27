<?php
/**
 * Created by PhpStorm.
 * User: PMTB
 * Date: 10/6/2017
 * Time: 5:44 PM
 */
error_reporting(E_ALL); ini_set('display_errors', 1);
header('Access-Control-Allow-Origin:*');

define('DB_CHARSET','UTF8');
define('_DB_REPLICATE',false);
define('DB_MASTER_SERVER', '');
define('DB_MASTER_USER', '');
define('DB_MASTER_PASSWORD', '');
define('DB_MASTER_NAME', 'apaxenglish_live');
date_default_timezone_set('Asia/Ho_Chi_Minh');
define('BASE_URL','http://localhost/dulichhe/');

define('_3_THANG', 30);
define('_9_THANG', 90);
define('_12_THANG', 120);
define('_4_5_THANG', 45);
define('_5_THANG', 50);
define('_10_THANG', 100);
define('HN_ZONE', 1);
define('HCM_ZONE', 2);




