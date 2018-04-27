<?php
/**
 * Created by PhpStorm.
 * User: PMTB
 * Date: 10/6/2017
 * Time: 3:14 PM
 */
require_once('config.php');
require_once('DB.php');
session_start();

$error = false;


if(!isset($_SESSION['user'])){
    if(isset($_POST['username']) && isset($_POST['password'])){
        $username = $_POST['username']?$_POST['username']:"";
        $password = md5($_POST['password']?$_POST['password']:"");

        $sql = "SELECT id, full_name, event_code, center_id, role FROM `events_account` WHERE username='$username' AND password='$password' AND status=1";
        $res = DB::fetch($sql);
        if($res){
            $_SESSION['user'] = $res;
            header("LOCATION:".BASE_URL."index.php");
        }else{
            $error = true;
        }
    }
}else{
    header("LOCATION:".BASE_URL."index.php");
}

?>

<!DOCTYPE html>
<html lang="en">

<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống kê</title>

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700' rel='stylesheet' type='text/css'>


    <link href="public_files/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="public_files/plugins/font-awesome/css/font-awesome.min.css">

    <link href="public_files/css/nifty.min.css" rel="stylesheet">

    <link href="public_files/css/demo/nifty-demo-icons.min.css" rel="stylesheet">

    <link href="public_files/css/demo/nifty-demo.min.css" rel="stylesheet">

    <link href="public_files/plugins/chosen/chosen.min.css" rel="stylesheet">

    <link href="public_files/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css" rel="stylesheet">



    <script src="public_files/js/jquery-2.2.4.min.js"></script>

    <script src="public_files/js/bootstrap.min.js"></script>

    <script src="public_files/js/nifty.min.js"></script>

    <script src="public_files/js/demo/nifty-demo.min.js"></script>

    <script src="public_files/plugins/chosen/chosen.jquery.min.js"></script>

    <script src="public_files/js/d3.v3.min.js"></script>

    <script src="public_files/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
</head>
<body>
<div id="container" class="cls-container">

    <!-- BACKGROUND IMAGE -->
    <!--===================================================-->
    <div id="bg-overlay"></div>


    <!-- LOGIN FORM -->
    <!--===================================================-->
    <div class="cls-content">
        <div class="cls-content-sm panel">
            <div class="panel-body">
                <div class="mar-ver pad-btm">
                    <h3 class="h4 mar-no">Thống kê</h3>
<!--                    <p class="text-muted">Sign In to your account</p>-->
                </div>
                <?php if($error) echo '<div class="alert alert-danger">Sai tài khoản hoặc mật khẩu</div>'; ?>
                <form action="./login.php" method="post">
                    <div class="form-group">
                        <input type="text" class="form-control" name="username" placeholder="Tên đăng nhập" autofocus>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" name="password" placeholder="Mật khẩu">
                    </div>
<!--                    <div class="checkbox pad-btm text-left">-->
<!--                        <input id="demo-form-checkbox" class="magic-checkbox" type="checkbox">-->
<!--                        <label for="demo-form-checkbox">Remember me</label>-->
<!--                    </div>-->
                    <button class="btn btn-danger btn-lg btn-block" type="submit">Đăng nhập</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="public_files/js/pmtb.js"></script>
</body>
</html>
