<?php if (!defined('SERVER_ROOT')) { exit('No direct script access allowed');} ?>
<?php
//display header
$this->template->title = __('citizens question detail');
$this->template->display('dsp_header.php');

$v_field_id        = isset($arr_single_field['PK_FIELD'])?$arr_single_field['PK_FIELD']:'';
$v_field_name      = isset($arr_single_field['C_NAME'])?$arr_single_field['C_NAME']:'';
$v_date            = isset($arr_single_field['C_DATE'])?$arr_single_field['C_DATE']:date('d/m/Y'); 
$v_order           = isset($arr_single_field['C_ORDER'])?$arr_single_field['C_ORDER']:$arr_single_field['C_ORDER_MAX']+1; 
$v_status          = isset($arr_single_field['C_STATUS'])?$arr_single_field['C_STATUS']:''; 
?>

<script src="<?php echo SITE_ROOT; ?>public/tinymce/script/tiny_mce.js"></script>

<form id="frmMain" name="frmMain" action="" method="POST">
<?php echo $this->hidden('controller',$this->get_controller_url());
 echo $this->hidden('hdn_item_id', $v_field_id);
 echo $this->hidden('hdn_item_id_list','');
 echo $this->hidden('hdn_item_id_swap', '');
 echo $this->hidden('hdn_delete_method', '');
 echo $this->hidden('hdn_dsp_single_method', '');
 echo $this->hidden('hdn_dsp_all_method', 'dsp_all_cq');
 echo $this->hidden('hdn_update_method', 'update_field');
 echo $this->hidden('hdn_current_order', $v_order);
 echo $this->hidden('hdn_tab_select', 'field');
 echo $this->hidden('XmlData', '');
 ?>
    <!-- Toolbar -->
    <h2 class="module_title"><?php echo __('field detail'); ?></h2>
    <!-- /Toolbar -->
    <div class="Row">
        <div class="left-Col">
            <label> <?php echo __('field name');?> </label>
        </div>
        <div class="right-Col">
            <input type="textbox" name="txt_field_name" id="txt_field_name" value="<?php echo $v_field_name?>" size="40">
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">
            <label> <?php echo __('order');?> </label>
        </div>
        <div class="right-Col">
            <input type="textbox" name="txt_order" id="txt_order" value="<?php echo $v_order?>" size="20">
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">
            <label> <?php echo __('init date');?> </label>
        </div>
        <div class="right-Col">
            <input type="textbox" name="txt_date" id="txt_date" value="<?php echo $v_date?>" size="30" disabled/>
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">
            <label> <?php echo __('status');?> </label>
        </div>
        <div class="right-Col">
            <label for="chk_status">
                <input type="checkbox" name="chk_status" id="chk_status" <?php echo ($v_status=='1')?'checked':'';?>/>
                <?php echo __('display');?>
            </label>
        </div>
    </div>
    <div class="button-area">
        <input type="button" name="addnew" class="ButtonAccept" value="<?php echo __('update'); ?>" onclick="btn_update_onclick();"/>
        <input type="button" name="trash" class="ButtonBack" value="<?php echo __('back'); ?>" onclick="btn_back_onclick();"/>
    </div>
</form>
<script>
     tinyMCE_init();
     tinyMCE.execCommand('mceAddControl', false, 'txt_content');
     tinyMCE.execCommand('mceAddControl', false, 'txt_answer');
</script>
<?php
$this->template->display('dsp_footer.php');
?>