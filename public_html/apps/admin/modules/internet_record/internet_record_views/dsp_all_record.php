<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed'); ?>
<?php

//header
$this->template->title = __('internet record');
$this->template->display('dsp_header.php');

$v_record_type_id = get_post_var('hdn_record_type_id','');
$v_member_id = get_post_var('sel_member','');
//don vi thanh vien
$arr_all_village  =  $arr_all_member['arr_all_village'];
$arr_all_district =  $arr_all_member['arr_all_district'];

?>
<form name="frmMain" id="frmMain" action="#" method="POST" class="form-horizontal">
    <?php
    echo $this->hidden('controller',$this->get_controller_url());
    echo $this->hidden('hdn_item_id','0');
    echo $this->hidden('hdn_item_id_list','');

    echo $this->hidden('hdn_dsp_single_method','dsp_single_record');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_record');
    echo $this->hidden('hdn_delete_method', 'do_delete_record');
    echo $this->hidden('hdn_update_processing_record_method', 'do_update_processing_record');
    
    echo $this->hidden('hdn_record_type_id', $v_record_type_id);
    ?>
<h2 class="module_title"><?php echo __('internet record');?></h2>
<div class="div-notice full-width">
    <span class="title-general-infor"><?php echo __('record Statistics')?> </span>
    <ul>
        <?php foreach($arr_all_notice as $arr_notice):
                $record_type_id   = $arr_notice['FK_RECORD_TYPE'];
                $count            = $arr_notice['C_COUNT'];
                $record_type_name = $arr_notice['C_RECORD_TYPE_NAME'];
        ?>
        <li>
            <a href="javascript:void(0)" onclick="record_type_filter('<?php echo $record_type_id?>')">
                - <?php echo $record_type_name?> có <?php echo $count?> hồ sơ 
            </a>
        </li>
        <?php endforeach;?>
    </ul>
</div>
<div class="filter full-width">
    <!--filter ma loai hs--> 
    <label >Mã loại hồ sơ </label>
    <input type="textbox" id="txt_record_type_code" name="txt_record_type_code" 
           size="10" 
           style="text-transform: uppercase;"
           onkeypress="txt_record_type_code_onkeypress(event);" />
    <select style="width: 50%;" id="sel_record_type" name="sel_record_type" onchange="sel_record_type_onchange(this)">
        <option value=''>-- Chọn loại hồ sơ --</option>
        <?php foreach($arr_all_notice as $arr_notice):
                $record_type_id   = $arr_notice['FK_RECORD_TYPE'];
                $count            = $arr_notice['C_COUNT'];
                $record_type_name = $arr_notice['C_RECORD_TYPE_NAME'];
                $record_type_code = $arr_notice['C_RECORD_TYPE_CODE'];
                $selected         = ($v_record_type_id == $record_type_id)?'selected':'';
        ?>
        <option <?php echo $selected;?> data-code="<?php echo $record_type_code?>" value="<?php echo $record_type_id?>"><?php echo $record_type_name?></option>
        <?php endforeach;?>
    </select>
    <!--filter don vi tiep nhan-->
    <label ><?php echo __('units')?> </label>
    <select style="width: 20%;" name="sel_member" onchange="btn_filter_onclick()">
        <option value=''>-- Đơn vị tiếp nhận --</option>
        <?php foreach($arr_all_district as $arr_district):
                $v_name = $arr_district['C_NAME'];
                $v_id   = $arr_district['PK_MEMBER'];
                $v_selected = ($v_id == $v_member_id)?'selected':'';
        ?>
        <option <?php echo $v_selected?> value="<?php echo $v_id?>"><?php echo $v_name;?></option>
        <?php foreach($arr_all_village as $key => $arr_village):
                $v_village_name = $arr_village['C_NAME'];
                $v_village_id   = $arr_village['PK_MEMBER'];
                $v_parent_id    = $arr_village['FK_MEMBER'];
                if($v_parent_id != $v_id)
                {
                    continue;
                }
                $v_selected = ($v_village_id == $v_member_id)?'selected':'';
        ?>
        <option <?php echo $v_selected?> value="<?php echo $v_village_id?>">----- <?php echo $v_village_name;?></option>
        <?php 
            unset($arr_all_village[$key]);
            endforeach;
        ?>
        <?php endforeach;?>
    </select>
</div>  
    <!--danh sach-->
    <?php
    $xml_file = strtolower('xml_internet_record_list.xml');
    if ($this->load_xml($xml_file)) {
        echo $this->render_form_display_all($arr_all_record);
    }
    ?>
    <!--pagging-->
    <div id="dyntable_length" class="dataTables_length">
        <?php echo $this->paging2($arr_all_record);?>
    </div>
    <!--button--> 
    <div class="button-area">
        <button type="button" name="upate_processing" class="ButtonAccept" onclick="btn_update_processing_record_onclick();"><?php echo __('Cập nhật trạng thái xử lý');?></button>
        <button type="button" name="trash" class="ButtonDelete" onclick="btn_delete_onclick();"><?php echo __('delete');?></button>
    </div>
</form>
<script>
    $(document).ready(function(){
       var record_type_code = $('#sel_record_type option:selected').attr('data-code');
       $('#txt_record_type_code').val(record_type_code);
    });
    function record_type_filter(record_type_id)
    {
        $('#hdn_record_type_id').val(record_type_id);
        btn_filter_onclick();
    }
    
    function sel_record_type_onchange(v_sel_record_type)
    {
        record_type_filter($(v_sel_record_type).val());
    }
    
    function txt_record_type_code_onkeypress(evt)
    {
        if (IE()){
            theKey=window.event.keyCode
        } else {
            theKey=evt.which;
        }

        if(theKey == 13){
            var v_record_type_code = trim($("#txt_record_type_code").val()).toUpperCase();
            if(v_record_type_code != '')
            {
                $("#sel_record_type option").each(function(){
                    if($(this).attr('data-code') == v_record_type_code)
                    {
                        record_type_filter($(this).val());
                    }
                });
            }
        }
        return false;
    }
  
    function btn_update_processing_record_onclick()
    {
        var f = document.frmMain;
        m = $("#controller").val() + f.hdn_update_processing_record_method.value;
        $("#frmMain").attr("action", m);
        f.submit();
    }
</script>
<?php $this->template->display('dsp_footer.php');