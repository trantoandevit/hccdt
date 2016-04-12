<?php

if (!defined('SERVER_ROOT'))
{
    exit('No direct script access allowed');
}
//header
$this->template->title = get_system_config_value('unit_name');

$this->template->display('dsp_header.php');
@session::init();
if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
{
    $ip = $_SERVER['HTTP_CLIENT_IP'];
}
elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
{
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}
else
{
    $ip = $_SERVER['REMOTE_ADDR'];
}

?>
<div>
    <table cellpadding="0" cellspacing="0" width="100%" class="TableContent">
        <tr>
            <!--thong tin nguoi dung-->
            <td valign="top" class="LeftContent">
                <table width="100%" cellpadding="0" cellspacing="0" class="TableLoginInfo">
                    <tr>
                        <td class="Header_default" colspan="2">
                            &nbsp;<span><?php echo __('login information'); ?></span>
                        </td>
                    </tr>
                    <tr><td style="height:9px"></td></tr>
                    <tr>
                        <td class="LoginInfoLeftContent"></td>
                        <td class="LoginInfoRightContent">
                            <table width="100%" cellpadding="0" cellspacing="0" class="TableLoginInfoContent">
                                <tr>
                                    <td class="LoginInfo">
                                        <span><?php echo __('user'); ?></span>:
                                        <br>
                                        <span class="UserName"><?php echo session::get('user_name') ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="LoginInfo">
                                        <span><?php echo __('position'); ?></span>:
                                        <span><?php echo session::get('user_job_title'); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="LoginInfo">
                                        <span><?php echo __('unit'); ?></span>:
                                        <span><?php echo session::get('ou_name'); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="LoginInfo">
                                        <span><?php echo __('login times'); ?></span>:
                                        <span><?php echo session::get('time_to_join'); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="LoginInfo">
                                        <span><?php echo __('ip address'); ?></span>:
                                        <span><?php echo $ip; ?></span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
            <td valign="top" class="RightContent">
                <!--div quan tri noi dung-->
                <div id="dashboard_content">
                    <table id="ctl00_ContentPlaceHolderContent_AdminLeftMenu_dtlMenu" class="TableLeftMenuOverview" cellspacing="0" align="Left" border="0" style="border-width:0px;width:180px;border-collapse:collapse;">
                        <tr>
                            <?php if (session::check_permission('QL_DANH_SACH_CHUYEN_MUC') > 0): ?>
                                <td class="LeftMenuOverview">
                                    <div class="Content">
                                        <div id="ctl00_ContentPlaceHolderContent_AdminLeftMenu_dtlMenu_ctl00_divRoleImage">
                                            <img alt="" onclick="javascript:window.location='<?php echo $this->get_controller_url('category'); ?>'" id="imgSubRole" style="cursor:pointer;" width="99" height="84" src="<?php echo SITE_ROOT . "public/images/CategoryLogo.gif" ?>">
                                        </div>
                                        <div class="Item">
                                            <a href="<?php echo $this->get_controller_url('category'); ?>">
                                                <span><?php echo __('category'); ?></span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            <?php endif; ?>
                            <?php if (session::check_permission('QL_DANH_SACH_SU_KIEN') > 0): ?>
                                <td class="LeftMenuOverview">
                                    <div class="Content">
                                        <div id="ctl00_ContentPlaceHolderContent_AdminLeftMenu_dtlMenu_ctl02_divRoleImage">
                                            <img alt="" onclick="javascript:window.location='<?php echo $this->get_controller_url('event'); ?>'" id="imgSubRole" style="cursor:pointer;" width="99" height="84" src="<?php echo SITE_ROOT . "public/images/EventLogo.gif" ?>">
                                        </div>
                                        <div class="Item">
                                            <a href="<?php echo $this->get_controller_url('event'); ?>">
                                                <span><?php echo __('event'); ?></span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            <?php endif; ?>
                            <?php if (session::check_permission('QL_DANH_SACH_QUANG_CAO') > 0): ?>
                                <td class="LeftMenuOverview">
                                    <div class="Content">
                                        <div id="ctl00_ContentPlaceHolderContent_AdminLeftMenu_dtlMenu_ctl04_divRoleImage">
                                            <img alt="" onclick="javascript:window.location='<?php echo $this->get_controller_url('advertising'); ?>'" id="imgSubRole" style="cursor:pointer;" width="99" height="84" src="<?php echo SITE_ROOT . "public/images/AdvLogo.gif" ?>">
                                        </div>
                                        <div class="Item">
                                            <a href="<?php echo $this->get_controller_url('advertising'); ?>">
                                                <span><?php echo __('advertising'); ?></span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            <?php endif; ?>
                            <?php if (session::check_permission('QL_DANH_SACH_BANNER') > 0): ?>
                                <td class="LeftMenuOverview">
                                    <div class="Content">
                                        <div id="ctl00_ContentPlaceHolderContent_AdminLeftMenu_dtlMenu_ctl06_divRoleImage">
                                            <img alt="" onclick="javascript:window.location='<?php echo $this->get_controller_url('banner'); ?>'" id="imgSubRole" style="cursor:pointer;" width="99" height="84" src="<?php echo SITE_ROOT . "public/images/BannerLogo.gif" ?>">
                                        </div>
                                        <div class="Item">
                                            <a href="<?php echo $this->get_controller_url('banner'); ?>">
                                                <span><?php echo __('Banner'); ?></span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            <?php endif; ?>
                            <?php if (session::check_permission('QL_DANH_SACH_TIN_BAI') > 0): ?>
                                <td class="LeftMenuOverview">
                                    <div class="Content">
                                        <div id="ctl00_ContentPlaceHolderContent_AdminLeftMenu_dtlMenu_ctl01_divRoleImage">
                                            <img alt="" onclick="javascript:window.location='<?php echo $this->get_controller_url('article'); ?>'" id="imgSubRole" style="cursor:pointer;" width="99" height="84" src="<?php echo SITE_ROOT . "public/images/ArticleLogo.gif" ?>">
                                        </div>
                                        <div class="Item">
                                            <a href="<?php echo $this->get_controller_url('article'); ?>">
                                                <span><?php echo __('article') ?></span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            <?php endif; ?>
                        </tr>
                        <tr>
                            <?php if (session::check_permission('QL_DANH_SACH_VI_TRI_TIEU_DIEM') > 0): ?>
                                <td class="LeftMenuOverview">
                                    <div class="Content">
                                        <div id="ctl00_ContentPlaceHolderContent_AdminLeftMenu_dtlMenu_ctl01_divRoleImage">
                                            <img alt="" onclick="javascript:window.location='<?php echo $this->get_controller_url('spotlight'); ?>'" id="imgSubRole" style="cursor:pointer;" width="99" height="84" src="<?php echo SITE_ROOT . "public/images/Spotlight-Logo.gif" ?>">
                                        </div>
                                        <div class="Item">
                                            <a href="<?php echo $this->get_controller_url('spotlight'); ?>">
                                                <span><?php echo __('spotlight'); ?></span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            <?php endif; ?>
                            <?php if (session::check_permission('QL_DANH_SACH_NOI_BAT') > 0): ?>
                                <td class="LeftMenuOverview">
                                    <div class="Content">
                                        <div id="ctl00_ContentPlaceHolderContent_AdminLeftMenu_dtlMenu_ctl03_divRoleImage">
                                            <img alt="" onclick="javascript:window.location='<?php echo $this->get_controller_url('sticky'); ?>'" id="imgSubRole" style="cursor:pointer;" width="99" height="84" src="<?php echo SITE_ROOT . "public/images/Stickylogo.gif" ?>">
                                        </div>
                                        <div class="Item">
                                            <a href="<?php echo $this->get_controller_url('sticky'); ?>">
                                                <span><?php echo __('sticky'); ?></span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            <?php endif; ?>
                            <?php if (session::check_permission('QL_DANH_SACH_MENU') > 0): ?>
                                <td class="LeftMenuOverview">
                                    <div class="Content">
                                        <div id="ctl00_ContentPlaceHolderContent_AdminLeftMenu_dtlMenu_ctl07_divRoleImage">
                                            <img alt="" onclick="javascript:window.location='<?php echo $this->get_controller_url('menu'); ?>'" id="imgSubRole" style="cursor:pointer;" width="99" height="84" src="<?php echo SITE_ROOT . "public/images/MenuLogo.gif" ?>">
                                        </div>
                                        <div class="Item">
                                            <a href="<?php echo $this->get_controller_url('menu'); ?>">
                                                <span><?php echo __('menu'); ?></span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            <?php endif; ?>
                            <?php if (session::check_permission('QL_DANH_SACH_WIDGET') > 0): ?>                               
                                <td class="LeftMenuOverview">
                                    <div class="Content">
                                        <div id="ctl00_ContentPlaceHolderContent_AdminLeftMenu_dtlMenu_ctl09_divRoleImage">
                                            <img alt="" onclick="javascript:window.location='<?php echo $this->get_controller_url('widget/dsp_all_widget'); ?>'" id="imgSubRole" style="cursor:pointer;" width="99" height="84" src="<?php echo SITE_ROOT . "public/images/Widgets.png" ?>">
                                        </div>
                                        <div class="Item">
                                            <a href="<?php echo $this->get_controller_url('widget/dsp_all_widget'); ?>">
                                                <span><?php echo __('widget'); ?></span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            <?php endif; ?>
                            <?php if (session::check_permission('QL_DANH_SACH_WEBLINK') > 0): ?>
                                <td class="LeftMenuOverview">
                                    <div class="Content">
                                        <div id="ctl00_ContentPlaceHolderContent_AdminLeftMenu_dtlMenu_ctl09_divRoleImage">
                                            <img alt="" onclick="javascript:window.location='<?php echo $this->get_controller_url('weblink'); ?>'" id="imgSubRole" style="cursor:pointer;" width="99" height="84" src="<?php echo SITE_ROOT . "public/images/weblink.png" ?>">
                                        </div>
                                        <div class="Item">
                                            <a href="<?php echo $this->get_controller_url('weblink'); ?>">
                                                <span><?php echo __('weblink'); ?></span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            <?php endif; ?>
                        </tr>
                        <tr>
                            <?php if (session::check_permission('QL_DANH_SACH_MEDIA', FALSE) > 0): ?>
                                <td class="LeftMenuOverview">
                                    <div class="Content">
                                        <div id="ctl00_ContentPlaceHolderContent_AdminLeftMenu_dtlMenu_ctl09_divRoleImage">
                                            <img alt="" onclick="javascript:window.location='<?php echo $this->get_controller_url('advmedia'); ?>'" id="imgSubRole" style="cursor:pointer;" width="99" height="84" src="<?php echo SITE_ROOT . "public/images/MediaLogo.gif" ?>">
                                        </div>
                                        <div class="Item">
                                            <a href="<?php echo $this->get_controller_url('advmedia'); ?>">
                                                <span><?php echo __('media'); ?></span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            <?php endif; ?>
                        </tr>
                        </tbody></table>
                </div>
                <!--div tuong tac ban doc-->
                <div id="dashboard_interactive">
                    <table id="ctl00_ContentPlaceHolderContent_AdminLeftMenu_dtlMenu" class="TableLeftMenuOverview" cellspacing="0" align="Left" border="0" style="border-width:0px;width:180px;border-collapse:collapse;">
                        <tr>
                            <!--tham do y kien-->
                            <?php if (session::check_permission('QL_DANH_SACH_CUOC_THAM_DO_Y_KIEN') > 0): ?>
                                <td class="LeftMenuOverview">
                                    <div class="Content">
                                        <div id="ctl00_ContentPlaceHolderContent_AdminLeftMenu_dtlMenu_ctl09_divRoleImage">
                                            <img alt="" onclick="javascript:window.location='<?php echo $this->get_controller_url('poll'); ?>'" id="imgSubRole" style="cursor:pointer;" width="99" height="84" src="<?php echo SITE_ROOT . "public/images/PollLogo.gif" ?>">
                                        </div>
                                        <div class="Item">
                                            <a href="<?php echo $this->get_controller_url('poll'); ?>">
                                                <span><?php echo __('poll'); ?></span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            <?php endif; ?>
                            <!--tham do y kien-->
                            <?php if (session::check_permission('QL_CAU_HOI_DAP') > 0): ?>
                                <td class="LeftMenuOverview">
                                    <div class="Content">
                                        <div id="ctl00_ContentPlaceHolderContent_AdminLeftMenu_dtlMenu_ctl09_divRoleImage">
                                            <img alt="" onclick="javascript:window.location='<?php echo $this->get_controller_url('citizens_question'); ?>'" id="imgSubRole" style="cursor:pointer;" width="99" height="84" src="<?php echo SITE_ROOT . "public/images/citizens_question.jpg" ?>">
                                        </div>
                                        <div class="Item">
                                            <a href="<?php echo $this->get_controller_url('citizens_question'); ?>">
                                                <span><?php echo __('citizens question'); ?></span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            <?php endif; ?>
                                
                            <!--gop y phan hoi-->
                            <?php if (session::check_permission('QL_DANH_SACH_GOP_Y') > 0): ?>
                            <td class="LeftMenuOverview">
                                <div class="Content">
                                    <div id="ctl00_ContentPlaceHolderContent_AdminLeftMenu_dtlMenu_ctl09_divRoleImage">
                                        <img alt="" onclick="javascript:window.location='<?php echo $this->get_controller_url('feedback'); ?>'" id="imgSubRole" style="cursor:pointer;" width="99" height="84" src="<?php echo SITE_ROOT . "public/images/feedback.png" ?>">
                                    </div>
                                    <div class="Item">
                                        <a href="<?php echo $this->get_controller_url('feedback'); ?>">
                                            <span><?php echo __('feedback'); ?></span>
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <?php endif;?>
                            <?php if (session::check_permission('QL_DANH_DANH_HO_SO_NOP_TRUC_TUYEN',FALSE) > 0): ?>
                            <td class="LeftMenuOverview">
                                <div class="Content">
                                    <div id="ctl00_ContentPlaceHolderContent_AdminLeftMenu_dtlMenu_ctl09_divRoleImage">
                                        <img alt="" onclick="javascript:window.location='<?php echo $this->get_controller_url('internet_record'); ?>'" id="imgSubRole" style="cursor:pointer;" width="99" height="84" src="<?php echo SITE_ROOT . "public/images/internet_record.png" ?>">
                                    </div>
                                    <div class="Item">
                                        <a href="<?php echo $this->get_controller_url('internet_record'); ?>">
                                            <span><?php echo __('confirmed records'); ?></span>
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <?php endif;?>
                            <?php if (session::check_permission('QL_CAU_HOI_KHAO_SAT') > 0): ?>
                            <td class="LeftMenuOverview">
                                <div class="Content">
                                    <div id="ctl00_ContentPlaceHolderContent_AdminLeftMenu_dtlMenu_ctl09_divRoleImage">
                                        <img alt="" onclick="javascript:window.location='<?php echo $this->get_controller_url('survey'); ?>'" id="imgSubRole" style="cursor:pointer;" width="99" height="84" src="<?php echo SITE_ROOT . "public/images/survey-icon.png" ?>">
                                    </div>
                                    <div class="Item">
                                        <a href="<?php echo $this->get_controller_url('survey'); ?>">
                                            <span><?php echo __('survey'); ?></span>
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <?php endif;?>
                        </tr>
                        <tr>
                            <?php if (session::check_permission('QL_DANH_SACH_TAI_KHOAN_CONG_DAN',FALSE) > 0): ?>
                            <td class="LeftMenuOverview">
                                <div class="Content">
                                    <div id="ctl00_ContentPlaceHolderContent_AdminLeftMenu_dtlMenu_ctl09_divRoleImage">
                                        <img alt="" onclick="javascript:window.location='<?php echo $this->get_controller_url('citizen_account'); ?>'" id="imgSubRole" style="cursor:pointer;" width="99" height="84" src="<?php echo SITE_ROOT . "public/images/citizen_account.jpg" ?>">
                                    </div>
                                    <div class="Item">
                                        <a href="<?php echo $this->get_controller_url('citizen_account'); ?>">
                                            <span><?php echo __('citizen account'); ?></span>
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <?php endif;?>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <!--div quan tri he thog-->
                <div id="dashboard_system">
                    <table id="ctl00_ContentPlaceHolderContent_AdminLeftMenu_dtlMenu" class="TableLeftMenuOverview" cellspacing="0" align="Left" border="0" style="border-width:0px;width:180px;border-collapse:collapse;">
                        <tr>
                            <?php if (session::check_permission('XEM_DANH_SACH_USER',FALSE) > 0): ?>
                                <td class="LeftMenuOverview">
                                    <div class="Content">
                                        <div id="ctl00_ContentPlaceHolderContent_AdminLeftMenu_dtlMenu_ctl08_divRoleImage">
                                            <img alt="" onclick="javascript:window.location='<?php echo $this->get_controller_url('user'); ?>'" id="imgSubRole" style="cursor:pointer;" width="99" height="84" src="<?php echo SITE_ROOT . "public/images/UserLogo.gif" ?>">
                                        </div>
                                        <div class="Item">
                                            <a href="<?php echo $this->get_controller_url('user'); ?>">
                                                <span><?php echo __('user'); ?></span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            <?php endif; ?>
                            <?php if (session::check_permission('QL_DANH_SACH_CHUYEN_TRANG',FALSE) > 0): ?>
                                <td class="LeftMenuOverview">
                                    <div class="Content">
                                        <div id="ctl00_ContentPlaceHolderContent_AdminLeftMenu_dtlMenu_ctl09_divRoleImage">
                                            <img alt="" onclick="javascript:window.location='<?php echo $this->get_controller_url('website'); ?>'" id="imgSubRole" style="cursor:pointer;" width="99" height="84" src="<?php echo SITE_ROOT . "public/images/WebsiteLogo.gif" ?>">
                                        </div>
                                        <div class="Item">
                                            <a href="<?php echo $this->get_controller_url('website'); ?>">
                                                <span><?php echo __('website'); ?></span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            <?php endif; ?>
                            <?php if (session::check_permission('QL_DANH_SACH_LISTTYPE',FALSE) > 0): ?>
                                <td class="LeftMenuOverview">
                                    <div class="Content">
                                        <div id="ctl00_ContentPlaceHolderContent_AdminLeftMenu_dtlMenu_ctl09_divRoleImage">
                                            <img alt="" onclick="javascript:window.location='<?php echo $this->get_controller_url('xlist/dsp_all_listtype'); ?>'" id="imgSubRole" style="cursor:pointer;" width="99" height="84" src="<?php echo SITE_ROOT . "public/images/ListStyleLogo.jpg" ?>">
                                        </div>
                                        <div class="Item">
                                            <a href="<?php echo $this->get_controller_url('xlist/dsp_all_listtype'); ?>">
                                                <span><?php echo __('list type'); ?></span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            <?php endif; ?>
                            <?php if (session::check_permission('QL_DANH_SACH_LIST',FALSE) > 0): ?>
                                <td class="LeftMenuOverview">
                                    <div class="Content">
                                        <div id="ctl00_ContentPlaceHolderContent_AdminLeftMenu_dtlMenu_ctl09_divRoleImage">
                                            <img alt="" onclick="javascript:window.location='<?php echo $this->get_controller_url('xlist/dsp_all_list'); ?>'" id="imgSubRole" style="cursor:pointer;" width="99" height="84" src="<?php echo SITE_ROOT . "public/images/ListLogo.png" ?>">
                                        </div>
                                        <div class="Item">
                                            <a href="<?php echo $this->get_controller_url('xlist/dsp_all_list'); ?>">
                                                <span><?php echo __('list'); ?></span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            <?php endif; ?>
                          <?php if (session::check_permission(1) > 0): ?>
                                <td class="LeftMenuOverview">
                                    <div class="Content">
                                        <div id="ctl00_ContentPlaceHolderContent_AdminLeftMenu_dtlMenu_ctl09_divRoleImage">
                                            <img alt="" onclick="javascript:window.location='<?php echo $this->get_controller_url('system_config'); ?>'" id="imgSubRole" style="cursor:pointer;" width="99" height="84" src="<?php echo SITE_ROOT . "public/images/config.png" ?>">
                                        </div>
                                        <div class="Item">
                                            <a href="<?php echo $this->get_controller_url('system_config'); ?>">
                                                <span><?php echo __('system config'); ?></span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            <?php endif; ?>
                        </tr>
                        <tr>
                            <?php if (session::check_permission('QL_DON_VI_TRUC_THUOC') > 0): ?>
                            <td class="LeftMenuOverview">
                                <div class="Content">
                                    <div id="">
                                        <img alt="" onclick="javascript:window.location='<?php echo $this->get_controller_url('member'); ?>'" id="imgSubRole" style="cursor:pointer;" width="99" height="84" src="<?php echo SITE_ROOT . "public/images/unit.png" ?>">
                                    </div>
                                    <div class="Item">
                                        <a href="<?php echo $this->get_controller_url('member'); ?>">
                                            <span><?php echo __('members'); ?></span>
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <?php endif;?>
                            <?php if (session::check_permission('QL_DANH_SACH_THU_TUC_HANH_CHINH') > 0): ?>
                            <td class="LeftMenuOverview">
                                <div class="Content">
                                    <div id="">
                                        <img alt="" onclick="javascript:window.location='<?php echo $this->get_controller_url('record_type'); ?>'" id="imgSubRole" style="cursor:pointer;" width="99" height="84" src="<?php echo SITE_ROOT . "public/images/Inventory.png" ?>">
                                    </div>
                                    <div class="Item">
                                        <a href="<?php echo $this->get_controller_url('record_type'); ?>">
                                            <span><?php echo __('record type'); ?></span>
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <?  endif; ?>
                        </tr>
                        </tbody>
                    </table>
                </div>
                
                <!--div Quan ly bao cao-->
                <div id="dashboard_report">
                    <table id="ctl00_ContentPlaceHolderContent_AdminLeftMenu_dtlMenu" class="TableLeftMenuOverview" cellspacing="0" align="Left" border="0" style="border-width:0px;width:180px;border-collapse:collapse;">
                        <tr>
                            <?php if (session::check_permission('QL_BAO_TONG_HOP_DANH_GIA_CAN_BO') > 0): ?>
                            <td class="LeftMenuOverview">
                                <div class="Content">
                                    <div id="">
                                        <img alt="" onclick="javascript:window.location='<?php echo $this->get_controller_url('report','admin','all_evaluation'); ?>'" id="imgSubRole" style="cursor:pointer;" width="99" height="84" src="<?php echo SITE_ROOT . "public/images/Inventory.png" ?>">
                                    </div>
                                    <div class="Item">
                                        <a href="<?php echo $this->get_controller_url('report','admin','all_evaluation'); ?>">
                                            <span><?php echo __('report all evaluation'); ?></span>
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <?  endif; ?>
                            <?php if (session::check_permission('QL_BAO_CHI_TIET_DANH_GIA_CAN_BO') > 0): ?>
                            <td class="LeftMenuOverview">
                                <div class="Content">
                                    <div id="">
                                        <img alt="" onclick="javascript:window.location='<?php echo $this->get_controller_url('report','admin','single_evaluation'); ?>'" id="imgSubRole" style="cursor:pointer;" width="99" height="84" src="<?php echo SITE_ROOT . "public/images/Inventory.png" ?>">
                                    </div>
                                    <div class="Item">
                                        <a href="<?php echo $this->get_controller_url('report','admin','single_evaluation'); ?>">
                                            <span><?php echo __('report single evaluation'); ?></span>
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <?  endif; ?>
                            <?php if (session::check_permission('QL_BAO_CAO_TONG_HOP_GIAI_QUYET_THU_TUC_HANH_CHINH') > 0): ?>
                            <td class="LeftMenuOverview">
                                <div class="Content">
                                    <div id="">
                                        <img alt="" onclick="javascript:window.location='<?php echo $this->get_controller_url('report','admin','report_all_recordtype'); ?>'" id="imgSubRole" style="cursor:pointer;" width="99" height="84" src="<?php echo SITE_ROOT . "public/images/Inventory.png" ?>">
                                    </div>
                                    <div class="Item">
                                        <a href="<?php echo $this->get_controller_url('report','admin','report_all_recordtype'); ?>">
                                            <span><?php echo __('report all recordtype'); ?></span>
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <?  endif; ?>
                            <?php if (session::check_permission('QL_BAO_CAO_CHI_TIET_GIAI_QUYET_THU_TUC_HANH_CHINH') > 0): ?>
                            <td class="LeftMenuOverview">
                                <div class="Content">
                                    <div id="">
                                        <img alt="" onclick="javascript:window.location='<?php echo $this->get_controller_url('report','admin','report_single_recordtype'); ?>'" id="imgSubRole" style="cursor:pointer;" width="99" height="84" src="<?php echo SITE_ROOT . "public/images/Inventory.png" ?>">
                                    </div>
                                    <div class="Item">
                                        <a href="<?php echo $this->get_controller_url('report','admin','report_single_recordtype'); ?>">
                                            <span><?php echo __('report single recordtype'); ?></span>
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <?  endif; ?>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>
</div>
<script>
    $(document).ready(function(){
        if($('#hdn_menu_select').attr('value')=='div_menu_content')
        {
            $('#dashboard_interactive').remove();
            $('#dashboard_system').remove();
            $('#dashboard_report').remove();
        }
        else if($('#hdn_menu_select').attr('value')=='div_menu_system')
        {
            $('#dashboard_content').remove();
            $('#dashboard_interactive').remove();
            $('#dashboard_report').remove();
        }
        else if($('#hdn_menu_select').attr('value')=='div_menu_interactive')
        {
            $('#dashboard_content').remove();
            $('#dashboard_system').remove();
            $('#dashboard_report').remove();
        }
        else if($('#hdn_menu_select').attr('value')=='div_menu_report')
        {
            $('#dashboard_content').remove();
            $('#dashboard_interactive').remove();
            $('#dashboard_system').remove();
        }
    });    
</script>
<?php $this->template->display('dsp_footer.php'); ?>