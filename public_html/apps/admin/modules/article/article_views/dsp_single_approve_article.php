<?php
defined('DS') or die('no direct access');
lang::load_lang('lang_vi');
$v_media_file_name = $arr_single_article['C_FILE_NAME'];
if (file_exists(SERVER_ROOT . 'upload' . DS . $v_media_file_name) && !is_dir(SERVER_ROOT . 'upload' . DS . $v_media_file_name))
{
    $v_img = SITE_ROOT . 'upload/' . $v_media_file_name;
}
else
{
    $v_img                     = SITE_ROOT . 'public/images/langson.png';
}
Session::set('VIDEO_THUMBNAIL', $v_img);

$arr_single_article['C_SUMMARY'] = htmlspecialchars_decode($arr_single_article['C_SUMMARY']);

$arr_single_article['C_CONTENT'] = htmlspecialchars_decode($arr_single_article['C_CONTENT']);


//bien tap
$arr_status = array(
    0         => __('draft'),
    3         => __('published')
);
$arr_role = Session::get('arr_all_grant_group_code');
if (!in_array('ADMINISTRATORS', $arr_role) && !in_array('TONG_BIEN_TAP', $arr_role))
{
    unset($arr_status[3]);
    if (!in_array('BIEN_TAP_VIEN', $arr_role))
    {
        unset($arr_status[2]);
    }
}

$v_msg              = isset($arr_single_article['C_MESSAGE']) ? $arr_single_article['C_MESSAGE'] : '';
$v_begin_date       = new DateTime($arr_single_article['C_BEGIN_DATE']);
$v_begin_time       = $v_begin_date->format('H:i');
$v_begin_date       = $v_begin_date->format('d/m/Y');
$v_pen_name         = isset($arr_single_article['C_PEN_NAME']) ? $arr_single_article['C_PEN_NAME'] : '';
$v_disable_pen_name = $arr_single_article['FK_INIT_USER'] == Session::get('user_id') ? '' : 'disabled';

$v_end_date = new DateTime($arr_single_article['C_END_DATE']);
$v_end_time = $v_end_date->format('H:i');
$v_end_date = $v_end_date->format('d/m/Y');

//xem co hien begin end date ko
$show_date         = true;
$arr_required_role = array('ADMINISTRATORS', 'TONG_BIEN_TAP', 'BIEN_TAP_VIEN');
$arr_user_role = Session::get('arr_all_grant_group_code');
$arr_intersect = array_intersect($arr_required_role, $arr_user_role);

if (empty($arr_intersect))
{
    $show_date = false;
}

//search
$v_search = get_post_var('hdn_search','');
$arp_approve_method = get_post_var('hdn_arp_approve_artilce','');

//include header
$this->template->title = __('single approve article');
$this->template->display('dsp_header_mobile.php');
?>
<style>.video_container{height: 400px;}</style>

<div class="article-view">
    <h3><?php echo $arr_single_article['C_TITLE'] ?></h3>
    <h4>(<?php echo $arr_single_article['C_SUB_TITLE'] ?>)</h4>
    <div class="summary-container">
        <b><?php echo $arr_single_article['C_SUMMARY']; ?></b>
    </div>
    <?php error_reporting(E_ALL) ?>
    <?php $pattern = "/\[VIDEO\](.*)\[\/VIDEO\]/i"; ?>
    <?php echo preg_replace_callback($pattern, 'replace_video', $arr_single_article['C_CONTENT'], -1, $count) ?>
    <div>
        <div style="float:right"><b><?php echo $arr_single_article['C_PEN_NAME']; ?></b></div>
    </div>

</div>
<center><div style="width: 80%;border-bottom: 1px black solid"></div></center>
<div id="div_approve" style="margin: 8px;">
    <form name="frmMain" id="frmMain" action="<?php echo $this->get_controller_url() . 'update_edit_article/' ?>" method="POST" style="width:100%">
        <?php
            echo $this->hidden('controller', $this->get_controller_url());
            echo $this->hidden('hdn_item_id', '0');
            echo $this->hidden('hdn_item_id_list', '');

            echo $this->hidden('hdn_dsp_single_method', 'dsp_single_approve_article');
            echo $this->hidden('hdn_dsp_all_method', 'dsp_approve_article');
            echo $this->hidden('hdn_update_method', 'update_edited_article');
            echo $this->hidden('hdn_search', $v_search);
            echo $this->hidden('hdn_arp_approve_artilce', $arp_approve_method);
        ?>
         <div class="div-table" style="width: 100%">
            <div class="div-table-cell" style="width: 70%">

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
                <div class="Row">
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
                <div class="Row">
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
                <?php if ($show_date): ?>
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
                <?php endif; //xem co hien beginend ko ?>
            </div>
            <div class="div-table-cell" style="width: 30%">
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
        </div>
        <div class="clear"></div>
        <div class="button-area">
            <input type="button" class="ButtonAccept" onClick="btn_update_edit_onclick();" value="<?php echo __('update') ?>"/>
            <input type="button" class="ButtonBack" onClick="btn_back_onclick();" value="<?php echo __('goback to list') ?>"/>
        </div>
    </form>
</div>
<script>
    window.btn_update_edit_onclick = function(){
        var f = document.frmMain;
        m = '<?php echo $this->get_controller_url() ?>' + 'update_edited_article/1';
        var xObj = new DynamicFormHelper('','',f); 
        if (xObj.ValidateForm(f)){
            $('#msg-box').show();
            //f.XmlData.value = xObj.GetXmlData();
            $.ajax({
                type: 'post',
                url: m,
                data: $(f).serialize(),
                success: function(json){
                    m = $('#controller').val() + $('#hdn_dsp_all_method').val();
                    $('#frmMain').attr('action', m);
                    $('#frmMain').submit();
                }
            });
        }
    }
</script>
<?php $this->template->display('dsp_footer_mobile.php'); ?>