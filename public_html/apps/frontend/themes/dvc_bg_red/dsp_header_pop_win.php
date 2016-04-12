<?php
if (!defined('SERVER_ROOT'))
{
    exit('No direct script access allowed');
}


$v_show_menu_website = isset($this->show_menu_website)?$this->show_menu_website:1;
$v_show_menu_marquee = isset($this->show_menu_marquee)?$this->show_menu_marquee:1;
$v_show_menu_header  = isset($this->show_menu_header)?$this->show_menu_header:1;

$arr_css    = isset($arr_css)?$arr_css:array();
$arr_script = isset($arr_script)?$arr_script:array();

function add_css_javascript($arr_css,$arr_script)
{
    //css
    $html_css = '';
    foreach($arr_css as $value)
    {
        $html_css .= "<link rel='stylesheet' href='". CONST_SITE_THEME_ROOT . 'css/' . $value . ".css'> \n";
    }
    echo $html_css;
    
    //javascript
    $html_javascript = '';
    foreach($arr_script as $value)
    {
        
        $html_javascript .= "<script type='text/javascript' src='". CONST_SITE_THEME_ROOT ."js/". $value .".js'></script>\n";
    }
    echo $html_javascript;
    
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta http-equiv="Cache-Control" content="no-cache"/>
        <!--<link rel="SHORTCUT ICON" href="<?php echo SITE_ROOT ?>favicon.ico">-->
        <?php if (isset($v_description)): ?>
        <meta name="description" content="<?php echo remove_html_tag($v_description)?>" />
        <?php endif; ?>
        <title><?php echo $title; ?> - <?php echo _CONST_UNIT_NAME; ?></title>
        <?php if (isset($v_keywords)): ?>
            <?php if ($v_keywords == ''){$v_keywords = $title;}?>
            <meta name="keywords" content="<?php echo remove_html_tag($v_keywords); ?>" />
        <?php endif; ?>
        <!--su dung cho light box-->
        <script>
            var site_theme_root = '<?php echo CONST_SITE_THEME_ROOT?>';
        </script>
        <!--jquery-->
        <script type="text/javascript" src="<?php echo CONST_SITE_THEME_ROOT ?>js/jquery.min.js"></script>
        
        <!--bootstrap-->
        <link rel="stylesheet" href="<?php echo SITE_ROOT.'public/bootstrap'?>/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo SITE_ROOT.'public/bootstrap'?>/css/bootstrap-theme.min.css">
        <script src="<?php echo SITE_ROOT.'public/bootstrap'?>/js/bootstrap.min.js"></script>
        
        <!--main css-->
        <link   type="text/css"  rel="stylesheet" href="<?php echo CONST_SITE_THEME_ROOT ?>css/widget.css" />
        <link   type="text/css"  rel="stylesheet" href="<?php echo CONST_SITE_THEME_ROOT ?>css/main.css" />
        
        <?php add_css_javascript($arr_css, $arr_script);?>
        
        <script>
            var SITE_ROOT = '<?php echo SITE_ROOT?>';
        </script>
    </head>
    <body>
        <div class="container">
            <div class="clear"></div>
         