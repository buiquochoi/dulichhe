<?php
/**
 * Created by PhpStorm.
 * User: PMTB
 * Date: 10/6/2017
 * Time: 5:42 PM
 */
require_once('config.php');
session_start();

if(isset($_SESSION['user'])){
    unset($_SESSION['user']);
}

header("LOCATION:".BASE_URL."login.php");