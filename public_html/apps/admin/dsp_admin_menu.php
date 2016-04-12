<?php
$arr_url = explode('/', $_GET['url']);
$v_menu_active = $arr_url[1];
if ($v_menu_active == 'xlist')
{
    $v_menu_active = $arr_url[2];
    $arr_menu_active = explode('_', $v_menu_active);
    $v_last = count($arr_menu_active) - 1;
    $v_menu_active = $arr_menu_active[$v_last];
}
if ($v_menu_active == 'report')
{
    $v_menu_active = $arr_url[2];
    $arr_menu_active = explode('_', $v_menu_active);
}

?>
<!--quan tri noi dung-->
<div id="menu_content">
    <table id="ctl00_ctl00_ContentPlaceHolderContent_AdminLeftMenu_dtlMenu" class="TableLeftMenu" cellspacing="0" align="Left" border="0" style="border-width:0px;width: 100%;border-collapse:collapse;">
        <?php if (session::check_permission('XEM_DANH_SACH_CHUYEN_MUC') > 0): ?>
            <tr>
                <td class="LeftMenu" data-name="category">
                    <div class="Content_menu">
                        <div class="Item">
                            <a href="<?php echo get_admin_controller_url('category'); ?>">
                                <span><?php echo __('category') ?></span>
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endif; ?>
        <?php if (session::check_permission('XEM_DANH_SACH_TIN_BAI') > 0): ?>
            <tr>
                <td class="LeftMenu" data-name="article">
                    <div class="Content_menu">

                        <div class="Item">
                            <a href="<?php echo get_admin_controller_url('article'); ?>">
                                <span><?php echo __('article') ?></span>
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endif; ?>
        <?php if (session::check_permission('XEM_DANH_SACH_NOI_BAT') > 0): ?>
            <tr>
                <td class="LeftMenu" data-name="sticky">
                    <div class="Content_menu">

                        <div class="Item">
                            <a href="<?php echo get_admin_controller_url('sticky'); ?>">
                                <span><?php echo __('sticky') ?></span>
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endif; ?>
        <?php if (session::check_permission('XEM_DANH_SACH_VI_TRI_TIEU_DIEM') > 0): ?>
            <tr>
                <td class="LeftMenu" data-name="spotlight">
                    <div class="Content_menu">

                        <div class="Item">
                            <a href="<?php echo get_admin_controller_url('spotlight'); ?>">
                                <span><?php echo __('spotlight') ?></span>
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endif; ?>
        <?php if (session::check_permission('XEM_DANH_SACH_MENU') > 0): ?>
            <tr>
                <td class="LeftMenu" data-name="menu">
                    <div class="Content_menu">

                        <div class="Item">
                            <a href="<?php echo get_admin_controller_url('menu'); ?>">
                                <span><?php echo __('menu') ?></span>
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endif; ?>
        <?php if (session::check_permission('XEM_DANH_SACH_SU_KIEN') > 0): ?>
            <tr>
                <td class="LeftMenu" data-name="event">
                    <div class="Content_menu">

                        <div class="Item">
                            <a href="<?php echo get_admin_controller_url('event'); ?>">
                                <span><?php echo __('event') ?></span>
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endif; ?>
        <?php if (session::check_permission('XEM_DANH_SACH_QUANG_CAO') > 0): ?>
            <tr>
                <td class="LeftMenu" data-name="advertising">
                    <div class="Content_menu">

                        <div class="Item">
                            <a href="<?php echo get_admin_controller_url('advertising'); ?>">
                                <span><?php echo __('advertising') ?></span>
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endif; ?>
        <?php if (session::check_permission('XEM_DANH_SACH_BANNER') > 0): ?>
            <tr>
                <td class="LeftMenu" data-name="banner">
                    <div class="Content_menu">

                        <div class="Item">
                            <a href="<?php echo get_admin_controller_url('banner'); ?>">
                                <span><?php echo __('banner') ?></span>
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endif; ?>
        <?php if (session::check_permission('XEM_DANH_SACH_WIDGET') > 0): ?>
            <tr>
                <td class="LeftMenu" data-name="widget">
                    <div class="Content_menu">
                        <div class="Item">
                            <a href="<?php echo get_admin_controller_url('widget'); ?>">
                                <span><?php echo __('widget') ?></span>
                            </a>
                        </div>
                    </div>
                </td>
            </tr> 
        <?php endif; ?>
        <?php if (session::check_permission('XEM_DANH_SACH_MEDIA', 0) > 0): ?>
            <tr>
                <td class="LeftMenu" data-name="advmedia">
                    <div class="Content_menu">
                        <div class="Item">
                            <a href="<?php echo get_admin_controller_url('advmedia'); ?>">
                                <span><?php echo __('Media') ?></span>
                            </a>
                        </div>
                    </div>
                </td>
            </tr> 
        <?php endif; ?>
        <?php if (session::check_permission('XEM_DANH_SACH_WEBLINK', 0) > 0): ?>
            <tr>
                <td class="LeftMenu" data-name="weblink">
                    <div class="Content_menu">
                        <div class="Item">
                            <a href="<?php echo get_admin_controller_url('weblink'); ?>">
                                <span><?php echo __('weblink') ?></span>
                            </a>
                        </div>
                    </div>
                </td>
            </tr> 
        <?php endif; ?>
    </table>
</div>
<!--quan tri he thong-->
<div id="menu_system">
    <table name="tbl_menu_system" id="tbl_menu_system" class="TableLeftMenu" cellspacing="0" align="Left" border="0" style="border-width:0px;width: 100%;border-collapse:collapse;">
        <?php if (session::check_permission('XEM_DANH_SACH_USER',FALSE) > 0): ?>
            <tr>
                <td class="LeftMenu" data-name="user">
                    <div class="Content_menu">
                        <div class="Item">
                            <a href="<?php echo get_admin_controller_url('user'); ?>">
                                <span><?php echo __('user') ?></span>
                            </a>
                        </div>
                    </div>
                </td>
            </tr>  
        <?php endif; ?>
        <?php if (session::check_permission('XEM_DANH_SACH_CHUYEN_TRANG', 0) > 0): ?>
            <tr>
                <td class="LeftMenu" data-name="website">
                    <div class="Content_menu">
                        <div class="Item">
                            <a href="<?php echo get_admin_controller_url('website'); ?>">
                                <span><?php echo __('website') ?></span>
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endif; ?>
        <?php if (session::check_permission('XEM_DANH_SACH_LISTTYPE', 0) > 0): ?>
            <tr>
                <td class="LeftMenu" data-name="listtype">
                    <div class="Content_menu">
                        <div class="Item">
                            <a href="<?php echo get_admin_controller_url('xlist/dsp_all_listtype'); ?>">
                                <span><?php echo __('list type') ?></span>
                            </a>
                        </div>
                    </div>
                </td>
            </tr> 
        <?php endif; ?>
        <?php if (session::check_permission('XEM_DANH_SACH_LIST', 0) > 0): ?>
            <tr>
                <td class="LeftMenu" data-name="list">
                    <div class="Content_menu">
                        <div class="Item">
                            <a href="<?php echo get_admin_controller_url('xlist/dsp_all_list'); ?>">
                                <span><?php echo __('list') ?></span>
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endif; ?>
        <?php if (1): ?>
            <tr>
                <td class="LeftMenu" data-name="system_config">
                    <div class="Content_menu">
                        <div class="Item">
                            <a href="<?php echo get_admin_controller_url('system_config'); ?>">
                                <span><?php echo __('system config') ?></span>
                            </a>
                        </div>
                    </div>
                </td>
            </tr> 
        <?php endif; ?>
            <?php if(session::check_permission('QL_DON_VI_TRUC_THUOC',FALSE)):; ?>
            <tr>
                <td class="LeftMenu" data-name="member">
                    <div class="Content_menu">
                        <div class="Item">
                            <a href="<?php echo get_admin_controller_url('member'); ?>">
                                <span><?php echo __('members') ?></span>
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
            <?php endif; ?>
            <?php if(session::check_permission('QL_DANH_SACH_THU_TUC_HANH_CHINH',FALSE)):; ?>
            <tr>
                <td class="LeftMenu" data-name="record_type">
                    <div class="Content_menu">
                        <div class="Item">
                            <a href="<?php echo get_admin_controller_url('record_type'); ?>">
                                <span><?php echo __('record type') ?></span>
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
            <?php endif; ?>
    </table>
</div>
<!--tuong tac ban doc-->
<div id="menu_interactive">
    <table name="tbl_menu_system" id="tbl_menu_interactive" class="TableLeftMenu" cellspacing="0" align="Left" border="0" style="border-width:0px;width: 100%;border-collapse:collapse;">
        <?php if (session::check_permission('XEM_DANH_SACH_CUOC_THAM_DO_Y_KIEN',FALSE) > 0): ?>
            <tr>
                <td class="LeftMenu" data-name="poll">
                    <div class="Content_menu">
                        <div class="Item">
                            <a href="<?php echo get_admin_controller_url('poll'); ?>">
                                <span><?php echo __('poll') ?></span>
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endif; ?>
        <!--gop y phan hoi-->
        <?php if (session::check_permission('XEM_DANH_SACH_GOP_Y',FALSE) > 0): ?>
            <tr>
                <td class="LeftMenu" data-name="feedback">
                    <div class="Content_menu">
                        <div class="Item">
                            <a href="<?php echo get_admin_controller_url('feedback'); ?>">
                                <span><?php echo __('feedback') ?></span>
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endif;?>
        <!--end gop y phan hoi-->
        <!--hoi dap-->
         <?php if (session::check_permission('QL_CAU_HOI_DAP') > 0): ?>
            <tr>
                <td class="LeftMenu" data-name="citizens_question">
                    <div class="Content_menu">
                        <div class="Item">
                            <a href="<?php echo get_admin_controller_url('citizens_question'); ?>">
                                  <span><?php echo __('citizens question'); ?></span>
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endif;?>
        <!--end hoi dap-->
        <?php if(session::check_permission('QL_DANH_DANH_HO_SO_NOP_TRUC_TUYEN',FALSE)): ?>
        <tr>
            <td class="LeftMenu" data-name="internet_record">
                <div class="Content_menu">
                    <div class="Item">
                        <a href="<?php echo get_admin_controller_url('internet_record'); ?>">
                            <span><?php echo __('confirmed records') ?></span>
                        </a>
                    </div>
                </div>
            </td>
        </tr>
        <?php endif;?>
        <?php  if(session::check_permission('QL_CAU_HOI_KHAO_SAT',FALSE)==TRUE):?>
        <tr>
            <td class="LeftMenu" data-name="survey">
                <div class="Content_menu">
                    <div class="Item">
                        <a href="<?php echo get_admin_controller_url('survey'); ?>">
                            <span><?php echo __('survey') ?></span>
                        </a>
                    </div>
                </div>
            </td>
        </tr>
        <?php endif;?>
        <?php if (session::check_permission('QL_DANH_SACH_TAI_KHOAN_CONG_DAN',FALSE)): ?>
        <tr>
            <td class="LeftMenu" data-name="citizen_account">
                <div class="Content_menu">
                    <div class="Item">
                        <a href="<?php echo get_admin_controller_url('citizen_account'); ?>">
                            <span><?php echo __('citizen account') ?></span>
                        </a>
                    </div>
                </div>
            </td>
        </tr>
        <?php endif; ?>
    </table>
</div>


<!--Quan ly bao cao-->
<div id="menu_report">
    <table name="tbl_menu_report" id="tbl_menu_report" class="TableLeftMenu" cellspacing="0" align="Left" border="0" style="border-width:0px;width: 100%;border-collapse:collapse;">
      
        <?php if (session::check_permission('QL_BAO_TONG_HOP_DANH_GIA_CAN_BO',FALSE)): ?>
        <tr>
            <td class="LeftMenu" data-name="all_evaluation">
                <div class="Content_menu">
                    <div class="Item">
                        <a href="<?php echo get_admin_controller_url('report','all_evaluation'); ?>">
                            <span><?php echo __('report all evaluation') ?></span>
                        </a>
                    </div>
                </div>
            </td>
        </tr>
        <?php endif; ?>
        <?php if (session::check_permission('QL_BAO_CHI_TIET_DANH_GIA_CAN_BO',FALSE)): ?>
        <tr>
            <td class="LeftMenu" data-name="single_evaluation">
                <div class="Content_menu">
                    <div class="Item">
                        <a href="<?php echo get_admin_controller_url('report','single_evaluation'); ?>">
                            <span><?php echo __('report single evaluation') ?></span>
                        </a>
                    </div>
                </div>
            </td>
        </tr>
        <?php endif; ?>
        <?php if (session::check_permission('QL_BAO_CAO_TONG_HOP_GIAI_QUYET_THU_TUC_HANH_CHINH',FALSE)): ?>
        <tr>
            <td class="LeftMenu" data-name="report_all_recordtype">
                <div class="Content_menu">
                    <div class="Item">
                        <a href="<?php echo get_admin_controller_url('report','report_all_recordtype'); ?>">
                            <span><?php echo __('report all recordtype') ?></span>
                        </a>
                    </div>
                </div>
            </td>
        </tr>
          <?php endif; ?>
         <?php if (session::check_permission('QL_BAO_CAO_CHI_TIET_GIAI_QUYET_THU_TUC_HANH_CHINH',FALSE)): ?>
        <tr>
            <td class="LeftMenu" data-name="report_single_recordtype">
                <div class="Content_menu">
                    <div class="Item">
                        <a href="<?php echo get_admin_controller_url('report','report_single_recordtype'); ?>">
                            <span><?php echo __('report single recordtype') ?></span>
                        </a>
                    </div>
                </div>
            </td>
        </tr>
        <?php endif; ?>
    </table>
</div>
<script>
    $(document).ready(function() {
        //alert($('#hdn_menu_select').attr('value'));
        $('#menu_content').css({'display':'none'});
        $('#menu_system').css({'display':'none'});
        $('#menu_interactive').css({'display':'none'});
        $('#menu_office').css({'display':'none'});
        $('#menu_report').css({'display':'none'});
        
        if($('#hdn_menu_select').attr('value')=='div_menu_content')
        {
            $('#menu_content').css({'display':'block'});
        }
        else if($('#hdn_menu_select').attr('value')=='div_menu_system')
        {
            $('#menu_system').css({'display':'block'});
        }
        else if($('#hdn_menu_select').attr('value')=='div_menu_interactive')
        {
            $('#menu_interactive').css({'display':'block'});
        }
        else if($('#hdn_menu_select').attr('value')=='div_office_manager')
        {
            $('#menu_office').css({'display':'block'});
        }
        else if($('#hdn_menu_select').attr('value')=='citizens_question')
        {
            $('#menu_office').css({'display':'block'});
        }
        else if($('#hdn_menu_select').attr('value')=='div_menu_report')
        {
            $('#menu_report').css({'display':'block'});
        }
            
        $('[data-name="<?php echo $v_menu_active; ?>"]').attr('class','LeftMenuActive');
    });
</script>