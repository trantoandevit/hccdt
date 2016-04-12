<?php
defined('DS') or die('no direct access');
$arr_status = array(
    0         => __('draft'),
    3         => __('published')
);


$v_msg              = isset($arr_single_article['C_MESSAGE']) ? $arr_single_article['C_MESSAGE'] : '';
$v_author           = $arr_single_article['C_INIT_USER_NAME'];
$v_begin_date       = new DateTime($arr_single_article['C_BEGIN_DATE']);
$v_begin_time       = $v_begin_date->format('H:i');
$v_begin_date       = $v_begin_date->format('d/m/Y');
$v_pen_name         = isset($arr_single_article['C_PEN_NAME']) ? $arr_single_article['C_PEN_NAME'] : '';
$v_disable_pen_name = $arr_single_article['FK_INIT_USER'] == Session::get('user_id') ? '' : 'disabled';

$v_end_date = new DateTime($arr_single_article['C_END_DATE']);
$v_end_time = $v_end_date->format('H:i');
$v_end_date = $v_end_date->format('d/m/Y');


?>
<form name="frm_edit" id="frm_edit" action="<?php echo $this->get_controller_url() . 'update_edit_article/' ?>" style="width:966px;">
    <div class="grid_16">

        <?php echo $this->hidden('hdn_item_id', $v_id); ?>
        <div class="Row">
            <div class="left-Col">
                <?php echo __('status') ?> <span class="required">(*)</span>
            </div>
            <div class="right-Col">
                <select name="sel_status" id="sel_status">
                    <?php echo View::generate_select_option($arr_status, $arr_single_article['C_STATUS']); ?>
                </select>
            </div>
        </div>
        <div class="Row" style="display: none">
            <div class="left-Col">
                <?php echo __('message') ?>
            </div>
            <div class="right-Col">
                <input type="text" name="txt_msg" value="<?php echo $v_msg; ?>" id="txt_msg"
                       class="inputbox" maxlength="500" size="50"
                       data-allownull="yes" data-validate="text"
                       data-name="<?php echo __('title'); ?>"
                       data-xml="no" data-doc="no"
                       />
            </div>
        </div>
        <div class="Row"  style="display: none">
            <div class="left-Col">
                <?php echo __('pen name') ?>
            </div>
            <div class="right-Col">
                <input type="text" name="txt_pen_name" value="<?php echo $v_pen_name; ?>" id="txt_pen_name"
                       class="inputbox" maxlength="500" size="50"
                       data-allownull="yes" data-validate="text"
                       data-name="<?php echo __('pen name'); ?>"
                       data-xml="no" data-doc="no" <?php echo $v_disable_pen_name ?>
                       />
            </div>
        </div>
            <div class="Row">
                <div class="left-Col">
                    <?php echo __('begin date') ?>
                </div>
                <div class="right-Col">
                    <input type="text" name="txt_begin_date" value="<?php echo $v_begin_date; ?>" id="txt_begin_date"
                           class="inputbox" maxlength="500" size="20"
                           data-allownull="no" data-validate="date"
                           data-name="<?php echo __('begin date'); ?>"
                           data-xml="no" data-doc="no" onClick="DoCal('txt_begin_date')"
                           />
                    <img 
                        src="<?php echo SITE_ROOT ?>public/images/calendar.png"
                        onClick="DoCal('txt_begin_date')"
                        />
                    &nbsp;
                    <?php echo __('at') ?>
                    <input type="text" name="txt_begin_time" value="<?php echo $v_begin_time; ?>" id="txt_begin_time"
                           class="inputbox" maxlength="500" size="20"
                           data-allownull="yes" data-validate="text"
                           data-name="<?php echo __('begin time'); ?>"
                           data-xml="no" data-doc="no"
                           />
                    &nbsp;
                    hh:mm

                </div>
            </div>
            <div class="Row">
                <div class="left-Col">
                    <?php echo __('end date') ?>
                </div>
                <div class="right-Col">
                    <input type="text" name="txt_end_date" value="<?php echo $v_end_date; ?>" id="txt_end_date"
                           class="inputbox" maxlength="500" size="20"
                           data-allownull="no" data-validate="date"
                           data-name="<?php echo __('end date'); ?>"
                           data-xml="no" data-doc="no" onClick="DoCal('txt_end_date')"
                           />
                    <img 
                        src="<?php echo SITE_ROOT ?>public/images/calendar.png"
                        onClick="DoCal('txt_end_date')"
                        />
                    &nbsp;
                    <?php echo __('at') ?>
                    <input type="text" name="txt_end_time" value="<?php echo $v_end_time; ?>" id="txt_end_time"
                           class="inputbox" maxlength="500" size="20"
                           data-allownull="yes" data-validate="text"
                           data-name="<?php echo __('end time'); ?>"
                           data-xml="no" data-doc="no"
                           />
                    &nbsp;
                    hh:mm

                </div>
            </div>
    </div>
    <div class="grid_7">
        <div style="padding-left:10px;">
            <div class="ui-widget" id="category-widget">
                <div class="ui-widget-header ui-state-default ui-corner-top">
                    <h4><?php echo __('sticky of category') ?></h4>
                </div>
                <div class="ui-widget-content" style="height:150px;overflow-y: scroll;" id="category-content">                 
                    <?php foreach ($arr_all_category as $category): ?>
                        <?php if (in_array($category['PK_CATEGORY'], $arr_category_article)): ?>
                            <?php
                            $checked = in_array($category['PK_CATEGORY'], $arr_sticky_category) ? 'checked' : '';
                            ?>
                            <input 
                                type="checkbox" 
                                name="chk_category[]"
                                id="chk_category_<?php echo $category['PK_CATEGORY'] ?>"
                                value="<?php echo $category['PK_CATEGORY'] ?>"
                                <?php echo $checked ?>
                                />
                            <label for="chk_category_<?php echo $category['PK_CATEGORY'] ?>">
                                <?php echo $category['C_NAME'] ?>
                            </label>
                            <br/>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="clear"></div>
    <div class="button-area">
        <input type="button" class="ButtonAccept" onClick="btn_update_edit_onclick();" value="<?php echo __('update') ?>"/>
        <input type="button" class="ButtonBack" onClick="btn_back_onclick();" value="<?php echo __('goback to list') ?>"/>
    </div>
</form>

<script>
    window.btn_update_edit_onclick = function(){
        var f = document.frm_edit;
        m = '<?php echo $this->get_controller_url() ?>' + 'update_edited_article/';
        var xObj = new DynamicFormHelper('','',f); 
        if (xObj.ValidateForm(f)){
            $('#msg-box').show();
            //f.XmlData.value = xObj.GetXmlData();
            $.ajax({
                type: 'post',
                url: m,
                data: $(f).serialize(),
                success: function(json){
                    $('#frm_filter').attr('action', "<?php echo $this->get_controller_url(); ?>");
                    $('#frm_filter').submit();
                }
            });
        }
    }
</script>