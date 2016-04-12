<?php if (!defined('SERVER_ROOT')) { exit('No direct script access allowed');} ?>
<?php
//display header
$this->template->title = __('citizens question detail');
$this->template->display('dsp_header.php');

$v_cq_id           = isset($arr_single_question['PK_CQ'])?$arr_single_question['PK_CQ']:'0';
$v_field_id        = isset($arr_single_question['FK_FIELD'])?$arr_single_question['FK_FIELD']:'';
$v_sender          = isset($arr_single_question['C_NAME'])?$arr_single_question['C_NAME']:''; 
$v_address         = isset($arr_single_question['C_ADDRESS'])?$arr_single_question['C_ADDRESS']:''; 
$v_phone           = isset($arr_single_question['C_PHONE'])?$arr_single_question['C_PHONE']:''; 
$v_email           = isset($arr_single_question['C_EMAIL'])?$arr_single_question['C_EMAIL']:''; 
$v_date_send       = isset($arr_single_question['C_DATE'])?$arr_single_question['C_DATE']:''; 
$v_order           = isset($arr_single_question['C_ORDER'])?$arr_single_question['C_ORDER']:''; 
$v_title           = isset($arr_single_question['C_TITLE'])?$arr_single_question['C_TITLE']:''; 
$v_content         = isset($arr_single_question['C_CONTENT'])?$arr_single_question['C_CONTENT']:''; 
$v_answer          = isset($arr_single_question['C_ANSWER'])?$arr_single_question['C_ANSWER']:''; 
$v_status          = isset($arr_single_question['C_STATUS'])?$arr_single_question['C_STATUS']:''; 
$v_slug            = isset($arr_single_question['C_SLUG'])?$arr_single_question['C_SLUG']:''; 

?>

<script src="<?php echo SITE_ROOT; ?>public/tinymce/script/tiny_mce.js"></script>

<form id="frmMain" name="frmMain" action="" method="POST">
<?php echo $this->hidden('controller',$this->get_controller_url());?>
<?php echo $this->hidden('hdn_item_id', $v_cq_id);?>
<?php echo $this->hidden('hdn_item_id_list','');?>
<?php echo $this->hidden('hdn_item_id_swap', '');?>
<?php echo $this->hidden('hdn_delete_method', '');?>
<?php echo $this->hidden('hdn_dsp_single_method', '');?>
<?php echo $this->hidden('hdn_dsp_all_method', 'dsp_all_cq');?>
<?php echo $this->hidden('hdn_update_method', 'update_question');?>
<?php echo $this->hidden('hdn_current_order', $v_order);?>
<?php echo $this->hidden('XmlData', '');?>
    <!-- Toolbar -->
    <h2 class="module_title"><?php echo __('citizens question detail'); ?></h2>
    <!-- /Toolbar -->
    <div class="Row">
        <div class="left-Col">
            <label> <?php echo __('field');?> </label>
        </div>
        <div class="right-Col">
            <select id="select_field" name="select_field" style="min-width: 206px">
                <?php foreach ($arr_all_field as $field): ?>
                    <?php if ($field['C_STATUS'] == 1): ?>
                        <option value="<?php echo $field['PK_FIELD']; ?>" <?php echo ($v_field_id == $field['PK_FIELD']) ? 'selected' : ''; ?>>
                            <?php echo $field['C_NAME']; ?>
                        </option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">
            <label> <?php echo __('sender');?> </label>
        </div>
        <div class="right-Col">
            <input type="textbox" name="txt_sender" value="<?php echo $v_sender?>" size="40">
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">
            <label> <?php echo __('address');?> </label>
        </div>
        <div class="right-Col">
            <input type="textbox" name="txt_address" value="<?php echo $v_address?>" size="70">
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">
            <label> <?php echo __('phone');?> </label>
        </div>
        <div class="right-Col">
            <input type="textbox" name="txt_phone" value="<?php echo $v_phone?>" size="40">
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">
            <label> <?php echo __('email');?> </label>
        </div>
        <div class="right-Col">
            <input type="textbox" name="txt_email" value="<?php echo $v_email?>" size="40">
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">
            <label> <?php echo __('date submitted');?> </label>
        </div>
        <div class="right-Col">
            <input type="textbox" name="txt_datesend" value="<?php echo $v_date_send?>" size="40" disabled>
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">
            <label> <?php echo __('order');?> </label>
        </div>
        <div class="right-Col">
            <input type="textbox" name="txt_order" value="<?php echo $v_order?>" size="20">
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">
            <label> <?php echo __('title');?> </label>
        </div>
        <div class="right-Col">
            <input type="textbox" name="txt_title"
                   onkeyup="auto_slug(this,'#txt_slug');" 
                   value="<?php echo $v_title?>" size="70">
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">
            <label> <?php echo __('slug');?> </label>
        </div>
        <div class="right-Col">
            <input type="textbox" name="txt_slug" id="txt_slug"value="<?php echo $v_slug?>" size="70">
        </div>
    </div>
    <div class="Row">
        <div class="left-Col"><label><?php echo __('content')?></label></div>
    </div>
    <div class="Row">
        <textarea 
                name="txt_content" style="width:100%" id="txt_content"
                ><?php echo $v_content; ?></textarea>
    </div>
    <div class="Row">
        <div class="left-Col"><label><?php echo __('answer')?></label></div>
    </div>
    <div class="Row">
        <textarea 
                name="txt_answer" style="width:100%" id="txt_answer"
                ><?php echo $v_answer; ?></textarea>
    </div>
    <div class="Row" style="margin-top: 5px;">
        <div class="left-Col">
            <label><?php echo __('status');?></label>
        </div>
        <div class="right-Col">
            <input type="checkbox" id="chk_status" name="chk_status" <?php echo ($v_status=='1')?'checked':'';?>>
            <label for="chk_status"><?php echo __('display');?></label>
        </div>
    </div>
    <div class="button-area">
        <input type="button" name="addnew" class="ButtonAccept" value="<?php echo __('apply'); ?>" onclick="btn_update_onclick();"/>
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