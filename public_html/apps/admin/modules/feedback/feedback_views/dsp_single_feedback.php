<?php
if (!defined('SERVER_ROOT'))
{
    exit('No direct script access allowed');
}
?>
<?php
//display header
$this->template->title = __('feedback detail');
$this->template->display('dsp_header.php');

$v_id              = isset($arr_single_feedback['PK_FEEDBACK'])?$arr_single_feedback['PK_FEEDBACK']:0;
$v_name            = isset($arr_single_feedback['C_NAME'])?$arr_single_feedback['C_NAME']:'';
$v_address         = isset($arr_single_feedback['C_ADDRESS'])?$arr_single_feedback['C_ADDRESS']:'';
$v_email           = isset($arr_single_feedback['C_EMAIL'])?$arr_single_feedback['C_EMAIL']:'';
$v_date            = isset($arr_single_feedback['C_INIT_DATE'])?$arr_single_feedback['C_INIT_DATE']:'';
$v_title           = isset($arr_single_feedback['C_TITLE'])?$arr_single_feedback['C_TITLE']:'';
$v_content         = isset($arr_single_feedback['C_CONTENT'])?$arr_single_feedback['C_CONTENT']:'';
$v_reply           = isset($arr_single_feedback['C_REPLY'])?$arr_single_feedback['C_REPLY']:'';
$v_website_id      = isset($arr_single_feedback['FK_WEBSITE'])?$arr_single_feedback['FK_WEBSITE']:0;
$v_user_id         = isset($arr_single_feedback['FK_USER'])?$arr_single_feedback['FK_USER']:0;
$v_file_name       = isset($arr_single_feedback['C_FILE_NAME'])?$arr_single_feedback['C_FILE_NAME']:'';

$v_public          = isset($arr_single_feedback['C_PUBLIC'])?$arr_single_feedback['C_PUBLIC']:0;
$v_public          = ($v_public == 1)?'checked':'';
        
$v_private_answer  = isset($arr_single_feedback['C_PRIVATE_ANSWER'])?$arr_single_feedback['C_PRIVATE_ANSWER']:0;
$v_private_answer  = ($v_private_answer == 1)?'checked':'';

$v_user_name       = isset($arr_single_feedback['C_USER_NAME'])?$arr_single_feedback['C_USER_NAME']:'';

?>
<script src="<?php echo SITE_ROOT; ?>public/tinymce/script/tiny_mce.js"></script>

<h2 class="module_title"><?php echo __('feedback detail'); ?></h2>
<form name="frmMain" id="frmMain" action="" method="POST">
<?php
    echo $this->hidden('controller', $this->get_controller_url());

    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_feedback');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_feedback');
    echo $this->hidden('hdn_update_method', 'update_feedback');
    echo $this->hidden('hdn_delete_method', 'delete_banner');

    echo $this->hidden('hdn_item_id', $v_id);
    echo $this->hidden('hdn_item_id_list', '');
    echo $this->hidden('XmlData', '');
?>
    <!--ho va ten-->
    <div class="Row">
        <div class="left-Col"><?php echo __('full name')?></div>
        <div class="right-Col"><?php echo $v_name?></div>
    </div>
     <!--dia chi-->
    <div class="Row">
        <div class="left-Col"><?php echo __('address')?> </div>
        <div class="right-Col"><?php echo $v_address?></div>
    </div>
      <!--emai-->
    <div class="Row">
        <div class="left-Col">Email </div>
        <div class="right-Col"><?php echo $v_email?></div>
    </div>
      <!--tieu de-->
    <div class="Row">
        <div class="left-Col"><?php echo ('title')?></div>
        <div class="right-Col">
            <input disabled class="txt-style" type="textbox" name="txt_title" id="txt_title" value="<?php echo $v_title?>" size="100">
        </div>
    </div>
      <!--emai-->
    <div class="Row">
        <div class="left-Col"><?php echo __('content')?></div>
        <div class="right-Col">
            <textarea disabled name="txt_content" id="txt_content" style="width: 632px;min-height: 134px;" ><?php echo $v_content;?></textarea>
        </div>
    </div>
    <!--tra loi cau hoi-->
    <div class="Row">
        <div class="left-Col">
            <?php echo __('reply')?>
        </div>
        <div class="right-Col">
             <textarea name="txt_reply" style="width: 632px;min-height: 134px;" id="txt_reply" >
                 <?php echo $v_reply; ?>
             </textarea>
        </div>
    </div>
      <!--file dinh kem-->
    <div class="Row">
        <div class="left-Col"><?php echo __('attachments')?></div>
        <div class="right-Col">
            <?php if($v_file_name != '' && $v_file_name != NULL):
                    
            ?>
            <a target="_blank" href="<?php echo SITE_ROOT.'upload/'.$v_file_name?>"><?php echo end(explode(DS, $v_file_name))?></a>
            <?php else:?>
            <?php echo __('no attachments')?>
            <?php endif;?>
        </div>
    </div>
    <!--nguoi tra loi cau hoi-->
    <div class="Row">
        <div class="left-Col">
            <?php echo __('init user');?>
        </div>
        <div class="right-Col">
            <input type="textbox" name="txt_init_user" id="txt_init_user" value="<?php echo $v_user_name?>" size="80" disabled/> 
        </div>
    </div>
    <!--hien thi-->
    <div class="Row">
        <div class="left-Col">
            &nbsp;
        </div>
        <div class="right-Col">
            <label>
                <input <?php echo $v_public;?> type="checkbox" name="chk_public" id="chk_public" />
                <?php echo __('display')?>
            </label> 
        </div>
    </div>
    <!--private answer-->
    <div class="Row">
        <div class="left-Col">
            &nbsp;
        </div>
        <div class="right-Col">
            <label>
                <input <?php echo ($v_private_answer == 1)?'disabled':'';?> <?php echo $v_private_answer;?> type="checkbox" name="chk_private_answer" id="chk_private_answer" />
                <?php echo __('private answer')?>
            </label> 
        </div>
    </div>
    <!--button-->
    <div class="button-area">
        <input type="button" class="ButtonAccept" onclick="btn_update_onclick();" value="<?php echo __('accept')?>">
        <input type="button" class="ButtonBack" onclick="btn_back_onclick();" value="<?php echo __('back')?>">
    </div> 
</form>
<script>
    tinyMCE_init(); 
    tinyMCE.execCommand('mceAddControl', false, 'txt_reply');
</script>
<?php
$this->template->display('dsp_footer.php');
?>