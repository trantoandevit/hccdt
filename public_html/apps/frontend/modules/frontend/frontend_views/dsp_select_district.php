<?php
defined('DS') or die('no direct access');
$this->template->title = 'Danh sách Quận / Huyện';
$this->template->active = $active;
$this->template->controller_url = $controller_url = $this->get_controller_url(). 'touch_screen';
$this->template->display('dsp_header_touchscreen.php');
?>
<?php if(!empty($arr_all_member)):?>
<ul class="menu_member">
    <li class="title">Chọn đơn vị</li>
    <?php 
    $i = 0;
    foreach($arr_all_member as $arr_member):
        $v_member_id   = $arr_member['PK_MEMBER'];
        $v_member_name = $arr_member['C_NAME'];
    ?>
    <li class="<?php echo (($i+1) == count($arr_all_member))?'last':'';?>">
        <a href="<?php echo $controller_url. "/$active/&member_id=$v_member_id"?>"><?php echo $v_member_name?></a>
    </li>
    <?php $i++;endforeach;?>
</ul>
<?php else:
        $v_unit_name = $arr_all_staff[0]['C_UNIT_NAME'];
?>
<ul class="menu_staff">
    <li class="title"><?php echo $v_unit_name;?></li>
    <?php foreach($arr_all_staff  as $arr_staff):
        $v_staff_code = $arr_staff['C_CODE'];
        $v_staff_name = $arr_staff['C_NAME'];
        $v_xml_data   = $arr_staff['C_XML_DATA'];
        $v_staff_id   = $arr_staff['PK_LIST'];
        
        $v_dir_img_staff_logo = CONST_DIRECT_VOTE_IMAGES . $v_staff_code . '.jpg';
        //Anh logo mac dinh
        $v_url_img_staff_logo = SITE_ROOT.'public/images/logo_default.jpg';
        if (file_exists($v_dir_img_staff_logo)) 
        {
            $v_url_img_staff_logo = CONST_URL_VOTE_IMAGES . $v_staff_code . '.jpg';
        }
        
        @$dom = simplexml_load_string($v_xml_data);
        if($dom)
        {
            $v_birthday   = $dom->xpath('//item[@id="txt_birthday"]/value');
            $v_birthday   = (string)$v_birthday[0];

            $v_education  = $dom->xpath('//item[@id="txt_education"]/value');
            $v_education  = (string)$v_education[0];

            $v_jo_title   = $dom->xpath('//item[@id="txt_job_title"]/value');
            $v_jo_title   = (string)$v_jo_title[0];
        }
    ?>
    <li>
        <a href="<?php echo $this->get_controller_url()."danh_gia/$v_staff_id"?>">
            <div class="row">
                <div class="col-sm-12">
                    <div class="col-sm-2 img-thumbnail">
                        <img src="<?php echo $v_url_img_staff_logo; ?>" width="100%" heght="100%"> 
                    </div>
                    <div class="col-sm-10 staff_info" >
                        <div class="col-md-12 name"><?php echo $v_staff_name?></div>
                        <div class="col-md-12 general">
                            <b>Ngày sinh: </b><?php echo $v_birthday?>,
                            <b>Trình độ học vấn: </b><?php echo $v_education?>, 
                            <b>Chức vụ: </b><?php echo $v_jo_title?>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </li>
    <?php endforeach;?>
</ul>
<?php endif;?>
<?php $this->template->display('dsp_footer_touchscreen.php'); ?>