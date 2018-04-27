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

$zone = Process::$PRODUCTS[2]['zones'][1];

// echo Process::$PRODUCTS[1]['zones'][1][45]; die;
// var_dump(Process::$PRODUCTS[1]['zones'][1][90]); die;
$user = ['id'=>0,'center_id'=>0,'full_name'=>""];

if(!isset($_SESSION['user']) || !$_SESSION['user']){
    header("LOCATION:".BASE_URL."login.php");
}else{
    $user = $_SESSION['user'];
}
if(isset($_GET['cmd'])){
    if(isset($_GET['key']) && $_GET['key']){
        Process::confirmRegister($_GET['key']);
        header("LOCATION:".BASE_URL);
    }
}

if(isset($_GET['confirm']) && isset($_GET['key'])){
    Process::confirmByselect($_GET['confirm'],$_GET['key']);
    header("LOCATION:".BASE_URL);
}

$show_modal = false;
if (isset($_GET['edit'])){
    $x = $_GET['edit'];
    $p = DB::fetch("SELECT *from events_register where created_time = '$x'");
    $show_modal = true;
}

if (isset($_POST['export'])){
    if (isset($_POST['export_center'])){
        $center = $_POST['export_center'];
    }
    if (isset($_POST['export_status'])){
        $status = $_POST['export_status'];
    }
    if (isset($_POST['export_start']) && $_POST['export_end']){
        $start = $_POST['export_start'];
        $end = $_POST['export_end'];
    }
    if (isset($center) && isset($status) && isset($start) && isset($end)){
        $center_id = '('.implode(",", $center).')';
        $status_id = '('.implode(",", $status).')';
        $start_day = DateTime::createFromFormat("d-m-Y",trim($start));
        $start_time = $start_day->getTimestamp();
        $end_day = DateTime::createFromFormat("d-m-Y",trim($end));
        $end_time = $end_day->getTimestamp();
        $sql = "SELECT re.*,ag.address as center_name from events_register as re 
            LEFT JOIN agents as ag on ag.id = re.center
            where re.center in $center_id and re.status in $status_id and event_code = 'DULICHHE2018' AND created_time BETWEEN $start_time AND $end_time";

        $data = DB::fetch_all($sql);
        Process::export_data($data,$user['role']);
    }
}

if (isset($_POST['save'])){
    $name = $_POST['ten'];
    $parent =  $_POST['parent'];
    $phone =  $_POST['phone'];
    $id = $_POST['id'];
    $presenter_name = $_POST['presenter_name'];
    $presenter_phone = $_POST['presenter_phone'];
    $center = $_POST['center'];
    $zone = DB::fetch("SELECT *from agents where id = $center");
    $product = $_POST['product'];
    $dk_tuition_fee = key(Process::$PRODUCTS[$product]['zones'][$zone['zone_id']]);
    $users = [
        'name' => $name,
        'parent' => $parent,
        'phone' => $phone,
        'center' => $center,
        'product_id' => $product, 
        'tuition_fee' => $dk_tuition_fee,
        'presenter_name' => $presenter_name,
        'presenter_phone' => $presenter_phone
    ];
    
    DB::update_id('events_register', $users, $id);
}

if (isset($_POST['dangky'])){
    // var_dump($_SESSION['user']);die();
    $dk_name = trim($_POST['dk_name']);
    $dk_parent = trim($_POST['dk_parent']);
    $dk_phone = trim($_POST['dk_phone']);
    $dk_product_id = $_POST['dk_product_id'];
    $dk_center_id = $_POST['dk_center_id'];
    $center = DB::fetch("SELECT *from agents where id = $dk_center_id");
    $dk_name_gt = $_POST['dk_name_gt'];
    $dk_phone_gt = $_POST['dk_phone_gt'];
    $dk_method = $_POST['dk_method'];

    $dk_tuition_fee = key(Process::$PRODUCTS[$dk_product_id]['zones'][$center['zone_id']]);
    // var_dump($dk_tuition_fee);die();
    $data = [
        'name' => $dk_name,
        'parent' => $dk_parent,
        'phone' => $dk_phone,
        'product_id' => $dk_product_id,
        'center' => $dk_center_id,
        'presenter_name' => $dk_name_gt,
        'presenter_phone' => $dk_phone_gt,
        'event_code' => $_SESSION['user']['event_code'],
        'created_time' => (int)microtime(true),
        'mode' => $dk_method,
        'tuition_fee' => $dk_tuition_fee,
	'status' => 0
    ];
    Process::registerEvent($data);
}

$query = array();
$search = [
    'event_code' => $user['event_code'],
    'phone' => "",
    'name' => "",
    'parent' => "",
    'center' => $user['center_id'],
    'start_time' => "",
    'end_time' => "",
    'code' => '',
    'status' => ''
];

$limit = 20;
$page = isset($_GET['page'])?$_GET['page']:1;

$query['limit'] = ($page - 1)*$limit . "," . $limit;
if(isset($_POST['search'])){
    $search = $_POST['search'];
    $search['event_code'] = $user['event_code'];
    $search['center'] = $user['center_id']?$user['center_id']:$search['center'];
}


if(isset($_GET['export'])){
    $query['limit'] = 0;
    if($_GET['export'] == EXPORT_ALL){
        $search = [
            'phone' => "",
            'name' => "",
            'parent' => "",
            'status' => "",
            'center' => $user['center_id']?$user['center_id']:$search['center'],
            'event_code' => $user['event_code'],
            'start_time' => "",
            'presenter_name' => "",
            'presenter_phone' => "",
            'end_time' => "",
            'code' => ''
        ];
    }
}

// var_dump($search);
$statistic = new Statistic();
$data = $statistic->getTransactions($query,$search);
// var_dump($data[0]);die();

$centers = CenterModel::getAllCenter();
// var_dump($centers);die();
$cities = CityModel::getAllCity();

if(isset($_GET['export'])){
    Process::export($data,$centers,$cities);
}


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


    <link href="public_files/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="public_files/plugins/font-awesome/css/font-awesome.min.css">

    <link href="public_files/css/nifty.min.css" rel="stylesheet">

    <link href="public_files/css/demo/nifty-demo-icons.min.css" rel="stylesheet">

    <link href="public_files/css/demo/nifty-demo.min.css" rel="stylesheet">

    <link href="public_files/plugins/chosen/chosen.min.css" rel="stylesheet">

    <link href="public_files/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css" rel="stylesheet">

    <link href="public_files/plugins/select2/css/select2.min.css" rel="stylesheet">

    <script src="public_files/js/jquery-2.2.4.min.js"></script>

    <script src="public_files/js/bootstrap.min.js"></script>

    <script src="public_files/js/nifty.min.js"></script>
    <script src="public_files/plugins/select2/js/select2.min.js"></script>

    <script src="public_files/js/demo/nifty-demo.min.js"></script>

    <script src="public_files/plugins/chosen/chosen.jquery.min.js"></script>

    <script src="public_files/js/d3.v3.min.js"></script>

    <script src="public_files/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
<!-- Global site tag (gtag.js) - AdWords: 809196628 -->
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-809196628"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'AW-809196628');
</script>  
</head>
<body>
	<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MZ2TFGD"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <div id="container" class="effect aside-float aside-bright mainnav-lg">

            <!--NAVBAR-->
            <!--===================================================-->
            <header id="navbar">
                <div id="navbar-container" class="boxed">

                    <!--Brand logo & name-->
                    <!--================================-->
                    <div class="navbar-header">
                        <a href="/" class="navbar-brand">
                            <img src="public_files/img/logo.png" alt="Nifty Logo" class="brand-icon">
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
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Tìm kiếm</h3>
                                    </div>
                                    <form class="panel-body" method="post" id="search">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label class="control-label">Số điện thoại</label>
                                                    <input class="form-control" type="text" name="search[phone]" value="<?= isset($search['phone'])?$search['phone']:'' ?>">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label class="control-label">Trung tâm</label>
                                                    <select data-placeholder="Choose a Country..." id="select-center" name="search[center]" tabindex="2">
                                                        <option value="0">Tất cả</option>
                                                        <?php
                                                            if(isset($search['center'])){
                                                                foreach ($centers as $center){
                                                                    if($center['id'] == $search['center']){
                                                                        echo "<option value='".$center['id']."' selected>".$center['address']."</option>";
                                                                    }else{
                                                                        echo "<option value='".$center['id']."'>".$center['address']."</option>";
                                                                    }
                                                                }
                                                            }else{
                                                                foreach ($centers as $center){
                                                                    echo "<option value='".$center['id']."'>".$center['address']."</option>";
                                                                }
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label class="control-label">Họ tên phụ huynh</label>
                                                    <input class="form-control" type="text" name="search[parent]" value="<?= isset($search['parent'])?$search['parent']:'' ?>">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label class="control-label">Họ tên học sinh</label>
                                                    <input class="form-control" type="text" name="search[name]" value="<?= isset($search['name'])?$search['name']:'' ?>">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label class="control-label">Họ tên người giới thiệu</label>
                                                    <input class="form-control" type="text" name="search[present_name]" value="<?= isset($search['present_name'])?$search['present_name']:'' ?>">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label class="control-label">Số điện thoại người giới thiệu</label>
                                                    <input class="form-control" type="text" name="search[present_phone]" value="<?= isset($search['present_phone'])?$search['present_phone']:'' ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-3" style="padding-right: 7.5px !important;">
                                                <div class="form-group">
                                                    <label class="control-label">Thời gian đăng ký</label>
                                                    <div id="time">
                                                        <div class="input-daterange input-group" id="datepicker">
                                                            <input class="form-control" name="search[start_time]" style="border-radius: 0;" type="text" value="<?= isset($search['start_time'])?$search['start_time']:'' ?>">
                                                            <span class="input-group-addon">đến</span>
                                                            <input class="form-control" name="search[end_time]" style="border-radius: 0;" type="text" value="<?= isset($search['end_time'])?$search['end_time']:'' ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label class="control-label">Mã dự thưởng</label>
                                                    <input class="form-control" type="text" name="search[code]" value="<?= isset($search['code'])?$search['code']:'' ?>">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label class="control-label">Mã dự thưởng người giới thiệu</label>
                                                    <input class="form-control" type="text" name="search[present_code]" value="<?= isset($search['present_code'])?$search['present_code']:'' ?>">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label class="control-label">Trạng thái</label>
                                                    <select class="form-control" name="search[status]">
							<option value="" selected>Trạng thái</option>
                                                        <option <?php if ($search['status'] == 0): ?>
                                                            selected    
                                                        <?php endif ?>
                                                         value="0">Chưa liên hệ</option>
                                                        <option <?php if ($search['status'] == 1): ?>
                                                            selected    
                                                        <?php endif ?> value="1">Đã liên hệ</option>
                                                        <option <?php if ($search['status'] == 2): ?>
                                                            selected    
                                                        <?php endif ?> value="2">Đã đóng tiền</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="confirm" id="confirm" value="0">
                                        <div class="row text-center">
                                            
                                            <button type="button" class="btn btn-success search-button">Tìm kiếm</button>
					                       <button type="button" class="btn btn-warning exportdata" data-toggle="modal" data-target="#myModal_ep">Export</button>
                                            <a href="<?=BASE_URL?>" class="btn btn-default">Bỏ lọc</a>
                                        </div>
                                    </form>
                                </div>
				<div class="modal fade" id="myModal_ep" role="dialog">
                    <div class="modal-dialog">
                    
                      <!-- Modal content-->
                      <div class="modal-content">
                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal">&times;</button>
                          <h4 class="modal-title">Thông tin báo cáo</h4>
                        </div>
                        <div class="modal-body">
                        <form action="<?=BASE_URL?>" method="post">
                    		<div class="row">
                    			<div class="col-md-4">
                    				<label class="control-label">Trung tâm</label>
                    			</div>
                                <div class="col-md-8">
                                    <select class="form-control center_select" name="export_center[]" multiple>
                                        <?php foreach ($centers as $center): ?>
                                            <option value="<?=$center['id']?>"><?=$center['address']?></option>
                                        <?php endforeach ?>
                                    </select>
                                    <input type="checkbox" id="checkbox_center">
                                </div>
                    		</div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="control-label">Trạng thái</label>
                                </div>
                                <div class="col-md-8">
                                    <select name="export_status[]" class="form-control status_select"  multiple>
                                        <option value="0">Chưa liên hệ</option>
                                        <option value="1">Đã liên hệ</option>
                                        <option value="2">Đã xác nhận nộp tiền</option>
                                    </select>
                                <input type="checkbox" id="checkbox_status">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <div class="col-md-4">
                                        <label class="control-label">Thời gian đăng ký</label>
                                    </div>
                                    <div class="col-md-8">
                                        <div id="time">
                                        <div class="input-daterange input-group" id="datepicker">
                                            <input class="form-control" name="export_start" style="border-radius: 0;" type="text" value="<?= isset($search['start_time'])?$search['start_time']:'' ?>">
                                            <span class="input-group-addon">đến</span>
                                            <input class="form-control" name="export_end" style="border-radius: 0;" type="text" value="<?= isset($search['end_time'])?$search['end_time']:'' ?>">
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-success" type="submit" name="export">Export</button>
                        </form>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                      </div>
                      
                    </div>
                  </div>
                                <div class="panel">
                                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal">Đăng ký mới</button>
                                    <div class="modal fade" id="myModal" role="dialog">
                                        <div class="modal-dialog">
                                        
                                          <!-- Modal content-->
                                          <div class="modal-content">
                                            <div class="modal-header">
                                              <button type="button" class="close" data-dismiss="modal">&times;</button>
                                              <h4 class="modal-title text-danger text-center">Đăng ký ngay</h4>
                                            </div>
                                            <form action="./index.php" method="post"> 
                                            <div class="modal-body"> 
                                                        
                                                  <div class="row">
                                                      <div class="col-md-4">
                                                          <p>Tên học sinh:</p>
                                                      </div>
                                                      <div class="col-md-8">
                                                          <div class="form-group">
                                                              <input name="dk_name" type="text" class="form-control">
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <div class="row">
                                                      <div class="col-md-4">
                                                          <p>Phụ huynh:</p>
                                                      </div>
                                                      <div class="col-md-8">
                                                          <div class="form-group">
                                                              <input name="dk_parent" type="text" class="form-control">
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <div class="row">
                                                      <div class="col-md-4">
                                                          <p>Số điện thoại:</p>
                                                      </div>
                                                      <div class="col-md-8">
                                                          <div class="form-group">
                                                              <input name="dk_phone" type="text" class="form-control">
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <div class="row">
                                                      <div class="col-md-4">
                                                          <p>Chương trình học:</p>
                                                      </div>
                                                      <div class="col-md-8">
                                                          <div class="form-group">
                                                              <select  name="dk_product_id" id="" class="form-control">
                                                                  <?php foreach (Process::$PRODUCTS as $key => $value): ?>
                                                                      <option value="<?=$key?>"><?=Process::$PRODUCTS[$key]['name']?></option>
                                                                  <?php endforeach ?>
                                                              </select>
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <div class="row">
                                                      <div class="col-md-4">
                                                          <p>Trung tâm đăng ký  :</p>
                                                      </div>
                                                      <div class="col-md-8">
                                                          <div class="form-group">
                                                              <select name="dk_center_id" class="form-control" data-placeholder="Choose a Country..." id="select-center" name="search[center]" tabindex="2">
                                                            <option value="0">Tất cả</option>
                                                            <?php
                                                                if(isset($search['center'])){
                                                                    foreach ($centers as $center){
                                                                        if($center['id'] == $search['center']){
                                                                            echo "<option value='".$center['id']."' selected>".$center['address']."</option>";
                                                                        }else{
                                                                            echo "<option value='".$center['id']."'>".$center['address']."</option>";
                                                                        }
                                                                    }
                                                                }else{
                                                                    foreach ($centers as $center){
                                                                        echo "<option value='".$center['id']."'>".$center['address']."</option>";
                                                                    }
                                                                }
                                                            ?>
                                                        </select>
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <p><strong>Thông tin người giới thiệu</strong></p>
                                                  <div class="row">
                                                      <div class="col-md-4">
                                                          <p>Họ tên:</p>
                                                      </div>
                                                      <div class="col-md-8">
                                                          <div class="form-group">
                                                              <input name="dk_name_gt" type="text" class="form-control" placeholder="Tên người giới thiệu">
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <div class="row">
                                                      <div class="col-md-4">
                                                          <p>Số điện thoại:</p>
                                                      </div>
                                                      <div class="col-md-8">
                                                          <div class="form-group">
                                                              <input name="dk_phone_gt" type="text" class="form-control" placeholder="Số điện thoại người giới thiệu">
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <div class="row">
                                                      <div class="col-md-4">
                                                          <p>Hình thức đăng ký:</p>
                                                      </div>
                                                      <div class="col-md-8">
                                                          <div class="form-group">
                                                              <select name="dk_method" id="" class="form-control">
                                                                  <option value="0">Chọn hình thức đăng ký</option>
                                                                  <option value="1">OFFLINE</option>
                                                                  <option value="2">FACEBOOK</option>
                                                              </select>
                                                          </div>
                                                      </div>
                                                  </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" name="dangky" class="btn btn-danger">Đăng ký</button>
                                              <button type="button"  class="btn btn-default" data-dismiss="modal">Close</button>
                                            </div>
                                            </form>
                                          </div>
                                          
                                        </div>
                                      </div>
                                    <div class="panel-heading" style="position: relative">
                                        <h3 class="panel-title">
                                            Danh sách đăng ký
                                        </h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table class="table table-striped">
                                                        <thead>
                                                            <tr class="table-tt">
                                                                <th>STT</th>
                                                                <th>Học sinh</th>
                                                                <th>Phụ huynh</th>
                                                                <th>Số điện thoại</th>
                                                                <th>Tên người giới thiệu</th>
                                                                <th>SDT người giới thiệu</th>                                                                <th class="table-tt">Trung tâm</th>
                                                                <th>Chương trình học</th>
                                                                <th class="madt">Mã dự thưởng</th>
                                                                <th>Thời gian đăng ký</th>
                                                                <th>Hình thức đăng ký</th>
                                                                <th>Trạng thái</th>
                                                                <th>Thao tác</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if(!empty($data)){
                                                            $count = ($page - 1)*$limit + 1;
                                                            foreach ($data as $da){
                                                                ?>
                                                                <form method="post" action="./index.php">
                                                                <tr id="tr<?=$count?>">
                                                                    <td><?= $count++ ?></td>
                                                                    <td>
                                                                        <input name="ten" class="edit<?=$count?>" type="text" value="<?= $da['name'] ?>" disabled>
                                                                    </td>
                                                                    <td >
                                                                        <input name="parent" type="text" value="<?= $da['parent'] ?>" disabled class="edit<?=$count?>">
                                                                    </td>
                                                                    <td>
                                                                        <input name="phone" type="text" value="<?=$user['role'] == 1?$da['phone']:''?>" class="edit<?=$count?>" disabled>
                                                                    </td>
                                                                    <td>
                                                                        <input name="presenter_name" type="text" value="<?= $da['presenter_name'] ?>" class="edit<?=$count?>" disabled>
                                                                    </td>
                                                                    <td>
                                                                        <input name="presenter_phone" type="text" value="<?= $user['role'] == 1?$da['presenter_phone']:'' ?>" class="edit<?=$count?>" disabled>
                                                                    </td>

                                                                    <td>
                                                                        <select name="center" class="edit<?=$count?>" disabled>
                                                                            <?php foreach ($centers as $center): ?>
                                                                                <option value="<?=$center['id']?>" <?php if ($da['center'] == $center['id']): ?>
                                                                                 selected   
                                                                                <?php endif ?>
                                                                                ><?=$center['address']?></option>
                                                                            <?php endforeach ?>
                                                                        </select>

                                                                    </td>
                                                                    <td>
                                                                        <select name="product" class="edit<?=$count?>" disabled>
                                                                            <?php foreach (Process::$PRODUCTS as $key => $value): ?>
                                                                      <option value="<?=$key?>"
                                                                        <?php if ($da['product_id'] == $key): ?>
                                                                            selected
                                                                        <?php endif ?>
                                                                        ><?=Process::$PRODUCTS[$key]['name']?></option>
                                                                  <?php endforeach ?>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <?php 
                                                                            $codes = explode(',', $da['code']);
                                                                            foreach ($codes as $code) {
                                                                                 echo $code.'<br/>';
                                                                             } 
                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        <?= date("H:i d/m/Y",$da['created_time']) ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php if ($da['mode']==0) echo "<p class='text-success'>Online</p>";
                                                                        else if ($da['mode']==1)
                                                                             echo "<p class='text-danger'>Offline</p>";
                                                                        else if ($da['mode'] == 2)
                                                                            echo "<p class='text-info'>Facebook</p>";
                                                                        else echo "<p >Người giới thiệu</p>";
                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php  
                                                                        if ($da['status'] == 0){
                                                                            echo "<p class='text-danger'>Chưa liên hệ</p>";
                                                                        }
                                                                        else if ($da['status']==1){
                                                                            echo "<p class='text-warning'>Đã liên hệ</p>";
                                                                        }
                                                                        else echo "<p class='text-success'>Đã nộp tiền</p>";

                                                                        ?>
                                                                    </td>
                                                
                                                                    <td>
                                                                       <?php
                                                                            if($da['status'] && $da['status'] == 2){
                                                                                ?>
                                                                                
                                                                            <?php
                                                                                }else if ($da['status']== 1){
                                                                            ?>
                                                                                <p style="display: block;" data-key="<?= $da['created_time'] ?>" class="btn btn-primary confirm" data-toggle="modal" data-target="#notification">Xác nhận</p>
                                                                            <?php
                                                                                }else{
                                                                            ?>
                                                                                <select class="select_confirm" name="confirm_select">
                                                                                    <option value="">Chọn cách xác nhận</option>
                                                                                    <option value="<?=$da['created_time'] ?>,1">Xác nhận liên hệ</option>
                                                                                    <option value="<?=$da['created_time'] ?>,2">Xác nhận đóng tiền</option>
                                                                                </select>
                                                                            <?php  
                                                                                }
                                                                            ?>
                                                                        <p style="display: block;" id="editbtn<?=$count?>" class="btn btn-warning" onclick="edit(<?=$count?>,'<?=$user['full_name']?>')">Sửa</p>
                                                                        <button name="save" type="submit" id="save<?=$count?>" style="display: none; width: 100%;" class="btn btn-success">Lưu</button>
                                                                        <p name="reset" onclick="reset(<?=$count?>)"  id="reset<?=$count?>" style="display: none;" class="btn btn-danger">Hủy</p>
                                
                                                                    </td>
                                                                </tr>
                                                                <input type="hidden" name="id" value="<?=$da['id']?>">
                                                                </form>
                                                                <?php
                                                                }
                                                            }else{
                                                            ?>
                                                            <tr class="no-records-found">
                                                                <td colspan="9" class="text-center">Không có đăng ký nào</td>
                                                            </tr>
                                                            <?php
                                                            }
                                                            ?>
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
                                <div class="panel">
                                  <!-- Modal -->
                                  <div class="modal fade" id="editmodal" role="dialog">
                                    <div class="modal-dialog">
                                    
                                      <!-- Modal content-->
                                      <div class="modal-content">
                                        <div class="modal-header">
                                          <button type="button" class="close" data-dismiss="modal">&times;</button>
                                          <h4 class="modal-title">Modal Header</h4>
                                        </div>
                                        <div class="modal-body">
                                          <p>Some text in the modal.</p>
                                        </div>
                                        <div class="modal-footer">
                                          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
                                                <img class="img-circle img-sm img-border" src="public_files/img/profile-photos/1.png" alt="Profile Picture">
                                            </div>
                                            <a href="#profile-nav" class="box-block" data-toggle="collapse" aria-expanded="false">
                                                <span class="pull-right dropdown-toggle">
                                                    <i class="dropdown-caret"></i>
                                                </span>
                                                <p class="mnp-name"><?= $user['full_name'] ?></p>
                                            </a>
                                        </div>
                                        <div id="profile-nav" class="collapse list-group bg-trans">
                                            <a href="/change-password.php" class="list-group-item">
                                                <i class="demo-pli-gear icon-lg icon-fw"></i> Change password
                                            </a>
                                            <a href="./logout.php" class="list-group-item">
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
            <img src="public_files/img/btn.png" width="50px"/>	
        </button>
        <!--===================================================-->



    </div>
    <!--===================================================-->
    <!-- END OF CONTAINER -->

    <div id="notification" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><i class="pci-cross pci-circle"></i></button>
                    <h4 class="modal-title" id="mySmallModalLabel">Xác nhận</h4>
                </div>
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn thực hiện thao tác này?</p>
                    <p class="text-center">
                        <a href="#" class="btn btn-success mar-rgt" id="confirm_button">OK</a>
                        <button class="btn btn-danger" data-dismiss="modal">Hủy</button>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <!-- SETTINGS - DEMO PURPOSE ONLY -->
    <!--===================================================-->

    <!--===================================================-->
    <!-- END SETTINGS -->
    <script>
        $(document).ready(function() {
            $(".status_select").select2();
            $(".center_select").select2();  
            $(".status_select").select2({ width: '300px' });   
            $(".center_select").select2({ width: '300px' });    

            $("#checkbox_status").click(function(){
                if($("#checkbox_status").is(':checked') ){
                    $(".status_select > option").prop("selected","selected");// Select All Options
                    $(".status_select").trigger("change");// Trigger change to select 2
                }else{
                    $(".status_select > option").removeAttr("selected");
                    $(".status_select").trigger("change");// Trigger change to select 2
                 }
            });  

            $("#checkbox_center").click(function(){
                if($("#checkbox_center").is(':checked') ){
                    $(".center_select > option").prop("selected","selected");// Select All Options
                    $(".center_select").trigger("change");// Trigger change to select 2
                }else{
                    $(".center_select > option").removeAttr("selected");
                    $(".center_select").trigger("change");// Trigger change to select 2
                 }
            }); 
            $(".select_confirm").change(function(){
                var y = this.value.length
                var x = parseInt(this.value.split(",")[1]);
                var t = this.value.slice(0, y-2);
                if (x == 1){ 
                    result = window.confirm("Xác nhận đã liên hệ");
                }
                else if (x == 2) result = window.confirm("Xác nhận đã thu tiền")
                    
                if (result) window.location.href = "http://ketnoiyeuthuong.apaxenglish.com/index.php?confirm="+x+"&key="+t;
            })
        });
    </script>
    <script type="text/javascript" src="public_files/js/pmtb.js"></script>
    <script type="text/javascript">
        function reset(x){
            var p = document.getElementsByClassName('edit'+x);
            var tr = document.getElementById('tr'+(x-1));
            var saveBtn = document.getElementById('save'+x);
            var resetBtn = document.getElementById('reset'+x);
            var editBtn = document.getElementById('editbtn'+x);
            for (var i =0; i<p.length; i++){
                p[i].disabled = true;
            }
            tr.style.backgroundColor = "";
            saveBtn.style.display = "none";
            resetBtn.style.display = "none";
            editBtn.style.display = "block";
        }
        function edit(x, role){
            if (role === 'Admin'){
                var p = document.getElementsByClassName('edit'+x);
                var tr = document.getElementById('tr'+(x-1));
                var saveBtn = document.getElementById('save'+x);
                var resetBtn = document.getElementById('reset'+x);
                var editBtn = document.getElementById('editbtn'+x);
                 // console.log(p);
                for (var i =0; i<p.length; i++){
                    p[i].disabled = false;
                }
                tr.style.backgroundColor = "yellow";
                saveBtn.style.display = "block";
                resetBtn.style.display = "block";
                editBtn.style.display = "none";
            }
            else {
                alert("Bạn không có quyền sửa");

            }
     }
    </script>
    <style>
        table thead .madt{
            display: =
            width: 50px;
            word-wrap: break-word;
        }
    </style>
</body>
</html>