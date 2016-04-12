<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed'); ?>
<?php
//header
$this->template->title = __('internet record detail');
$this->template->display('dsp_header.php');

$v_record_id        = isset($arr_single_record['PK_RECORD'])?$arr_single_record['PK_RECORD']:0;
$v_record_type_code = isset($arr_single_record['C_RECORD_TYPE_CODE'])?$arr_single_record['C_RECORD_TYPE_CODE']:'';
$v_record_type_name = isset($arr_single_record['C_RECORD_TYPE_NAME'])?$arr_single_record['C_RECORD_TYPE_NAME']:'';
$v_spec_name        = isset($arr_single_record['C_SPEC_NAME'])?$arr_single_record['C_SPEC_NAME']:'';
$v_member_name      = isset($arr_single_record['C_MEMBER_NAME'])?$arr_single_record['C_MEMBER_NAME']:'';
$v_record_no        = isset($arr_single_record['C_RECORD_NO'])?$arr_single_record['C_RECORD_NO']:'';
$v_citizen_name     = isset($arr_single_record['C_CITIZEN_NAME'])?$arr_single_record['C_CITIZEN_NAME']:'';
$v_phone            = isset($arr_single_record['C_RETURN_PHONE_NUMBER'])?$arr_single_record['C_RETURN_PHONE_NUMBER']:'';
$v_email            = isset($arr_single_record['C_RETURN_EMAIL'])?$arr_single_record['C_RETURN_EMAIL']:'';
$v_note             = isset($arr_single_record['C_NOTE'])?$arr_single_record['C_NOTE']:'';
$v_addresss         = isset($arr_single_record['C_CITIZEN_ADDRESS'])?$arr_single_record['C_CITIZEN_ADDRESS']:'';
$v_xml              = isset($arr_single_record['C_XML_FILE'])?$arr_single_record['C_XML_FILE']:'';
?>
<h2 class="module_title"><?php echo __('internet record detail');?></h2>
<form name="frmMain" id="frmMain" action="#" method="POST" >
    <?php
        echo $this->hidden('controller',$this->get_controller_url());
        echo $this->hidden('hdn_item_id',$v_record_id);
        echo $this->hidden('hdn_item_id_list','');

        echo $this->hidden('hdn_dsp_single_method','dsp_single_record');
        echo $this->hidden('hdn_dsp_all_method', 'dsp_all_record');
        echo $this->hidden('hdn_update_method', 'do_confirm_record');
        echo $this->hidden('XmlData', '');
    ?>
    <div class="Row">
        <div class="left-Col">Tên TTHC</div>
        <div class="right-Col"><?php echo $v_record_type_code . ' - ' .$v_record_type_name?></div>
    </div>
    <div class="Row">
        <div class="left-Col">Lĩnh vực</div>
        <div class="right-Col"><?php echo $v_spec_name?></div>
    </div>
    <div class="Row">
        <div class="left-Col">Đơn vị tiếp nhận</div>
        <div class="right-Col"><?php echo $v_member_name?></div>
    </div>
    <div class="Row">
        <div class="left-Col">Mã hồ sơ </div>
        <div class="right-Col"><?php echo $v_record_no?></div>
    </div>
    <div class="Row">
        <div class="left-Col">Họ tên</div>
        <div class="right-Col"><?php echo $v_citizen_name?></div>
    </div>
    <div class="Row">
        <div class="left-Col"><?php echo __('phone')?></div>
        <div class="right-Col"><?php echo $v_phone?></div>
    </div>
    <div class="Row">
        <div class="left-Col">Email</div>
        <div class="right-Col"><?php echo $v_email?></div>
    </div>
    <div class="Row">
        <div class="left-Col"><?php echo __('address')?></div>
        <div class="right-Col"><?php echo $v_addresss?></div>
    </div>
    <div class="Row">
        <div class="left-Col">Ghi chú</div>
        <div class="right-Col"><?php echo $v_note?></div>
    </div>
    <div class="Row">
        <div class="left-Col">File đính kèm: </div>
        <div class="right-Col">
            <?php
                @$dom =  simplexml_load_string($v_xml,'SimpleXMLElement', LIBXML_NOCDATA);
                if($dom)
                {
                    $v_xpath   = '//item';
                    $obj_file  = $dom->xpath($v_xpath);
                    for($i=0;$i <sizeof($obj_file);$i++) 
                    {
                        $v_name         = (string)$obj_file[$i];
                        $v_field_name   = (string)$obj_file[0]->attributes()->c_file_name;
                        $v_url = _CONST_SITE_RECORD_FILE . urlencode($v_field_name);
                        ?>
                            <a href="<?php echo $v_url?>" target="_blank">
                                <img  src="<?php echo SITE_ROOT; ?>public/images/document.png" alt="Thumbnail" style="width: 15px;height: 17px;">
                                <?php echo $v_name?>
                            </a>
                            <div class="clearfix" style="height: 3px"></div>
                    <?php
                    }
                }
            ?>
        </div>
    </div>
    <div class="button-area">
        <?php
            if(trim($v_record_no) == '')
            {
                echo '<button type="button" name="addnew" class="ButtonAccept" onclick="btn_update_onclick();"><i class="icon-plus"></i>'. __('confirm record').'</button>';
            }
        ?>        
        <button type="button" name="trash" class="ButtonBack" onclick="btn_back_onclick();"><i class="icon-trash"></i><?php echo __('back');?></button>
    </div>
</form>
<?php $this->template->display('dsp_footer.php');