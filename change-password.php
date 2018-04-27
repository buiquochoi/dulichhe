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
$success = false;
$message = "";

if(isset($_SESSION['user'])){
    if(isset($_POST['old_password']) && isset($_POST['password']) && isset($_POST['re_password'])){
        $new_password = $_POST['password']?$_POST['password']:"";
        $re_password = $_POST['re_password']?$_POST['re_password']:"";
        $old_password = md5($_POST['old_password']?$_POST['old_password']:"");

        $user_id = $_SESSION['user']['id'];

        $sql = "SELECT id FROM `user` WHERE id=$user_id AND password='$old_password' AND status=1";
        $res = DB::fetch($sql);
        if(!$res){
            $error = true;
            $message = "Mật khẩu hiện tại không đúng";
        }else{
            if(!$new_password){
                $error = true;
                $message = "Vui lòng nhập mật khẩu mới";
            }elseif($new_password != $re_password){
                $error = true;
                $message = "Mật khẩu không khớp";
            }elseif($old_password == md5($new_password)){
                $error = true;
                $message = "Mật khẩu mới đang được sử dụng";
            }else{
                $password = md5($new_password);
                $sql = "UPDATE `user` SET password='$password' WHERE id=$user_id";
                $res = DB::query($sql);
                if(!$res){
                    $error = true;
                    $message = "Có lỗi xảy ra.<br>Vui lòng thử lại sau!";
                }else{
                    $success = true;
                    $message = "Thành công";
                }
            }

        }
    }
}else{
    header("LOCATION:".BASE_URL."login.php");
}

?>

<!DOCTYPE html>
<html lang="en">

<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống kê | Đổi mật khẩu</title>

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700' rel='stylesheet' type='text/css'>


    <link href="css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="plugins/font-awesome/css/font-awesome.min.css">

    <link href="css/nifty.min.css" rel="stylesheet">

    <link href="css/demo/nifty-demo-icons.min.css" rel="stylesheet">

    <link href="css/demo/nifty-demo.min.css" rel="stylesheet">

    <link href="plugins/chosen/chosen.min.css" rel="stylesheet">

    <link href="plugins/bootstrap-datepicker/bootstrap-datepicker.min.css" rel="stylesheet">



    <script src="js/jquery-2.2.4.min.js"></script>

    <script src="js/bootstrap.min.js"></script>

    <script src="js/nifty.min.js"></script>

    <script src="js/demo/nifty-demo.min.js"></script>

    <script src="plugins/chosen/chosen.jquery.min.js"></script>

    <script src="js/d3.v3.min.js"></script>

    <script src="plugins/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
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
                    <h3 class="h4 mar-no">Đổi mật khẩu</h3>
                    <!--                    <p class="text-muted">Sign In to your account</p>-->
                </div>
                <? if($error) echo '<div class="alert alert-danger">'.$message.'</div>'; ?>
                <? if($success){?>
                    <div class="alert alert-success"><?= $message ?></div>
                    <?}else{?>
                    <form action="/change-password.php" method="post">
                        <div class="form-group">
                            <input type="password" class="form-control" name="old_password" placeholder="Mật khẩu hiện tại" autofocus>
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" name="password" placeholder="Mật khẩu mới">
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" name="re_password" placeholder="Nhập lại mật khẩu">
                        </div>
                        <button class="btn btn-success btn-lg btn-block" type="submit">Xác nhận</button>
                    </form>
                <? } ?>
                <div class="pad-top">
                    <a href="index.php" class="btn-link mar-rgt">Quay lại trang chủ</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="js/pmtb.js"></script>
</body>
</html>
