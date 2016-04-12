<?php if (!defined('SERVER_ROOT')) { exit('No direct script access allowed');} ?>
<?php
//display header
$this->template->title = __('update website');
$this->template->display('dsp_header.php');
?>
<?php
if (isset($arr_single_website['PK_WEBSITE']) > 0)
{
    $v_website_id           = $arr_single_website['PK_WEBSITE'];
    $v_website_code         = $arr_single_website['C_CODE'];
    $v_website_name         = $arr_single_website['C_NAME'];
    $v_order                = $arr_single_website['C_ORDER'];
    $v_status               = $arr_single_website['C_STATUS'];
    $v_monitor_user_name    = $arr_single_website['C_NAME_USER'];
    $v_monitor_user_id      = $arr_single_website['FK_USER'];
    $v_theme_name           = $arr_single_website['C_THEME_CODE'];
    $v_theme_code           = $arr_single_website['C_THEME_CODE'];
    $v_theme_lang           = $arr_single_website['FK_LANG'];
}
else
{
    $v_website_id           = 0;
    $v_website_code         = '';
    $v_website_name         = '';
    $v_order                = $arr_single_website['C_ORDER'] + 1;
    $v_status               = 1;

    $v_monitor_user_name    = '';
    $v_monitor_user_id      = '';
    $v_theme_name           = '';
    $v_theme_code           = '';
}
//temp
/*$v_monitor_user_name    = 'Nguyen Hai Binh';
$v_monitor_user_id      = '81';
$v_theme_name           = 'haiduong';
$v_theme_code           = 'haiduong';*/

?>
<form name="frmMain" id="frmMain" action="" method="POST"><?php
    echo $this->hidden('controller', $this->get_controller_url());

    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_website');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_website');
    echo $this->hidden('hdn_update_method', 'update_website');
    echo $this->hidden('hdn_delete_method', 'delete_website');

    echo $this->hidden('hdn_item_id', $v_website_id);
    echo $this->hidden('hdn_item_id_list', '');

    echo $this->hidden('XmlData', '');
    echo $this->hidden('hdn_monitor_user_id', $v_monitor_user_id);
    echo $this->hidden('hdn_theme_code', $v_theme_code);

    //Luu dieu kien loc
    $this->write_filter_condition(array('sel_goto_page', 'sel_rows_per_page'));
    ?>
    <!-- Toolbar -->
    <h2 class="module_title"><?php echo __('update website'); ?></h2>
    <!-- /Toolbar -->

    <!-- Update Form -->
    <div class="Row">
        <div class="left-Col">
            <?php echo __('website code'); ?>
        </div>
        <div class="right-Col">
            <input type="text" name="txt_code" value="<?php echo $v_website_code; ?>" id="txt_code"
                   class="inputbox" maxlength="500" style="width:40%"
                   onKeyUp="ConverUpperCase('txt_code',this.value);"
                   data-allownull="no" data-validate="text"
                   data-name="<?php echo __('website code'); ?>"
                   data-xml="no" data-doc="no"
                   onblur="check_code_onblur(this)" autofocus="autofocus"
                   /><span class="required">(*)</span>
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">
            <?php echo __('website name'); ?>
        </div>
        <div class="right-Col">
            <input type="text" name="txt_name" value="<?php echo $v_website_name; ?>" id="txt_name"
                   class="inputbox" style="width:60%"
                   data-allownull="no" data-validate="text"
                   data-name="<?php echo __('website name'); ?>"
                   data-xml="no" data-doc="no"
                   onblur="check_name_onblur(this)"

                   /><span class="required">(*)</span>
        </div>
    </div>

    <div class="Row">
        <div class="left-Col">
            <?php echo __('order'); ?>
        </div>
        <div class="right-Col">
            <input type="text" name="txt_order" value="<?php echo $v_order; ?>" id="txt_order"
                   class="inputbox" size="4" maxlength="3"
                   onKeyDown="return handleEnter(this, event);"
                   data-allownull="no" data-validate="unsignNumber"
                   data-name="<?php echo __('order'); ?>"
                   data-xml="no" data-doc="no"
                   /><span class="required">(*)</span>
        </div>
    </div>

    <div class="Row">
        <div class="left-Col">
            <?php echo __('monitor user'); ?>
        </div>
        <div class="right-Col">
            <input type="text" name="txt_monitor_user" value="<?php echo $v_monitor_user_name; ?>" id="txt_monitor_user"
                   class="inputbox" style="width:40%"
                   onKeyDown="return handleEnter(this, event);"
                   data-allownull="no" data-validate="text"
                   data-name="<?php echo __('monitor user'); ?>"
                   data-xml="no" data-doc="no" readonly="true"
                   /><span class="required">(*)</span>
            <input type="button" onclick="btn_select_user_onclick()" class="ButtonUserService"/> 
        </div>
    </div>

    <div class="Row">
        <div class="left-Col">
            <?php echo __('choose a theme'); ?>
        </div>
        <div class="right-Col">
            <input type="text" name="txt_theme_name" value="<?php echo $v_theme_name; ?>" id="txt_theme_name"
                   class="inputbox" style="width:40%"
                   onKeyDown="return handleEnter(this, event);"
                   data-allownull="no" data-validate="text"
                   data-name="<?php echo __('choose a theme'); ?>"
                   data-xml="no" data-doc="no" readonly="true"
                   /><span class="required">(*)</span>
            <input type="button" onclick="btn_select_theme_onclick()" class="ButtonThemeService"/>
        </div>
    </div>
    <div class="Row">
        <div class="left-Col"><?php echo __('Chọn ngôn ngữ');?></div>
        <div class="right-Col">
            <select name="website_lang" id="website_lang">
                <?php foreach ($arr_all_lang as $row):?>
                <option value="<?php echo $row['PK_LIST'];?>" <?php echo ($v_theme_lang == $row['PK_LIST'])?'selected':'';?>>
                    <?php echo $row['C_NAME']?>
                </option>
                <?php endforeach;?>
            </select>
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">
            <?php echo __('status'); ?>
        </div>
        <div class="right-Col">
            <input type="checkbox" name="chk_status" value="1"
                <?php echo ($v_status > 0) ? ' checked' : ''; ?>
                   id="chk_status"
                   /><label for="chk_status"><?php echo __('active status'); ?></label><br/>
            
        </div>
    </div>

    <div class="button-area">
        <input type="button" name="btn_update" id="btn_update" class="ButtonAccept" value="<?php echo __('update');?>" onclick="check_err()"/>
        <input type="button" name="btn_cancel" id="btn_cancel" class="ButtonBack" value="<?php echo __('go back'); ?>" onclick="btn_back_onclick();"/>
    </div>
</form>
<script>
    function btn_select_user_onclick()
    {
        showPopWin('<?php echo $this->get_controller_url('user'); ?>dsp_all_user_by_ou_to_add/&pop_win=1', 800, 500, do_attach_user);
        
    }
    function do_attach_user(returnVal)
    {
        $("#hdn_monitor_user_id").val(returnVal[0].user_id);
        $("#txt_monitor_user").val(returnVal[0].user_name);
    }
    function btn_select_theme_onclick()
    {
        showPopWin('<?php echo $this->get_controller_url(); ?>dsp_all_theme_to_add/&pop_win=1', 800, 500, do_attach_theme);
    }
    function do_attach_theme(returnVal)
    {
        $('#hdn_theme_code').val(returnVal[0].theme_code);
        $("#txt_theme_name").val(returnVal[0].theme_name);
    }
    function check_code_onblur(txt_code)
    {

        var code = $(txt_code).val();
        var website_id = '<?php echo $v_website_id;?>';
        $.ajax({
                type: 'POST',
                url: '<?php echo $this->get_controller_url();?>check_code',
                data: {'website_code': code,'website_id':website_id},
                success: function(object){
                    val = object;
                    if(val==1)
                    {
                        $('#err_text_code').remove();
                        $('[name="txt_code"]').parent().append('<label id="err_text_code" class="required">&nbsp;Mã chuyên trang đã được sử dụng</label>');
                 
                    }
                    else
                    {
                        $('#err_text_code').remove();
          
                    }
                }
            });
    }
    function check_name_onblur(txt_name)
    {
        var name = $(txt_name).val();
        var website_id = '<?php echo $v_website_id;?>';
        $.ajax({
                type: 'POST',
                url: '<?php echo $this->get_controller_url();?>check_name',
                data: {'website_name': name,'website_id':website_id},
                success: function(object){
                    val = object;
                    if(val==1)
                    {
                        $('#err_text_name').remove();
                        $('[name="txt_name"]').parent().append('<label id="err_text_name" class="required">&nbsp;Tên chuyên trang đã được sử dụng</label>');

                    }
                    else
                    {
                        $('#err_text_name').remove();

                    }
                }
            });
    }
    function check_err()
    {
       var code_check = $('#err_text_code').html();
       var name_check = $('#err_text_name').html();
       //alert(name_check);
       if(code_check == null && name_check == null)
       {
            btn_update_onclick();
       }
       else
       {
           $('#Message_err').remove();
           str = '<div id="Message_err" class="Row" style="margin-top:40px;"><label class="required">Thông tin bạn nhập không hợp lệ</label></div>';
           $('[name="chk_status"]').parent().parent().append(str);
       }
    }
</script>
<?php
$this->template->display('dsp_footer.php');
?>