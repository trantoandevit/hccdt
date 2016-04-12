<?php
defined('DS') or die('no direct access');
$this->template->title = __('report staff');
$this->template->display('dsp_header.php');
$controller = $this->get_controller_url();

$arr_all_village  =  $arr_all_member['arr_all_village'];
$arr_all_district =  $arr_all_member['arr_all_district'];

$v_district    = get_request_var('district',0);
$v_begin_date  = get_request_var('txt_begin_date',date('d/m/Y'));
$v_end_date    = get_request_var('txt_end_date',date('d/m/Y'));

?>
<style>
    .right-Col label,.right-Col label input
    {
        cursor: pointer;
        padding: 5px;
    }
</style>
</style>
<h2 class="module_title"><?php echo __('report all evaluation') ?></h2>
<form name="frmMain" id="frmMain" action="" method="POST">
    <?php 
        echo $this->hidden('controller', $this->get_controller_url());
        echo $this->hidden('hdn_dsp_all_method', '');
        echo $this->hidden('hdn_method_print', 'print_evaluation_single');
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
                <option value="0">--     <?php echo __('unit') ?>      --</option>
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
            Từ ngày: 
        </div>
        <div class="right-Col2">
                 <input type="textbox" name="txt_begin_date" value="<?php echo $v_end_date; ?>" id="txt_begin_date"
                       data-allownull="no" data-validate="date" 
                       data-name="<?php echo __('begin date') ?>" 
                       data-xml="no" data-doc="no" 
                       autofocus="autofocus" 
                       />
                &nbsp;
                <img src="<?php echo $this->image_directory . "calendar.png"; ?>" onclick="DoCal('txt_begin_date')">
              
            <input type="textbox" name="txt_end_date" value="<?php echo $v_end_date; ?>" id="txt_end_date"
                       data-allownull="no" data-validate="date" 
                       data-name="<?php echo __('end date') ?>" 
                       data-xml="no" data-doc="no" 
                       autofocus="autofocus" 
                       />
                &nbsp;
                <img src="<?php echo $this->image_directory . "calendar.png"; ?>" onclick="DoCal('txt_end_date')">
                &nbsp; 
        
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
    function print_member() 
    {
        var f = document.frmMain;
            var xObj = new DynamicFormHelper('','',f);
            if (xObj.ValidateForm(f))
            {
               
                var sel_begin_date = $('#txt_begin_date').val() || 0;
                var sel_end_date   = $('#txt_end_date').val() || 0;
                var sel_district   = $('#sel_district').val() || 0;

                var params = '&district='+ sel_district +  '&txt_begin_date=' + sel_begin_date  + '&txt_end_date=' +sel_end_date;

                var url = $('#hdn_method_print').val() + '?' + params;
                showPopWin(url, 800, 600);
          }
    }
    
    
    
</script>
<?php
$this->template->display('dsp_footer.php');
?>