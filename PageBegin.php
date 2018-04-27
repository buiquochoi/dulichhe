<!DOCTYPE html>
<html>
    <head>
        <?
        CGlobal::$website_title .= '';
        ?>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <title><?= CGlobal::$website_title ?></title>

        <? if(CGlobal::$meta_tags){
            ?>
            <?= CGlobal::$meta_tags ?>
            <?
        }else{
            ?>
            <meta name="title" content="<?= CGlobal::$website_title ?>"/>
            <meta name="KEYWORDS" content="Apax, apaxenglish, apaxenglish.com, hoc tieng anh online, hoc anh van online, học tiếng anh online, hoc tieng anh truc tuyen, học tiếng anh trực tuyến, hoc tieng anh qua mang, tieng anh online">
            <meta name="DESCRIPTION" content="ApaxEnglish cung cấp phương pháp học tiếng Anh Một thầy - Một trò cực kỳ hiệu quả cho những người muốn giỏi tiếng Anh thật sự!">
            <meta name="og:title" content="ApaxEnglish - Học tiếng anh trực tuyến - Học tiếng anh online - Learning and Sharing"/>
            <meta name="og:description" content="ApaxEnglish cung cấp phương pháp học tiếng Anh Một thầy - Một trò cực kỳ hiệu quả cho những người muốn giỏi tiếng Anh thật sự!"/>
            <meta name="og:url" content="http://apaxenglish.com/"/>
            <meta name="og:image" content="<?= STATIC_BASE_URL . 'images/fb_share.png' ?>"/>
            <?
        } ?>

        <meta http-equiv="EXPIRES" content="0"/>
        <meta rel="canonical" href="http://apaxenglish.com">
        <meta name="RESOURCE-TYPE" content="DOCUMENT"/>
        <meta name="DISTRIBUTION" content="GLOBAL"/>
        <meta name="AUTHOR" content="ECHATDotVN"/>
        <meta name="COPYRIGHT" content="Copyright (c) by apaxenglish.com"/>
        <? if (isset($_GET['page']) && in_array($_GET['page'], CGlobal::$pg_noIndex)) { ?>
        <meta name="ROBOTS" content="NOINDEX, NOFOLLOW, NOARCHIVE, NOSNIPPET"/>
        <? } else { ?>
            <meta name="ROBOTS" content="<?= CGlobal::$robotContent ?>"/>
            <meta name="Googlebot" content="<?= CGlobal::$gBContent ?>">
        <? } 
        ?>
        <meta name="RATING" content="GENERAL"/>
        <meta name="GENERATOR" content="apax"/>
        <meta name="geo.region" content="Vietnam"/>
	<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-MZ2TFGD');</script>
<!-- End Google Tag Manager -->
	

        <link rel="shortcut icon" href="<?= STATIC_URL ?>public/images/favicon.ico?v=1.2"/>

        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0" >
        <!-- <link href='http://fonts.googleapis.com/css?family=Roboto:400,100,700,900,300' rel='stylesheet' type='text/css'> -->


        <link href="<?= STATIC_BASE_URL ?>static_math/ap_assets/bootstrap/css/bootstrap.min.css" rel=stylesheet>
        <link href="<?= STATIC_BASE_URL ?>static_math/css/font-awesome.min.css" rel="stylesheet">
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

        <script src="<?= STATIC_URL ?>public/js/jquery-2.1.4.js"></script>
<!--        <script src="--><?//= STATIC_BASE_URL ?><!--static_math/assets/9af5610a/jquery.js"></script>-->
        <script src="<?= STATIC_BASE_URL ?>static_math/ap_assets/bootstrap/js/bootstrap.min.js"></script>
<!--        <script src="--><?//= STATIC_URL ?><!--javascript/bootstrap.min.js"></script>-->
<!--        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" type="text/css"/>-->

        <?php
        $page_arr = array('page', 'module', 'user', 'edit_page', 'admin', 'emath');

        if (($_SERVER['REQUEST_URI'] != '') && ($_SERVER['REQUEST_URI'] != '/') && (in_array(Url::get('page'), $page_arr) || strpos($_SERVER['REQUEST_URI'], 'admin_'))) {
            ?>
            <script src="<?= STATIC_URL ?>javascript/admin.js"></script>
            <link href="<?= STATIC_URL ?>style/admin.css" rel="stylesheet">
        <?php } else { ?>

<!--            <link href="--><?//= STATIC_BASE_URL ?><!--static_math/css/font-awesome.min.css" rel="stylesheet">-->
            <link href="<?= STATIC_BASE_URL ?>static_math/css/site.css" rel="stylesheet">
            <link href="<?= STATIC_BASE_URL ?>static_math/css/course.css" rel="stylesheet">
<!--            <link href="--><?//= STATIC_BASE_URL ?><!--static_math/ap_assets/bootstrap/css/bootstrap.min.css" rel=stylesheet>-->
            <link href="<?= STATIC_BASE_URL ?>static_math/ap_assets/css/style.css" rel=stylesheet>
            <link href="<?= STATIC_BASE_URL ?>static_math/css/responsive.css" rel="stylesheet">
            <link href="<?= STATIC_BASE_URL ?>public/css/new_style.css" rel="stylesheet">
            <link href="<?= STATIC_BASE_URL ?>public/css/custom.css" rel="stylesheet">
            <link href="<?= STATIC_BASE_URL ?>public/css/custom-responsive.css" rel="stylesheet">

            <?php
                $jssor_page = array();
                if (in_array(Url::get('page'),$jssor_page)){
            ?>
            <script language="javascript" type="text/javascript" src="<?= STATIC_BASE_URL ?>static_math/ap_assets/js/jssor.slider.min.js"></script>
            <script language="javascript" type="text/javascript" src="<?= STATIC_BASE_URL ?>static_math/ap_assets/js/fn.js"></script>
            <?php
                }
            ?>
            <script type="text/javascript" src="<?= STATIC_BASE_URL ?>public/js/new-custom.js"></script>
            <script src="<?= STATIC_BASE_URL ?>public/js/slides.min.jquery.js"></script>
            <script src="<?= STATIC_BASE_URL ?>public/js/main.js" type="text/javascript"></script>
	

            <script type="text/javascript">
                $(document).ready(function () {
                    $('#slides').slides({
                        effect: 'fade',
                        fadeSpeed: 700,
                        play: 7000,
                        pause: 4000,
                        generatePagination: false,
                        crossfade: true,
                        hoverPause: true,
                        animationStart: function (current) {
                            $('.caption').animate({right: -500}, 200);
                        },
                        animationComplete: function (current) {
                            $('.caption').animate({right: 35}, 300);
                        },
                        slidesLoaded: function () {
                            $('.caption').animate({right: 35}, 100);
                        }
                    });
                })
            </script>

        <?php } ?>
        <script type="text/javascript">
            var BASE_URL = <?= '\''.STATIC_URL.'\'' ?>;
        </script>
<!--  <script src="<?= STATIC_URL ?>javascript/library.js"></script>-->

        <?php echo EClass::$extraHeaderCSS; ?>

        <?php echo EClass::$extraHeaderJS; ?>


        <script language="javascript">
            var query_string = "?<?= urlencode($_SERVER['QUERY_STRING']); ?>", BASE_URL = "<?= WEB_ROOT ?>", TINYMCE = "<?= TINYMCE ?>", WEB_DIR = "<?= WEB_DIR ?>";
<?php
echo 'var IS_ROOT = ' . (int) User::is_root() . ', IS_ADMIN = ' . (int) User::is_admin() . ', IS_MOD=' . (int) User::is_mod() . ', IS_LOGIN = ' . (User::is_login() ? User::id() : 0) . ',EB_USER_NAME = "' . (User::is_login() ? User::user_name() : '') . '", IS_BLOCK=' . (User::is_block() ? 1 : 0) . ', CUR_AREA = ' . CGlobal::$curArea . ';';
?>
        </script>

        <?= EClass::$extraHeader; ?>
    </head>

    <body>
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MZ2TFGD"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>

        <div class="wrap ">

