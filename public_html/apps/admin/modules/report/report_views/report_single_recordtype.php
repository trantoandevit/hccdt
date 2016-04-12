<?php
defined('DS') or die('no direct access');
$this->template->title = __('Báo cáo chi tiết tình hình xử lý hồ sơ');
$this->template->display('dsp_header.php');
$controller = $this->get_controller_url();

$arr_all_village  =  $arr_all_member['arr_all_village'];
$arr_all_district =  $arr_all_member['arr_all_district'];

$arr_all_record_list_type = isset($arr_all_record_list_type) ? $arr_all_record_list_type : array();
$arr_all_record_type      = isset($arr_all_record_type) ? $arr_all_record_type : array();

$v_year     = get_post_var('sel_year',date('Y'));
$v_month    = get_post_var('sel_month',date('m'));
$v_quarter  = get_post_var('sel_quarter',1);
$v_member_id=  get_post_var('sel_district',0);
?>
<script src="<?php echo SITE_ROOT?>public/js/jquery/jquery.chained.mini.js" type="text/javascript"> </script> 
<style>
    .right-Col label,.right-Col label input
    {
        cursor: pointer;
        padding: 5px;
    }
</style>
</style>
<h2 class="module_title"><?php echo __('Báo cáo chi tiết tình hình xử lý hồ sơ') ?></h2>
<form name="frmMain" id="frmMain" action="" method="POST">
    <?php 
        echo $this->hidden('controller', $this->get_controller_url());
        echo $this->hidden('hdn_dsp_all_method', '');
        echo $this->hidden('hdn_method_print', 'print_single_recordtype');
    ?>
<div class="" id="filter">
    <div class="Row">
        <div class="left-Col">
            <label>
                <?php echo __('unit') ?>:
            </label>
        </div>
        <div class="right-Col">
           
            <select name="sel_district" id="sel_district" style="width: 50%" >
                <option value=" ">-- <?php echo __('all unit')?> --</option>
                <?php foreach($arr_all_district as $arr_district):
                        $v_name       = $arr_district['C_NAME'];
                        $v_member_code= $arr_district['C_CODE'];
                        $v_id         = $arr_district['PK_MEMBER'];                                            
                ?>
                <option   data-id="<?php echo $v_id; ?>" value="<?php echo $v_member_code?>"><?php echo $v_name?></option>

                <?php foreach($arr_all_village as $key => $arr_village):
                        $v_village_name      = $arr_village['C_NAME'];
                        $v_member_child_id   = $arr_village['PK_MEMBER'];
                        $v_fk_village_id     = $arr_village['FK_VILLAGE_ID'];
                        $v_member_code  = $arr_village['C_CODE'];
                        $v_parent_id    = $arr_village['FK_MEMBER'];
                        $v_member_code = $v_member_code.'_'.$v_fk_village_id;
                        if($v_parent_id != $v_id)
                        {
                            continue;
                        }
                ?>
                <option  data-id="<?php echo $v_member_child_id; ?>" value="<?php echo $v_member_code ?>"><?php echo '---- '.$v_village_name?></option>
                <?php 
                    unset($arr_all_village[$key]);
                    endforeach;
                ?>
                <?php endforeach;?>
            </select>
        </div>
    </div>
    <!--End chon don vi-->

      <div class="Row">
        <div class="left-Col">
            <label>
                <?php echo __('Lĩnh vực') ?>:
            </label>
        </div>
        <div class="right-Col">
            <select name="sel_record_list_type" id="sel_record_list_type" style="width: 50%">
                <option value="">-- <?php echo __('all field')?> --</option>
                <?php 
                    for($i = 0;$i <count($arr_all_spec); $i ++)
                    {
                        $single_fields                      = $arr_all_spec[$i];
                        $v_fields_code                      = $single_fields['C_CODE'];
                        $v_fields_name                      = $single_fields['C_NAME'];
                        $v_fields_unit_code                 = $single_fields['C_UNIT_CODE'];                                            
                        $v_fields_unit_code_fk_village_id   = $single_fields['C_UNIT_CODE_FK_FILLAGE_ID'];
                        $v_fields_unit_code_fk_village_id   .= " $v_fields_unit_code_fk_village_id";
                        echo "<option value='$v_fields_code' class='{$v_fields_unit_code}{$v_fields_unit_code_fk_village_id}' >$v_fields_name</option>";
                    }
                ?>
            </select>
        </div>
    </div>
    <!--End Lĩnh vục-->
     
    <div class="Row">
        <div class="left-Col">
            <label>
                <?php echo __('Kỳ báo cáo') ?>:
            </label>
        </div>
        <div class="right-Col">
            <label><input value="month" type="radio" checked="true" id="rd_month" name="rd_date" onclick="rd_onclick_date(this)" /><?php echo __('month') ?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <label><input value="quarter" type="radio" id="rd_quarter" name="rd_date" onclick="rd_onclick_date(this)" /><?php echo __('quarter') ?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <label><input value="year" type="radio" id="rd_year" name="rd_date" onclick="rd_onclick_date(this)" /><?php echo __('year') ?></label>
        </div>
    </div>

     <div id="wrp-filder-date">
        <div  style="display: block">
            <div class="Row" > 
                <div class="left-Col">&nbsp;</div>
                <div class="right-Col" >
                    <div id="div-rd_month" class="Row" style="width: 35%;float: left" >
                        <div class="left-Col"><?php echo __('month'); ?>:&nbsp;</div>
                        <div class="right-Col">
                            <select name="sel_month" id="sel_month" style="width: 90%">
                                <?php
                                    for($i =1;$i<=12;$i ++)
                                    {
                                        $v_selected = ($i == $v_month) ? 'selected' : '';
                                        echo "<option $v_selected value='$i'>".__('month')." $i</option>";
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div id="div-rd_quarter" class="Row" style="width: 35%;float: left;display: none" >
                        <div class="left-Col"><?php echo __('quarter'); ?>:</div>
                        <div class="right-Col">
                            <select name="sel_quarter" id="sel_quarter" style="width: 80%">
                                 <?php
                                    for($i = 1;$i<= 4 ;$i ++)
                                    {
                                        $v_selected = ($i == $v_year) ? 'selected' : '';
                                        echo "<option $v_selected value='$i'>".__('quarter')." $i</option>";
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div id="div-sel_year" class="Row" style="width: 30%;">
                        <div class="left-Col"><?php echo __('year'); ?>:</div>
                        <div class="right-Col">
                            <select name="sel_year" id="sel_year" style="width: 90%">
                                 <?php
                                    if(sizeof($arr_year) >0)
                                    {
                                        for($i =0;$i<count($arr_year);$i ++)
                                        {
                                            $v_selected = ($v_year == $arr_year[$i]['C_YEAR']) ? 'selected' : '';
                                            
                                            echo "<option $v_selected value='".$arr_year[$i]['C_YEAR']."'>".$arr_year[$i]['C_YEAR']."</option>";
                                        }
                                    }
                                    else 
                                    {
                                        echo "<option  value='$v_year'> $v_year</option>";
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <div class="Row">
        <div class="left-Col">&nbsp;</div>
        <div class="right-Col">
            <div class="btn-filter">
                    <input type="button" name="btn_print" id="btn_print" class="ButtonAccept" value="<?php echo __('report print'); ?>" onclick="print_member();"/>
                <!--<input type="button" name="btn_back" id="btn_cancel" class="ButtonBack" value="<?php echo __('go back'); ?>" onclick="btn_back_onclick();"/>-->
            </div>
        </div>
    </div>
</div>
</form>
<script>
    
    /**
     * Thay doi dieu kien loc in bao cao
     */
    $(document).ready(function(){
        $("#sel_record_list_type").chained("#sel_district");
    });
    function rd_onclick_date(anchor) 
    {
        var select_id = $(anchor).attr('id') || '';
        if(select_id.trim() == 'rd_month')
        {
            $('#div-rd_quarter').hide();
            $('#div-rd_month').show();
        }
        else if(select_id.trim() == 'rd_year')
        {
            $('#div-rd_month').hide();
            $('#div-rd_quarter').hide();
        }
        else if(select_id.trim() == 'rd_quarter')
        {
            $('#div-rd_month').hide();
            $('#div-rd_quarter').show();
        }
             
    }
    
    function print_member() 
    {
        var f = document.frmMain; 
        var sel_year     = $('#sel_year').val();
        var sel_month    = $('#sel_month').val();
        var sel_district = $('#sel_district option:selected').attr('data-id') || 0;
        var sel_quarter  = $('#sel_quarter').val();
        var sel_rd_date  = $('input[name="rd_date"]:checked').val() || '';
        var sel_record_list_type  = $('#sel_record_list_type ').val() || '';
        
        var sel_rd_date  = $('input[name="rd_date"]:checked').val() || '';
        
        var params =  'district='+ sel_district 
                    + '&sel_month='+ sel_month 
                    + '&quarter=' + sel_quarter 
                    + '&year=' +sel_year 
                    + '&sel_record_list_type=' + sel_record_list_type
                    + '&type_time=' + sel_rd_date;
        var url = $('#hdn_method_print').val() + '?' + params;
        showPopWin(url, 800, 600);
    }
</script>
<?php
$this->template->display('dsp_footer.php');
?>