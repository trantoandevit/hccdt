<?php
if (!defined('SERVER_ROOT'))
{
    exit('No direct script access allowed');
}
    
    $title                  = __('print');
    $v_article_sub_title    = isset($arr_single_article['C_SUB_TITLE'])?$arr_single_article['C_SUB_TITLE']:'';
    $v_article_title        = isset($arr_single_article['C_TITLE'])?$arr_single_article['C_TITLE']:'';
    $v_begin_date           = isset($arr_single_article['C_BEGIN_DATE'])?$arr_single_article['C_BEGIN_DATE']:'';
    
    $v_article_sumary       = isset($arr_single_article['C_SUMMARY'])?$arr_single_article['C_SUMMARY']:'';
    $v_article_sumary       = htmlspecialchars_decode($v_article_sumary);
    
    $v_article_cotent       = isset($arr_single_article['C_CONTENT'])?$arr_single_article['C_CONTENT']:'';
    $v_article_cotent = htmlspecialchars_decode($v_article_cotent);
            
    $v_pen_name             = isset($arr_single_article['C_PEN_NAME'])?$arr_single_article['C_PEN_NAME']:'';
    
    $pattern           = "/\[VIDEO\](.*)\[\/VIDEO\]/i";
    $v_article_cotent = preg_replace($pattern, '', $v_article_cotent,-1);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta http-equiv="Cache-Control" content="no-cache"/>
        <link rel="SHORTCUT ICON" href="<?php echo CONST_SITE_THEME_ROOT ?>favicon.ico">
        <title><?php echo $v_article_title;?></title>
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/css/reset.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/css/text.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo CONST_SITE_THEME_ROOT ?>css/style.css" type="text/css" media="screen" />
        <!--[if gte IE 9]>
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>apps/frontend/themes/haiduong/css/style_ie.css" type="text/css" media="screen" />
        <![endif]-->

        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.min.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery-ui.min.js" type="text/javascript"></script>
        <link href="<?php echo SITE_ROOT; ?>public/js/jquery/jquery-ui.css" rel="stylesheet" type="text/css"/>
        <!--  Datepicker -->
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.ui.datepicker-vi.js" type="text/javascript"></script>
        <!--  Treeview -->
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.treeview.js" type="text/javascript"></script>
        <link href="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.treeview.css" rel="stylesheet" type="text/css"/>

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
        
        <!-- Upload -->
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.MultiFile.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.MetaData.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.blockUI.js" type="text/javascript"></script>
        

        <!--[if IE 9]>
        <link href="<?php echo CONST_SITE_THEME_ROOT ?>css/container-ie9.css" rel="stylesheet" type="text/css" />
        <![endif]-->
        <!--[if IE 8]>
        <link href="<?php echo CONST_SITE_THEME_ROOT ?>css/container-ie8.css" rel="stylesheet" type="text/css" />
        <![endif]-->
        <!--[if IE 7]>
        <link href="<?php echo CONST_SITE_THEME_ROOT ?>css/container-ie7.css" rel="stylesheet" type="text/css" />
        <![endif]-->
        <!--[if lte IE 6]>
        <link href="<?php echo CONST_SITE_THEME_ROOT; ?>css/container-ie6.css" rel="stylesheet" type="text/css" />
        <![endif]-->
        <script  src="<?php echo CONST_SITE_THEME_ROOT; ?>js/boxover.js" type="text/javascript"></script>
        <style>
            @media screen {
                #div_content_print {
                    width: 600px;
                    margin: 0 auto;
                }
            }
            @media print
            {
                .img_print {
                    display: none;
                }
                
                #div_content_print {
                    width: 100%;
                }
                .footer-info
                {
                    border-top: solid 1px #CCCCCC;
                    padding-top: 5px
                }
            }
            
        </style>
    </head>
<body>
<div id="div_content_print">
     <div  style="margin: 0 auto;width: 100%">
        <div id="div_banner" class="div_banner" style="position: relative">
            <?php 
                $v_array_str = explode('.', $v_banner);
                $v_extension    = $v_array_str[count($v_array_str)-1];
            ?>
            <?php if($v_extension == 'swf'):?>
                <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="<?php SITE_ROOT."upload/flash/swflash.cab"?>" width="1004" height="130">
                    <param name="movie" value="">
                    <param name="quality" value="high"><param name="wmode" value="transparent">
                    <embed src="<?php echo SITE_ROOT."upload/".$v_banner;?>" quality="high" 
                           pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" 
                           width="100%" height="130">
                </object>
            <?php else:?>
            <center><img src="<?php echo SITE_ROOT."upload/".$v_banner;?>" width="100%"></center>
            <?php endif;?>
        </div>
    </div>
    <div  style="border-bottom: 1px solid #000000;margin-top: 10px;width: 100%;" ></div>
    <div class="div_article" style="margin-bottom: 10px;">
        <div style="margin-top: 10px;padding: 5px 5px 5px 0px;width: 100%;overflow: hidden;" class="img_print">
            <a href="javascript:window.print()" style="float: left;text-decoration: none;">
                <img src="<?php echo CONST_SITE_THEME_ROOT . "images/icon_print.png"; ?>">
                <?php echo __('print') ?>
            </a>
        </div>
        <div class="clear"></div>
        <div class="div_sub_title">
            <?php echo $v_article_sub_title;?>
        </div>
        <div class="div_article_title">
            <h2><?php echo $v_article_title;?></h2>
        </div>
        <div class="div_article_begin_date">
            (<?php echo $v_begin_date;?>)
        </div>
        <div class="div_article_summary">
            <?php echo $v_article_sumary;?>
        </div>
         <div class="div_article_content">
            <?php echo $v_article_cotent;?>
        </div>
        <div class="clear" style="height: 8px;"></div>
        <div class="div_pen_name" style="float:right">
            <?php echo $v_pen_name;?>
        </div>
         <div style="margin-top: 10px;padding: 5px 5px 5px 0px;width: 100%;overflow: hidden;" class="img_print">
            <a href="javascript:window.print()" style="float: right;text-decoration: none;">
                <img src="<?php echo CONST_SITE_THEME_ROOT . "images/icon_print.png"; ?>">
                <?php echo __('print') ?>
            </a>
        </div>
        <div style="background-color: #F0F0F0;">
          <div class="col-md-12 footer-info" >
            <?php require_once __DIR__.'/dsp_content_footer.php';?>       
        </div>
    </div>
        </div>
    
</div>

</body>
</html>
