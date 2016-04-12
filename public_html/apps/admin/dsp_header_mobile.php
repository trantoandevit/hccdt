<?php
if (!defined('SERVER_ROOT'))
{
    exit('No direct script access allowed');
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="MobileOptimized" content="100" />
        
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta http-equiv="Cache-Control" content="no-cache"/>
        <title><?php echo $this->eprint($this->title); ?></title>
        
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.min.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery-ui.min.js" type="text/javascript"></script>
        <link href="<?php echo SITE_ROOT; ?>public/js/jquery/jquery-ui.css" rel="stylesheet" type="text/css"/>
        
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>apps/admin/style.css" type="text/css"  />
        <!--style mobile-->
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>apps/admin/style_mobile.css" type="text/css"  />
        
        <!--combobox-->
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquerycombobox/js/jquery.combobox.js" type="text/javascript"></script>
        <link href="<?php echo SITE_ROOT; ?>public/js/jquery/jquerycombobox/css/style.css" rel="stylesheet" type="text/css"/>
        
        <script type="text/javascript">
            var SITE_ROOT='<?php echo SITE_ROOT; ?>';
            var _CONST_LIST_DELIM = '<?php echo _CONST_LIST_DELIM; ?>';
        </script>
        <!--  Modal dialog -->
        <script src="<?php echo SITE_ROOT; ?>public/js/submodal.js" type="text/javascript"></script>
        <link href="<?php echo SITE_ROOT; ?>public/css/subModal.css" rel="stylesheet" type="text/css"/>

        <!-- Tooltip -->
        <script src="<?php echo SITE_ROOT; ?>public/js/overlib_mini.js" type="text/javascript"></script>

        <script src="<?php echo SITE_ROOT; ?>public/js/mylibs.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/DynamicFormHelper.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/auto-slug.js"></script>

        <?php if (isset($this->local_js)): ?>
            <!-- Module JS -->
            <script src="<?php echo $this->local_js; ?>" type="text/javascript"></script>
        <?php endif; ?>
    </head>
    <body>
       <!-- header mobile -->
       <div class="banner-mobile" >
           <div class="div-mobile-logo" >
               <img class="mobile-logo" src="<?php echo SITE_ROOT . $this->template_directory . 'images/logo.png'?>" >
           </div>
           <div class="div-mobile-search" >
                   <input id="txt_search" name="txt_search" class="mobile-search-box" type="text" style="height: 23px;width: 80%;">
                   <input type="button" onclick="btn_search_onclick();" value="" class="ButtonSearch">
           </div>
       </div>
       <!-- menu mobile -->
       <div class="clear"></div>
       <div style="height: 60px;width: 100%;border-bottom: 1px #A4D021 solid">
           <ul class="mobile-menu">
               <li>
                   <a class="mobile-menu-selected" href="javascript:void(0)" data-ajax="arp_approve_article/2" onclick="menu_mobile_onclick(this)">
                       Tin bài chờ duyệt
                   </a>
               </li>
               <li>
                   <a class="" href="javascript:void(0)" data-ajax="arp_approve_article/3" onclick="menu_mobile_onclick(this)" >
                       Tin bài duyệt gần đây
                   </a>
               </li>
               <li>
                    <?php
                        Session::init();

                        $v_user_name = Session::get('user_name');
                    ?>
                   <label >
                       <?php echo $v_user_name?>
                        (<a style="color: #0000EE" href="<?php echo SITE_ROOT.'logout.php'?>">
                            <?php echo __('logout');?>
                        </a>)
                   </label>
               </li>
           </ul>
       </div>