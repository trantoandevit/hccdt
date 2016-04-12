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
            <select id="sel_district" name="sel_district" style="width: 50%">
                <option value="">--     <?php echo __('unit') ?>      --</option>
                <?php 
                    foreach($arr_all_district as $arr_district)
                    {
                        $v_name = $arr_district['C_NAME'];
                        $v_id   = $arr_district['PK_MEMBER'];
                        $v_checked ='';
                        $v_checked = ($v_id == $v_member_id)?' selected':'';
                        echo "<option $v_checked value='$v_id'>$v_name</option>";
                         foreach($arr_all_village as $key => $arr_village)
                         {
                            $v_village_name = $arr_village['C_NAME'];
                            $v_village_id   = $arr_village['PK_MEMBER'];
                            $v_parent_id    = $arr_village['FK_MEMBER'];
                            if($v_parent_id != $v_id)
                            {
                                continue;
                            }
                            $v_checked = ($v_village_id == $v_member_id)?' selected':'';

                            echo "<option $v_checked value='$v_village_id'> ----- $v_village_name</option>";
                         }
                    }
                    ?>
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
            <select id="sel_record_list_type" name="sel_record_list_type" style="width: 50%">
                <option value=""> --     <?php echo __('Lĩnh vực') ?>      --</option>
                <?php
                    for($i = 0;$i<sizeof($arr_all_record_list_type) ;$i ++)
                    {
                        $v_record_list_type_id   = $arr_all_record_list_type[$i]['PK_LIST'];
                        $v_record_list_type_code = $arr_all_record_list_type[$i]['C_CODE'];
                        $v_record_list_type_name = $arr_all_record_list_type[$i]['C_NAME'];
                        
                        echo "<option value='$v_record_list_type_id' >$v_record_list_type_name</option>";
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
                <?php if (session::check_permission('QL_IN_BAO_CAO_TONG_HOP_DANH_GIA_CAN_BO')): ?>
                    <input type="button" name="btn_print" id="btn_print" class="ButtonAccept" value="<?php echo __('report print'); ?>" onclick="print_member();"/>
                <?php endif; ?>
                <!--<input type="button" name="btn_back" id="btn_cancel" class="ButtonBack" value="<?php echo __('go back'); ?>" onclick="btn_back_onclick();"/>-->
            </div>
        </div>
    </div>
</div>
</form>
<script>
    //<![CDATA[
    
    /**
     * Thay doi dieu kien loc in bao cao
     */
    $(document).ready(function(){
       $('#sel_record_type').chained('#sel_record_list_type') ;
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
        var sel_year     = $('#sel_year').val() || 0;
        var sel_month    = $('#sel_month').val() || 0;
        var sel_district = $('#sel_district').val() || 0;
        var sel_quarter  = $('#sel_quarter').val() || 0;
        
//        var sel_record_type       = $('#sel_record_type').val() || '';
        var sel_record_list_type  = $('#sel_record_list_type').val() || '';
        
        var sel_rd_date  = $('input[name="rd_date"]:checked').val() || '';
        
        var params = '';
        if(sel_rd_date == 'year')
        {
            params += '&district='+ sel_district + '&year=' +sel_year;
        }
        else if(sel_rd_date == 'month')
        {
            params += '&district='+ sel_district +  '&month=' + sel_month  + '&year=' +sel_year;
        }
        else if(sel_rd_date == 'quarter')
        {
            params += '&district='+ sel_district +  '&quarter=' + sel_quarter  + '&year=' +sel_year;
        }
        else
        {
            return false;
        }
        if(parseInt(sel_record_list_type) > 0)
        {
            params +=  '&sel_record_list_type='+ sel_record_list_type ;
        }
        
        var url = $('#hdn_method_print').val() + '?' + params;
        showPopWin(url, 800, 600);
    }
    //]]>
    
</script>
<?php
$this->template->display('dsp_footer.php');
?>