<?php
if (!defined('SERVER_ROOT'))
{
    exit('No direct script access allowed');
}
?>
<?php
//display header
$this->template->title = __('banner detail');
$this->template->display('dsp_header.php');
//var_dump($arr_single_media);

$v_file_name = isset($arr_single_banner['C_FILE_NAME']) ? $arr_single_banner['C_FILE_NAME'] : '';
$v_extension = substr($v_file_name, strrpos($v_file_name, '.') + 1);
$v_status    = isset($arr_single_banner['C_STATUS']) ? $arr_single_banner['C_STATUS'] : '';
$v_default   = isset($arr_single_banner['C_DEFAULT']) ? $arr_single_banner['C_DEFAULT'] : '';
$v_banner_id = isset($arr_single_banner['PK_BANNER']) ? $arr_single_banner['PK_BANNER'] : '';
?>

<form name="frmMain" id="frmMain" action="" method="POST"><?php
echo $this->hidden('controller', $this->get_controller_url());

echo $this->hidden('hdn_dsp_single_method', 'dsp_single_banner');
echo $this->hidden('hdn_dsp_all_method', 'dsp_all_banner');
echo $this->hidden('hdn_update_method', 'update_banner');
echo $this->hidden('hdn_delete_method', 'delete_banner');

echo $this->hidden('hdn_item_id', $v_banner_id);
echo $this->hidden('hdn_item_id_list', '');

echo $this->hidden('hdn_file_name', $v_file_name);
echo $this->hidden('XmlData', '');

//Luu dieu kien loc
$this->write_filter_condition(array('sel_goto_page', 'sel_rows_per_page'));
?>
    <!-- Toolbar -->
    <h2 class="module_title"><?php echo __('banner detail'); ?></h2>
    <!-- /Toolbar -->
    <div class="Row">
        <input type="button" class="ButtonMediaService" value="<?php echo __('select avatar') ?>" onclick="btn_service_media_onclick()">
    </div>
    <div style="padding-bottom: 10px;">
        <center id="show_img">

            <?php if ($v_extension == 'swf'): ?>
                <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
                        codebase="<?php SITE_ROOT . "upload/flash/swflash.cab" ?>" 
                        width="150" height="150" >
                    <param name="movie" value="" />
                    <param name="quality" value="high" />
                    <param name="wmode" value="transparent" />
                    <embed id="banner_img" src="<?php echo SITE_ROOT . "upload/" . $v_file_name; ?>" wmode="transparent" quality="high"
                           pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"
                           style="max-width:836;max-height:120">
                    </embed>
                </object>
            <?php else: ?>
                <img id="banner_img" src="<?php echo SITE_ROOT . "upload/" . $v_file_name; ?>" style="max-width: 100%;max-height:120;overflow: hidden;"/>
            <?php endif; ?>
        </center>
    </div>
    <div>
        <div class="Row">
            <div class="left-Col2">
                <div class="Row">
                    <div class="left-Col2" style="font-weight: bold;"><?php echo __('status') ?></div>
                    <div class="right-Col2" style="font-weight: inherit">
                    <label>                    
	                    <input type="checkbox" name="chk_banner_status" id="chk_banner_status"  <?php echo ($v_status == 1) ? 'checked' : ''; ?>  />
	                     Hiển thị
                    </label>
                    <br/>
                    <label>
	                    <input type="checkbox" name="chk_banner_default" id="chk_banner_default"  <?php echo ($v_default == 1) ? 'checked' : ''; ?> />
	                    <?php  echo __('banner default');?>
                    </label>
                    
                       <!-- 
                        <select name="banner_status" id="banner_status" onchange="banner_status_onchange(this.value)">
                            <option value="0"><?php echo __('display none'); ?></option>
                            <option value="1" <?php echo ($v_status == 1) ? 'selected' : ''; ?> ><?php echo __('display'); ?></option>
                        </select>
                         -->
                    </div>
                </div>
                <div class="Row">
                    <div class="left-Col2" ><?php //echo __('banner default') ?></div>
                    <div class="right-Col2">
                    <!-- 
                        <select name="banner_default" id="banner_default">
                            <option value="0"><?php echo __('no'); ?></option>
                            <option value="1" <?php echo ($v_default == 1) ? 'selected' : ''; ?> ><?php echo __('yes'); ?></option>
                        </select>
                    -->
                    </div>
                </div>
            </div>
            <div class="right-Col2">
                <div id="div_category">
                    <table width="100%" class="adminlist" cellspacing="0" border="1">
                        <colgroup>
                            <col width="5%" />
                            <col width="95%" />
                        </colgroup>
                        <tr>
                            <th><input type="checkbox" name="chk_check_all" onclick="toggle_check_all(this,this.form.chk);"/></th>
                            <th><?php echo __('category name'); ?></th>
                        </tr>
                        <?php
                        foreach ($arr_all_category_on_web as $arr_row):
                            $v_category_id    = $arr_row['PK_CATEGORY'];
                            $v_category_name  = $arr_row['C_NAME'];
                            $v_internal_order = $arr_row['C_INTERNAL_ORDER'];
                            $v_level          = strlen($v_internal_order) / 3 - 1;
                            $v_depend         = $arr_row['C_DEPEND'];
                            ?>
                            <tr>
                                <td>
                                    <input type="checkbox" 
                                           name="chk" id="chk_<?php echo $v_category_id; ?>"
                                           value="<?php echo $v_category_id; ?>"
                                           <?php echo ($v_depend > 0) ? 'disabled' : ''; ?>
                                           <?php echo in_array($v_category_id, $arr_all_cat_to_check) ? 'CHECKED' : ''; ?>
                                           >
                                </td>
                                <td>
                                    <label 
                                    <?php if ($v_depend <= 0): ?>
                                            for="chk_<?php echo $v_category_id; ?>"
                                        <?php endif; ?>
                                        >
                                            <?php
                                            for ($i = 0; $i < $v_level; $i++)
                                            {
                                                echo ' -- ';
                                            }
                                            echo $v_category_name;
                                            ?>
                                    </label>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>
        <br>
        <label class="required" id="message_err"></label>
        <br>
        <div class="button-area">
            <input type="button" name="btn_update" id="btn_update" class="ButtonAccept" value="<?php echo __('update'); ?>" onclick="btn_update_banner_onclick()"/>
            <input type="button" name="btn_back" id="btn_cancel" class="ButtonBack" value="<?php echo __('go back'); ?>" onclick="btn_back_onclick();"/>
        </div>
    </div>
</form>
<script>

    function btn_service_media_onclick()
    {
        var url="<?php echo $this->get_controller_url('advmedia') . "dsp_service/image"; ?>";
        showPopWin(url, 800, 600,do_attach);
    }
    function do_attach(returnVal)
    {
        if(returnVal[0]){
            var media_file_ext    = returnVal[0].type;
            var media_file_name   = returnVal[0].name;
            var media_file_path   = returnVal[0].path;

            $('#hdn_file_name').attr('value',media_file_path);
            $('#show_img').html('');

            str = '';
     		if (media_file_ext != '')
     		{
         		if (media_file_ext.toLowerCase() != 'swf')
         		{
	            	str = "<img src='<?php echo SITE_ROOT . "upload/"; ?>"+media_file_path+"'style='max-width: 100%;max-height:120;' id='banner_img'/>";
         		}
         		else
         		{
         			str = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="" width="150" height="150">';
         			str += '<param name="movie" value="">';
         			str += '<param name="quality" value="high">';
         			str += '<param name="wmode" value="transparent">';
         			str += '<embed src="/upload/' + media_file_path + '" wmode="transparent" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" style="max-width:590;max-height:70">';
         			str += '</object>';
         		}
     		}
     		
            $('#show_img').html(str);
        }
       
        //str= "<img src='<?php echo $this->image_directory; ?>"+media_file_ext+"-image.png' style='max-width:836;max-height:120'/>";
    }
    function btn_update_banner_onclick()
    {
        if($('#hdn_media_id').val()=="")
        {
            $('#message_err').html('Báº¡n chÆ°a chá»�n áº£nh banner !!!');
            return false;
        }
        var arr_checked_category = new Array();
        var q="#div_category input[name='chk'] ";
        $(q).each(function (index){
            if ($(this).is(':checked'))
            {
                arr_checked_category.push($(this).val());
            }
        });
        
        $('#hdn_item_id_list').val(arr_checked_category.join());
        btn_update_onclick();
    }
</script>
<?php
$this->template->display('dsp_footer.php');
?>