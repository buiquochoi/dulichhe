<?php
/**
 * Created by PhpStorm.
 * User: PMTB
 * Date: 9/21/2017
 * Time: 10:38 AM
 */
require_once('config.php');
require_once('DB.php');
require_once('Statistic.php');
require_once('CenterModel.php');
require_once('CityModel.php');
require_once('Paging.php');
require_once('Process.php');
session_start();

//var_dump($_SESSION); die;

$user = ['id'=>0,'center_id'=>0,'full_name'=>""];

if(!isset($_SESSION['user']) || !$_SESSION['user']){
    header("LOCATION:".BASE_URL."login.php");
}else{
    $user = $_SESSION['user'];
}

$query = array();
$search = [
    'event_code' => $user['event_code'],
    'phone' => "",
    'name' => "",
    'parent' => "",
    'center' => 0,
    'start_time' => "",
    'end_time' => ""
];
$limit = 20;
$page = isset($_GET['page'])?$_GET['page']:1;

$query['limit'] = ($page - 1)*$limit . "," . $limit;

$statistic = new Statistic();
$data = $statistic->getCodes($query);

$count_transaction = $statistic->countTransactions($search);
$count_page = ceil($count_transaction/$limit);

$paging_obj = new Paging();
$paging = $paging_obj->page($page,$count_page);

?>

<!DOCTYPE html>
<html lang="en">

<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống kê</title>

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
<div id="container" class="effect aside-float aside-bright mainnav-lg">

    <!--NAVBAR-->
    <!--===================================================-->
    <header id="navbar">
        <div id="navbar-container" class="boxed">

            <!--Brand logo & name-->
            <!--================================-->
            <div class="navbar-header">
                <a href="/" class="navbar-brand">
                    <img src="img/logo.png" alt="Nifty Logo" class="brand-icon">
                    <div class="brand-title">
                        <span class="brand-text">Apax English</span>
                    </div>
                </a>
            </div>
            <!--================================-->
            <!--End brand logo & name-->


            <!--Navbar Dropdown-->
            <!--================================-->
            <div class="navbar-content clearfix">
                <ul class="nav navbar-top-links pull-left">

                    <!--Navigation toogle button-->
                    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                    <li class="tgl-menu-btn">
                        <a class="mainnav-toggle" href="#">
                            <i class="demo-pli-view-list"></i>
                        </a>
                    </li>
                    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                    <!--End Navigation toogle button-->
                </ul>
            </div>
            <!--================================-->
            <!--End Navbar Dropdown-->

        </div>
    </header>
    <!--===================================================-->
    <!--END NAVBAR-->

    <div class="boxed">

        <!--CONTENT CONTAINER-->
        <!--===================================================-->
        <div id="content-container">

            <!--Page Title-->
            <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
            <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
            <!--End page title-->

            <!--Page content-->
            <!--===================================================-->
            <div id="page-content">

                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel">
                            <div class="panel-heading" style="position: relative">
                                <h3 class="panel-title">
                                    Danh sách code chưa được sử dụng
                                </h3>
                                <a href="/index.php?export=<?= EXPORT_ALL ?>" class="export btn btn-success pull-right" style="position: absolute;top: 9px;right: 30px;">Export all</a>
                                <a href="/index.php?export=<?= EXPORT ?>" class="export btn btn-success pull-right" style="position: absolute;top: 9px;right: 120px;">Export</a>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                <tr>
                                                    <th style="width: 50px;">STT</th>
                                                    <th style="width: 130px;">Học sinh</th>
                                                    <th style="width: 110px;">Phụ huynh</th>
                                                    <th style="width: 130px;">Số điện thoại</th>
                                                    <th style="/*! width: 25%; */">Trung tâm</th>
                                                    <th style="/*! width: 25%; */">Chương trình học</th>
                                                    <th style="/*! width: 25%; */">Gói học phí</th>
                                                    <th style="">Thời gian đăng ký</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php if(!empty($data)){
                                                    $count = ($page - 1)*$limit + 1;
                                                    foreach ($data as $da){
                                                        ?>
                                                        <tr>
                                                            <td><?= $count++ ?></td>
                                                            <td>
                                                                <?= $da['name'] ?>
                                                            </td>
                                                            <td>
                                                                <?= $da['parent'] ?>
                                                            </td>
                                                            <td>
                                                                <?= $da['phone'] ?>
                                                            </td>
                                                            <td>
                                                                <?= $centers[$da['center']]['address'] ?>
                                                            </td>
                                                            <td>
                                                                <?= PRODUCTS[$da['product_id']]['name'] ?>
                                                            </td>
                                                            <td>
                                                                <?= $da['tuition_fee']/10 . ' tháng' ?>
                                                            </td>
                                                            <td>
                                                                <?= date("H:i d/m/Y",$da['created_time']) ?>
                                                            </td>
                                                        </tr>
                                                        <?
                                                    }
                                                }else{
                                                    ?>
                                                    <tr class="no-records-found"><td colspan="9" class="text-center">Không có đăng ký nào</td></tr>
                                                    <?
                                                }?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="pad-btm">
                                            <div class="dataTables_info" id="demo-dt-basic_info" role="status" aria-live="polite" style="margin: 20px 0;">Tổng số: <b><?= $count_transaction?$count_transaction:0 ?></b> kết quả</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="pad-btm" style="text-align: right">

                                            <!--Small Pagination-->
                                            <!--===================================================-->
                                            <ul class="pagination pagination-sm" id="paging">
                                                <?= $paging ?>
                                            </ul>
                                            <!--===================================================-->
                                            <!--End Small Pagination-->

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>




            </div>
            <!--===================================================-->
            <!--End page content-->


        </div>
        <!--===================================================-->
        <!--END CONTENT CONTAINER-->



        <!--ASIDE-->
        <!--===================================================-->
        <!--===================================================-->
        <!--END ASIDE-->


        <!--MAIN NAVIGATION-->
        <!--===================================================-->
        <nav id="mainnav-container">
            <div id="mainnav">

                <!--Menu-->
                <!--================================-->
                <div id="mainnav-menu-wrap">
                    <div class="nano">
                        <div class="nano-content">

                            <!--Profile Widget-->
                            <!--================================-->
                            <div id="mainnav-profile" class="mainnav-profile">
                                <div class="profile-wrap">
                                    <div class="pad-btm">
                                        <span class="label label-success pull-right">Admin</span>
                                        <img class="img-circle img-sm img-border" src="img/profile-photos/1.png" alt="Profile Picture">
                                    </div>
                                    <a href="#profile-nav" class="box-block" data-toggle="collapse" aria-expanded="false">
                                            <span class="pull-right dropdown-toggle">
                                                <i class="dropdown-caret"></i>
                                            </span>
                                        <p class="mnp-name"><?= $user['full_name'] ?></p>
                                        <!--                                        <span class="mnp-desc">2012206</span>-->
                                    </a>
                                </div>
                                <div id="profile-nav" class="collapse list-group bg-trans">
                                    <!--                                    <a href="#" class="list-group-item">-->
                                    <!--                                        <i class="demo-pli-male icon-lg icon-fw"></i> View Profile-->
                                    <!--                                    </a>-->
                                    <a href="/change-password.php" class="list-group-item">
                                        <i class="demo-pli-gear icon-lg icon-fw"></i> Change password
                                    </a>
                                    <!--                                    <a href="#" class="list-group-item">-->
                                    <!--                                        <i class="demo-pli-information icon-lg icon-fw"></i> Help-->
                                    <!--                                    </a>-->
                                    <a href="/logout.php" class="list-group-item">
                                        <i class="demo-pli-unlock icon-lg icon-fw"></i> Logout
                                    </a>
                                </div>
                            </div>

                            <ul id="mainnav-menu" class="list-group">

                                <!--Category name-->
                                <li class="list-header">Navigation</li>

                                <!--Menu list item-->
                                <li class="active-link">
                                    <a href="/">
                                        <i class="demo-psi-home"></i>
                                        <span class="menu-title">
												<strong>Dashboard</strong>
											</span>
                                    </a>
                                </li>
                            </ul>


                            <!--Widget-->
                            <!--================================-->
                            <!--================================-->
                            <!--End widget-->

                        </div>
                    </div>
                </div>
                <!--================================-->
                <!--End menu-->

            </div>
        </nav>
        <!--===================================================-->
        <!--END MAIN NAVIGATION-->

    </div>



    <!-- FOOTER -->
    <!--===================================================-->
    <footer id="footer">

        <!-- Visible when footer positions are fixed -->
        <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
        <div class="show-fixed pull-right">
            You have <a href="#" class="text-bold text-main"><span class="label label-danger">3</span> pending action.</a>
        </div>



        <!-- Visible when footer positions are static -->
        <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->



        <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
        <!-- Remove the class "show-fixed" and "hide-fixed" to make the content always appears. -->
        <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->

        <p class="pad-lft">&#0169; 2017 APAX ENGLISH</p>



    </footer>
    <!--===================================================-->
    <!-- END FOOTER -->


    <!-- SCROLL PAGE BUTTON -->
    <!--===================================================-->
    <button class="scroll-top btn">
        <i class="pci-chevron chevron-up"></i>
    </button>
    <!--===================================================-->



</div>
<!--===================================================-->
<!-- END OF CONTAINER -->



<!-- SETTINGS - DEMO PURPOSE ONLY -->
<!--===================================================-->

<!--===================================================-->
<!-- END SETTINGS -->
<script type="text/javascript" src="js/pmtb.js"></script>
</body>
</html>

