<?php
if (!defined('SERVER_ROOT'))
{
    exit('No direct script access allowed');
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta http-equiv="Cache-Control" content="no-cache"/>
        <link rel="SHORTCUT ICON" href="<?php echo SITE_ROOT ?>favicon.ico">
        <title><?php echo $this->eprint($this->title); ?></title>
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/css/reset.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/css/1008_24_0_0.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/css/text.css" type="text/css" media="screen" />
        
        
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.min.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery-ui.min.js" type="text/javascript"></script>
        <link href="<?php echo SITE_ROOT; ?>public/js/jquery/jquery-ui.css" rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>apps/admin/style.css" type="text/css"  />
        
        <!--  Datepicker -->
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.ui.datepicker-vi.js" type="text/javascript"></script>
        <!--  Treeview -->
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.treeview.js" type="text/javascript"></script>
        <link href="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.treeview.css" rel="stylesheet" type="text/css"/>
        
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

        <!-- Upload -->
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.MultiFile.pack.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.blockUI.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.MetaData.js" type="text/javascript"></script>
         
        <script>
            <?php
                if(check_file_htaccess())
                {
                    echo 'QS = "'.DS . '\";' ;
                }
                else
                {
                    echo 'QS = "?"';
                }
            ?>

        </script>
        
        <?php if (isset($this->local_js)): ?>
            <!-- Module JS -->
            <script src="<?php echo $this->local_js; ?>" type="text/javascript"></script>
        <?php endif; ?>
        <!--css ie-->
        <!--[if IE]>
       <link href="<?php echo SITE_ROOT; ?>apps/admin/style_ie.css" rel="stylesheet" type="text/css" />
       <![endif]-->
    </head>
    <body>
        <?php
        Session::init();
        $arr_all_grant_website = $this->arr_all_grant_website; //
        $arr_all_lang          = $this->arr_all_lang;
        $arr_count_article     = $this->arr_count_article;
        //var_dump($arr_all_grant_website);

        $v_user_login_name = Session::get('user_login_name');

        $v_session_website_id = isset($_SESSION['session_website_id']) ? $_SESSION['session_website_id'] : '';
        $v_session_lang_id    = isset($_SESSION['session_lang_id']) ? $_SESSION['session_lang_id'] : '';
        $v_show_div_website   = (isset($this->show_div_website) && $this->show_div_website == FALSE) ? FALSE : TRUE;

        $v_menu_select = session::get('menu_select');
        echo view::hidden('hdn_menu_select', $v_menu_select);
        ?>

        <DIV id=overDiv style="Z-INDEX: 10000; VISIBILITY: hidden; POSITION: absolute"></DIV>
        <div class="container_24">
            <div id="header_admin">
                <div>
                    <div class="Banner">
                        <table cellpadding="0" cellspacing="0" class="TableBannerMenu none-border-table" align="right" border="0">
                            <tr>
                                <td class="BannerMenu">
                                    <img src="<?php echo SITE_ROOT . "public/images/icon_bannermenu_account.gif"; ?>" alt="" style="vertical-align: middle">
                                    <a  href="javascript:void(0)" onclick="change_password_onclick();" style="cursor:pointer;">
                                        <span id="ctl00_lblTopMenuAccount" class="BannerMenu"><?php echo __('account'); ?></span></a>
                                </td>
                                <td class="BannerMenu">
                                    <img src="<?php echo SITE_ROOT . "public/images/icon_bannermenu_help.gif"; ?>" alt="" style="vertical-align: middle">
                                    <a href="javascript:void(0)">
                                        <span id="ctl00_lblTopMenuHelp" class="BannerMenu"><?php echo __('help'); ?></span></a>
                                </td>
                                <td class="BannerMenuLast">
                                    <img src="<?php echo SITE_ROOT . "public/images/icon_bannermenu_exit.gif"; ?>" alt="" style="vertical-align: middle">
                                    <a href="<?php echo SITE_ROOT . "logout.php" ?>">
                                        <span id="ctl00_lblTopMenuExit" class="BannerMenu"><?php echo __('logout'); ?>(<span class="LoginName"><?php echo $v_user_login_name; ?></span>)</span></a>
                                </td>
                            </tr>
                            <tr valign="top">
                                <td class="CounterStatistic" colspan="3">


                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="Aticle_info">
                    <label><?php echo __('information'); ?>: <?php echo __('total pending articles'); ?>:(<?php echo $arr_count_article['count_approval']; ?>) - <?php echo __('total redone articles'); ?>: (<?php echo $arr_count_article['count_editor']; ?>)</label>
                </div>
                <div id="header_admin_bot" class="TopMenuBound">
                    <?php if ($v_show_div_website == TRUE): ?>
                        <div class="div_website">
                            <div class="website_detail">
                                <select style="width: 159;height: 20" name="select_website" id="select_website" onchange="choose_website_onchange()">

                                    <?php foreach ($arr_all_grant_website as $key => $value): ?>
                                        <option value="<?php echo $key; ?>" 
                                        <?php
                                        if ($key == $v_session_website_id)
                                        {
                                            echo 'selected';
                                        }
                                        ?>
                                                > 
                                                    <?php echo $value; ?> 
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <select name="select_lang" id="select_lang" onchange="choose_lang_onchange()"> 
                                    <?php foreach ($arr_all_lang as $key => $value): ?>
                                        <option value="<?php echo $key; ?>" <?php echo ($v_session_lang_id == $key) ? 'selected' : ''; ?>>
                                            <?php echo $value; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="website_name"><?php echo __('website') ?>:</div>
                        </div>
                    <?php endif ?>
                    <div class="TopMenuAdmin">
                        <div style="float: left;width: 100%;">
                            <div style="width: 10px;height: 40px;float: left;"></div>
                            <div class="TopMenu" name="div_menu_content" id="div_menu_content">
                                <a href="javascript:void(0)" onclick="set_cookie_menu('div_menu_content')">
                                    <span><?php echo __('content management') ?></span>
                                </a>
                            </div>
                            <div class="TopMenu" name="div_menu_interactive" id="div_menu_interactive">
                                <a href="javascript:void(0)" onclick="set_cookie_menu('div_menu_interactive')">
                                    <span><?php echo __('citizen interaction') ?></span>
                                </a>
                            </div>
                            <div class="TopMenu" name="div_menu_system" id="div_menu_system">
                                <a href="javascript:void(0)" onclick="set_cookie_menu('div_menu_system')">
                                    <span> <?php echo __('system administration') ?></span>
                                </a>
                            </div>
                            <div class="TopMenu" name="div_menu_report" id="div_menu_report">
                                <a href="javascript:void(0)" onclick="set_cookie_menu('div_menu_report')">
                                    <span> <?php echo __('report statistics') ?></span>
                                </a>
                            </div>
                        </div>
                        <div class="clear"></div>
                        <div style="border-top:solid 1px #438cb4;"></div>
                    </div>
                </div>

            </div> <!--header admin-->
            <div class="clear"></div>
            <div class="grid_24" style="background-color: #EBEBEB;">
                <?php
                //$arr_url       = explode('/', $_GET['url']);
                //$v_menu_active = $arr_url[1];
                ?>
                <?php if ($this->dsp_side_bar == true): ?>
                    <div class="grid_4" style="min-height: 1px;">
                        <?php include 'dsp_admin_menu.php'; ?>
                    </div>
                    <div class="grid_20" style="background-color: white;">
                        <div style="padding-left: 4px;">

                        <?php else: ?>
                            <div class="grid_24" style="background-color: white">
                                <div>
                                <?php endif; ?>

                                <?php

                                function get_admin_controller_url($module,$type = NULL)
                                {
                                    if (file_exists(SERVER_ROOT . '.htaccess'))
                                    {
                                        return SITE_ROOT . 'admin/' . $module . '/'.$type;
                                    }
                                    return SITE_ROOT . 'index.php?url=admin/' . $module . '/'.$type;
                                }
                                ?>
<script>
    var SITE_ROOT = "<?php echo SITE_ROOT; ?>";
    $(document).ready(function(){
        var div_select = '#'+$('#hdn_menu_select').val();
        //alert($('#hdn_menu_select').attr('value'));
        $(div_select).attr('class','TopMenuActive');
    });
    function choose_website_onchange()
    {
        var lang_id     = $('#select_lang').val();
        var website_id  = $('#select_website').val();
        var html = '<form name="form_submit" id="form_submit" method="POST" action=""></form>';
        $('#header_admin').append(html);
        $('#form_submit').attr('action','<?php echo get_admin_controller_url('dashboard/do_change_session_website_id'); ?>&website_id='+website_id+'&lang_id='+lang_id);
        $('#form_submit').submit();
    }
    function choose_lang_onchange()
    {
        var lang_id     = $('#select_lang').val();
        var website_id  = 0;
        var html = '<form name="form_submit" id="form_submit" method="POST" action=""></form>';
        $('#header_admin').append(html);
        $('#form_submit').attr('action','<?php echo get_admin_controller_url('dashboard/do_change_session_website_id'); ?>&website_id='+website_id+'&lang_id='+lang_id);
        $('#form_submit').submit();
    }
    function change_password_onclick()
    {
        var url="<?php echo get_admin_controller_url('dashboard/dsp_change_password'); ?>&pop_win=1";
        showPopWin(url,800,500);
    }
    function set_cookie_menu(value)
    {
        var html = '<form name="form_submit" id="form_submit" method="POST" action=""></form>';
        $('#header_admin').append(html);
        $('#form_submit').attr('action','<?php echo get_admin_controller_url('dashboard/do_change_session_menu_select'); ?>'+value);
        $('#form_submit').submit();
    }
</script>