<?php
defined('SERVER_ROOT') or die('No direct access');
$v_id       = isset($arr_single_category['PK_CATEGORY']) ? $arr_single_category['PK_CATEGORY'] : 0;
$v_name     = isset($arr_single_category['C_NAME']) ? $arr_single_category['C_NAME'] : '';
$v_parent   = isset($arr_single_category['FK_PARENT']) ? $arr_single_category['FK_PARENT'] : '';
$v_slug     = isset($arr_single_category['C_SLUG']) ? $arr_single_category['C_SLUG'] : '';
$v_status   = isset($arr_single_category['C_STATUS']) ? $arr_single_category['C_STATUS'] : 0;
$v_order    = isset($arr_single_category['C_ORDER']) ? $arr_single_category['C_ORDER'] : 1;
$v_is_video = isset($arr_single_category['C_IS_VIDEO']) ? $arr_single_category['C_IS_VIDEO'] : '';
//$v_news_photos = isset($arr_single_category['C_NEWS_PHOTOS']) ? $arr_single_category['C_NEWS_PHOTOS'] : '';
if (intval($v_is_video))
{
    $v_is_video = 'checked';
}
else
{
    $v_is_video = '';
}
//if(intval($v_news_photos))
//{
//    $v_news_photos = 'checked';
//}
//else
//{
//    $v_news_photos ='';
//}
$this->template->title = __('category');
$this->template->display('dsp_header.php');
?>
<form name="frmMain" id="frmMain" method="post" action="<?php echo $this->get_controller_url() . 'update_insert_category'; ?>">
    <?php
    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_category');
    echo $this->hidden('controller', $this->get_controller_url());
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_category');
    echo $this->hidden('hdn_update_method', 'update_category');
    echo $this->hidden('hdn_delete_method', 'delete_category');
    echo $this->hidden('hdn_item_id', $v_id);
    echo $this->hidden('XmlData', '');
    ?>
    <h2 class="module_title">
        <?php echo __('category'); ?>
    </h2>
    <div class="Row">
        <div class="left-Col">
            <?php echo __('category name'); ?> <label class="required">(*)</label>
        </div>
        <div class="right-Col">
            <input type="text" name="txt_name" id="txt_name" value="<?php echo $v_name; ?>" 
                   onKeyUp="auto_slug(this, '#txt_slug');"
                   class="inputbox" maxlength="255" style="width:50%" 
                   data-allownull="no" data-validate="text" data-name="<?php echo __('name'); ?>" 
                   data-xml="no" data-doc="no" autofocus="autofocus"
                   />
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">
            <?php echo __('slug'); ?> <label class="required">(*)</label>
        </div>
        <div class="right-Col">
            <input type="text" name="txt_slug" id="txt_slug" value="<?php echo $v_slug; ?>" 
                   class="inputbox" maxlength="255" style="width:50%" 
                   data-allownull="no" data-validate="text" data-name="<?php echo __('slug'); ?>" 
                   data-xml="no" data-doc="no" 
                   />
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">
            <?php echo __('parent category'); ?>
            <label class="required">(*)</label>
        </div>
        <div class="right-Col">
            <select 
                name="sel_category" id="sel_category"
                data-validate="number" data-name="<?php echo __('parent category'); ?>"
                >
                <option value="0"> << <?php echo __('root category') ?> >></option>
                <?php foreach ($arr_all_category as $item): ?>
                    <?php
                    $indent     = strlen($item['C_INTERNAL_ORDER']) / 3 - 1;
                    $index_text = '';
                    for ($i = 0; $i < $indent; $i++)
                    {
                        $index_text .= ' -- ';
                    }
                    ?>
                    <option value="<?php echo $item['PK_CATEGORY']; ?>">
                        <?php echo $index_text . $item['C_NAME'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <script>$('select[name="sel_category"] option[value=<?php echo $v_parent ?>]').attr('selected',1);</script>
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">
            <?php echo __('order') ?> <label class="required">(*)</label>
        </div>
        <div class="right-Col">
            <input type="text" name="txt_order" id="txt_order" value="<?php echo $v_order; ?>" 
                   class="inputbox" maxlength="255" style="width:15%" 
                   data-allownull="yes" data-validate="unsignNumber"  data-name="<?php echo __('order'); ?>" 
                   data-xml="no" data-doc="no" 
                   />
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">
            &nbsp;
        </div>
        <div class="right-Col">
            <label>
                <input type="checkbox" name="sel_status" id="sel_status" <?php echo ($v_status == 1)?'checked':'';?> />
                <?php echo __('active status')?>
            </label>
        </div>
    </div>
    <div class="Row">
        <div class="left-Col" >
            &nbsp;
        </div>
        <div class="right-Col">
            <label>
                <input type="checkbox" name="chk_is_video" id="chk_is_video" value="1" <?php echo $v_is_video; ?>/>
                <?php echo __('is video category'); ?>
            </label>
        </div>
    </div>
    <div class="button-area">
        <input type="submit" class="ButtonAccept" value="<?php echo __('update'); ?>" onClick="btn_update_onclick();"/>
        <input 
            type="button" class="ButtonBack" value="<?php echo __('goback'); ?>" 
            onClick="window.location = '<?php echo $this->get_controller_url(); ?>';"
            />
    </div>
</form>

<?php $this->template->display('dsp_footer.php') ?>
