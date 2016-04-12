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
        <link rel="SHORTCUT ICON" href="<?php echo CONST_SITE_THEME_ROOT ?>favicon.ico">
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
        
        
        <!--Datepicker--> 
        <!--<script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.ui.datepicker-vi.js" type="text/javascript"></script>-->
        <script src="<?php echo SITE_ROOT; ?>public/js/DynamicFormHelper.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.chained.mini.js" type="text/javascript"></script>
        
        <!--mylib js-->
        <script src="<?php echo SITE_ROOT.'public/js'?>/mylibs.js"></script>
        <script language="javascript" type="text/javascript" src="<?php echo FULL_SITE_ROOT ?>public/submenu-hover/jquery.smartmenus.js"></script>
        <link href="<?php echo FULL_SITE_ROOT ?>public/submenu-hover/sm-core-css.css" rel="stylesheet" type="text/css">
        <link href="<?php echo CONST_SITE_THEME_ROOT ?>css/sm-blue.css" rel="stylesheet" type="text/css">
        
        <script>
            var SITE_ROOT = '<?php echo SITE_ROOT?>';
        </script>
        <link rel="stylesheet" href="<?php echo SITE_ROOT.'public/bootstrap'?>/css/datepicker.css">
        <script src="<?php echo SITE_ROOT.'public/bootstrap'?>/js/bootstrap-datepicker.js"></script>
    </head>
    <body style="position: relative">
         <?php
               $v_login_path = __DIR__.DS.'dsp_login.php';
               if(is_file($v_login_path))
               {
                   require_once  $v_login_path;
               }
           ?>
            <!--banner-->            
            <div class="col-md-12 banner">
                    <?php
                        $v_array_str = explode('.', $v_banner);
                        $v_extension = $v_array_str[count($v_array_str) - 1];
                    ?>
                    <?php if (strtolower($v_extension) == 'swf'): ?>
                        <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="<?php SITE_ROOT . "upload/flash/swflash.cab" ?>"  width="100%" height="100%">
                            <param name="movie" value="<?php echo SITE_ROOT . "upload/" . $v_banner; ?>">
                            <param name="quality" value="high">
                            <PARAM NAME="SCALE" VALUE="exactfit">
                            <embed src="<?php echo SITE_ROOT . "upload/" . $v_banner; ?>" quality="high" width="100%" height="100%" SCALE="exactfit" 
                                   pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" 
                            >
                        </object>
                    <?php else: ?>
                        <img src="<?php echo SITE_ROOT . "upload/" . $v_banner; ?>"/>
                    <?php endif; ?>
                </div>
            <div class="clear"></div>
            <!--menu header-->
            <div class="col-md-12 box-menu-top" style="">
                <div class="container">
                    <div class="header-menu">
                           <ul id="menu-bar" class="col-md-9 block">
                        <li class="home-page <?php  if(isset($this->active_menu_top) && $this->active_menu_top === 'home-page') echo ' current' ;?>">
                            <a href="<?php echo SITE_ROOT .$this->website_id ?>"></a>
                            <img src="<?php echo CONST_SITE_THEME_ROOT?>images/icon-menu-border-right.png" />
                        </li>
                        <?php $arr_all_menu_position['menu_header'] = isset($arr_all_menu_position['menu_header']) ? $arr_all_menu_position['menu_header'] : array(); ?>
                        <?php
                        $v_current_index = 0;
                        $v_selected_menu = -1;
                        for ($i = 0; $i < count($arr_all_menu_position['menu_header']); $i++):
                            $row_menu        = $arr_all_menu_position['menu_header'][$i];
                            $v_menu_id       = $row_menu['PK_MENU'];
                            $v_internal_order= $row_menu['C_INTERNAL_ORDER'];
                            $v_level_index   = strlen($v_internal_order) / 3 - 1;
                            $v_url           = $row_menu['C_URL'];
                            $v_name          = $row_menu['C_NAME'];
                            $v_menu_type     = $row_menu['C_MENU_TYPE'];
                            $v_title         = $v_name;
                            
                            $v_current_class = '';
                            
                            if($v_menu_type === 'link')
                            {
                                $v_reques_uri    = $_SERVER['REQUEST_URI'];
                                if($v_url == $v_reques_uri)
                                {
                                    $v_current_class = ' current';
                                }
                            }
                            elseif($v_menu_type === 'category')
                            {
                                $v_cat_id = get_xml_value(simplexml_load_string($row_menu['C_VALUE']), "//item[@data='1' and @type='category']/id");
                                if (isset($_GET['category_id']) == TRUE && $_GET['category_id'] == $v_cat_id) {
                                    $v_current_class = ' current';
                                    if ($v_level_index == 0) {
                                        $v_selected_menu = $v_current_index;
                                    }
                                }
                            }
                            elseif($v_menu_type === 'article')
                            {
                                $v_art_id = get_xml_value(simplexml_load_string($row_menu['C_VALUE']), "//item[@data='1' and @type='article']/article_id");
                                if (isset($_GET['article_id']) == TRUE && $_GET['article_id'] == $v_art_id) 
                                {
                                    $v_current_class = ' current';                                            
                                }
                            }
                            elseif($v_menu_type === 'module')
                            {
                                $v_current_menu = $row_menu['C_MODULE_CURRENT'];
                                if(isset($this->active_menu_top) && $this->active_menu_top === $v_current_menu)
                                {
                                    $v_current_class = ' current';   
                                }                                        
                            }
                            
                            if (isset($arr_all_menu_position['menu_header'][$i - 1]['C_INTERNAL_ORDER'])) {
                                $v_internal_order_pre = $arr_all_menu_position['menu_header'][$i - 1]['C_INTERNAL_ORDER'];
                            } else {
                                $v_internal_order_pre = '000';
                            }

                            if ($v_level_index == 0) {
                                $v_current_index++;
                            }
                            $v_level_pre = strlen($v_internal_order_pre) / 3 - 1;
                            ?>
                            <?php if ($v_level_pre == $v_level_index && $i != 0): ?>
                                </li>
                            <?php elseif ($v_level_pre < $v_level_index): ?>
                                <ul>
                                <?php
                            elseif ($v_level_pre > $v_level_index):
                                echo "</li>";
                                for ($n = 0; $n < ($v_level_pre - $v_level_index); $n++) {
                                    echo "</ul>";
                                    echo "</li>";
                                }
                                ?>
                                <?php endif; ?>                           
                                    <li class="menu-<?php echo $v_menu_id ?> <?php echo $v_current_class; ?>">
                                    <a href="<?php echo $v_url; ?>" title="<?php echo $v_title; ?>">
                                       <span><?php echo $v_name; ?></span>
                                       <img src="<?php echo CONST_SITE_THEME_ROOT?>images/icon-menu-border-right.png" />
                                    </a>
                               
                            <?php endfor; ?>
                        </ul>
                        <div class="col-md-3 Search block">
                            <form class="navbar-form" role="search"  action="<?php echo build_url_search($this->website_id) ?>" name="frmSearch" id="frmSearch">
                                <div class="input-group add-on">
                                  <input type="textbox" name="keywords" class="form-control" placeholder="<?php echo __('enter keywords')?>" name="srch-term" id="srch-term">
                                  <div class="input-group-btn">
                                    <button class="btn btn-search" type="submit"></button>
                                  </div>
                                </div>
                              </form>
                        </div>
                
                    
                    
                    
                    
                    </div>
                </div>
            </div>
            
            <div class="container">
            <div class="border-left">
            <div class="border-right">     
                 