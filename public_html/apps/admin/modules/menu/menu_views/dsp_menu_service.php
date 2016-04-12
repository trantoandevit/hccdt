<?php
if (!defined('SERVER_ROOT')) {
    exit('No direct script access allowed');
}
$this->template->title = 'Menu service';
$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');
@session::init();
$v_is_admin = session::get('is_admin');
$v_user_id  = session::get('user_id');

$tab_select = get_request_var('type','');
?>
<form action="" name="frmMain" id="frmMain" method="POST">
    <div id="tabs_service_menu">
        <div>
            <ul>
                <li><a href="#tab_url" ><?php echo __('Url ngoài');?></a></li>
                <li><a href="#tab_category" ><?php echo __('Chuyên mục');?></a></li>
                <li><a href="#tab_article" onclick="tab_article_onclick();"><?php echo __('Tin bài');?></a></li>
                <li><a href="#tab_module" onclick="tab_article_onclick();"><?php echo __('module');?></a></li>
            </ul>
        </div>
        <div id="tab_category">
            <h2 class="module_title"><?php echo __('Chuyên mục') ?></h2>
            <table width="100%" class="adminlist" cellspacing="0" border="1">
                <colgroup>
                    <col width="100%" />
                </colgroup>
                <tr>
                    <th><?php echo __('Tên chuyên mục'); ?></th>
                </tr>
            <?php $row = 0; ?>
            <?php foreach ($arr_all_category as $row_cat):
                    $v_cat_name         = $row_cat['C_NAME'];
                    $v_cat_id           = $row_cat['PK_CATEGORY'];
                    $v_cat_slug         = $row_cat['C_SLUG'];
                    $v_status           = $row_cat['C_STATUS'];
                    $v_internal_order   = $row_cat['C_INTERNAL_ORDER'];
                    $v_level            = strlen($v_internal_order)/3-1;
                    $v_level_text='';
                    for($i=0;$i<$v_level;$i++)
                    {
                        $v_level_text.=' -- ';
                    }
            ?>
            <tr class="<?php echo ($v_status==1)?"row$row":"line-through";?>">
                <td>
                    <a 
                       <?php if($v_status==1):?>
                       href="javascript:void(0)" onclick="row_cat_onclick(this)" 
                       <?php endif;?>
                       data-cat_id="<?php echo $v_cat_id;?>"
                       data-cat_name="<?php echo $v_cat_name;?>"
                       data-cat_slug="<?php echo $v_cat_slug;?>">
                        <?php echo $v_level_text.$v_cat_name;?>
                    </a>
                </td>
            </tr>
            <?php $row=($row==0)?1:0;?>
            <?php endforeach;?>
            </table>
            <div class="button-area">
                <input type="button" class="ButtonCancel" value="<?php echo __('Bỏ qua') ?>" onclick="btn_cancel_upload_onclick()"/>
            </div> 
        </div>
        <div id="tab_url">
            <h2 class="module_title"><?php echo __('Url ngoài') ?></h2>
            <div class="Row">
                <div class="left-Col"><?php echo __('Url từ trang khác: ');?></div>
                <div class="right-Col">
                    <input type="textbox" value="" name="txt_url" id="txt_url">
                </div>
            </div>
            <div class="button-area">
                <input type="button" class="ButtonAccept" value="<?php echo __('Cập nhật');?>" onclick="btn_accept_url_onclick();">
                <input type="button" class="ButtonCancel" value="<?php echo __('Bỏ qua') ?>" onclick="btn_cancel_upload_onclick()"/>
            </div> 
        </div>
        <div id="tab_article">
           <iframe id="frame_article" name="frame_article" src="" style="width: 100%;height: 100%;overflow: hidden;">
           </iframe>
        </div>
        <div id="tab_module">
            <div class="Row">
                <div class="left-Col">
                    <label><?php echo __('select module');?></label>
                </div>
                <div class="right-Col">
                    <select name="select_module" id="select_module">
                        <option value="public_service"><?php echo __('public service');?></option>
                        <option value="synthesis"><?php echo __('synthesis');?></option>
                        <!--<option value="weblink"><?php echo __('weblink');?></option>-->
                        <option value="sitemap"><?php echo __('has sitemap');?></option>
                        <option value="feedback"><?php echo __('feedback');?></option>
                        <option value="evaluation"><?php echo __('cadre evaluation');?></option>
                        <option value="guidance"><?php echo __('administrative guide');?></option>
                    </select>
                </div>
            </div>
             <div class="button-area">
                <input type="button" class="ButtonAccept" value="<?php echo __('Cập nhật');?>" onclick="btn_accept_module_onclick();">
                <input type="button" class="ButtonCancel" value="<?php echo __('Bỏ qua') ?>" onclick="btn_cancel_upload_onclick()"/>
            </div>
        </div>
    </div>
</form>
<script>
    $(document).ready(function(){
        $("#tabs_service_menu" ).tabs();
        var tab = '';
        <?php if($tab_select=='article'):?>
            tab = "#tab_<?php echo $tab_select;?>";
        $('#tabs_service_menu').tabs('select',tab);
        tab_article_onclick();
        <?php elseif($tab_select!=''):?>
            tab = "#tab_<?php echo $tab_select;?>";
        $('#tabs_service_menu').tabs('select',tab);
        <?php endif;?>
    });
    function btn_cancel_upload_onclick()
    {
        window.parent.hidePopWin();
    }
    function row_cat_onclick(row)
    {
        var arr_cat=new Array();
        arr_cat.push({'category_id':$(row).attr('data-cat_id'),
                      'category_name':$(row).attr('data-cat_name'),
                      'category_slug':$(row).attr('data-cat_slug'),
                      'service_type': 'category'
                     });
        returnVal = arr_cat;
        window.parent.hidePopWin(true);
    }
    function btn_accept_url_onclick()
    {
        var arr_url= new Array();
        arr_url.push({'url':$('#txt_url').val(),'service_type':'url'});
        returnVal = arr_url;
        window.parent.hidePopWin(true);
    }
    function btn_accept_module_onclick()
    {
        module_value=$('#tab_module select option:selected').val();
        module_name =$('#tab_module select option:selected').html();
        var arr_module= new Array();
        arr_module.push({'module_value':module_value,'module_name':module_name,'service_type':'module'});
        returnVal = arr_module;
        window.parent.hidePopWin(true);
    }
    function tab_article_onclick()
    {
        src='<?php echo $this->get_controller_url('article', 'admin').'dsp_all_article_svc/&parent_iframe_path=frame_article'; ?>';
        $('#frame_article').attr('src',src);
    }
</script>
<?php $this->template->display('dsp_footer' . $v_pop_win . '.php'); ?>