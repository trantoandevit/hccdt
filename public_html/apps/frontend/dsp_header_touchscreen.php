<?php
if (!defined('SERVER_ROOT'))
{
    exit('No direct script access allowed');
}
$this->active = isset($this->active)?$this->active:'district';
$this->left_menu = isset($this->left_menu)?$this->left_menu:TRUE;
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta http-equiv="Cache-Control" content="no-cache"/>
        <!--<link rel="SHORTCUT ICON" href="<?php echo SITE_ROOT ?>favicon.ico">-->
        <?php if (isset($this->description)): ?>
        <meta name="description" content="<?php echo remove_html_tag($this->description)?>" />
        <?php endif; ?>
        
        <title><?php echo $this->title; ?> - <?php echo _CONST_UNIT_NAME; ?></title>
        <?php if (isset($this->v_keywords)): ?>
            <?php if ($this->v_keywords == ''){$this->v_keywords = $this->title;}?>
            <meta name="keywords" content="<?php echo remove_html_tag($this->v_keywords); ?>" />
        <?php endif; ?>
        <!--main css-->
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>apps/frontend/style_touchscreen.css" type="text/css"  />
        <!--su dung cho light box-->
        <script>
            var site_theme_root = '<?php echo CONST_SITE_THEME_ROOT?>';
        </script>
        <!--jquery-->
        <script type="text/javascript" src="<?php echo SITE_ROOT.'public/bootstrap/'?>js/jquery.min.js"></script>
        
        <!--bootstrap-->
        <link rel="stylesheet" href="<?php echo SITE_ROOT.'public/bootstrap'?>/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo SITE_ROOT.'public/bootstrap'?>/css/bootstrap-theme.min.css">
        <script src="<?php echo SITE_ROOT.'public/bootstrap'?>/js/bootstrap.min.js"></script>
        
        <!--mylib js-->
        <script src="<?php echo SITE_ROOT.'public/js'?>/mylibs.js"></script>
        
        <script>
            var SITE_ROOT = '<?php echo SITE_ROOT?>';
        </script>
        <link rel="stylesheet" href="<?php echo SITE_ROOT.'public/bootstrap'?>/css/datepicker.css">
        <script src="<?php echo SITE_ROOT.'public/bootstrap'?>/js/bootstrap-datepicker.js"></script>
    </head>
    <body>
    <div class='row'>
        <div class='row header'>
            <div class='col-sm-12 '>
                <!--button home and back-->
                <div class='col-sm-4'>
                    <button type="button" class="btn btn-primary btn-sm" onclick="window.location.href='<?php echo $this->controller_url?>';">
                        <span class="glyphicon glyphicon-home"></span>
                        Trang chủ
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="window.history.go(-1);">
                        <span class="glyphicon glyphicon-arrow-left"></span>
                        Quay lại
                    </button>
                </div>
                <div class='col-sm-8'>
                    <span class="title" >
                        Đánh giá cán bộ tiếp nhận và trả kết quả
                    </span>
                </div>
            </div>
        </div>
        <div class="row content">
            <div class="col-sm-12">
                <?php if($this->left_menu == TRUE):?>
                <!--left side bar-->
                <div class="col-sm-4">
                    <ul class="menu-left">
                        <li class="<?php echo ($this->active == 'department')?'active':'';?>">
                            <a href="<?php echo $this->controller_url . '/department'?>">Sở / Ngành</a>
                            <span class="icon-menu glyphicon glyphicon-play-circle"></span>
                        </li>
                        <li class="<?php echo ($this->active == 'district')?'active':'';?>">
                            <a href="<?php echo $this->controller_url . '/district'?>">Quận / Huyện</a>
                            <span class="icon-menu glyphicon glyphicon-play-circle"></span>
                        </li>
                        <li class="<?php echo ($this->active == 'village')?'active':'';?>">
                            <a href="<?php echo $this->controller_url . '/village'?>">Phường / Xã / Thị Trấn</a>
                            <span class="icon-menu glyphicon glyphicon-play-circle"></span>
                        </li>
                    </ul>
                </div>
                <!--main content-->
                <div class="col-sm-8 main-content">
                <?php else:?>
                <div class="col-sm-12 main-content">    
                <?php endif; ?>

                
                 