<?php
/**
 */
?>
<?php $this->widget = isset($this->widget) ? $this->widget : ''; ?>
<div class="block" >
    <?php     
    //dsp_single_article and dsp_single_cateogry
    if($this->widget == 'article')
    {
            $v_category_reques_id = get_request_var('category_id', 0);
            //Chuyen muc noi bat
            $arr_all_cat_art = isset($arr_all_cat_art) ? $arr_all_cat_art : array();
    ?>
      <?php
        if ($arr_all_cat_art) {
            ?>
            <div>
                <ul class="widget cp-menu-side-bar">
                    <div class="widget-header"><h6><?php echo __('featured category') ?></h6></div>
                    <?php for ($i = 0; $i < sizeof($arr_all_cat_art); $i++) { ?>
                        <?php
                        $v_cat_id = $arr_all_cat_art[$i]['PK_CATEGORY'];
                        $v_cat_name = $arr_all_cat_art[$i]['C_NAME'];
                        $v_cat_slug = $arr_all_cat_art[$i]['C_SLUG'];
                        $v_website_id = $arr_all_cat_art[$i]['FK_WEBSITE'];
                        $v_cat_url = build_url_category($v_cat_slug, $v_website_id, $v_cat_id);
                        $v_div_active = ($v_category_reques_id == $v_cat_id) ? 'active' : 'bg-none';
                        ?>
                        <li class="bg-none <?php echo $v_div_active ?>">
                            <a href="<?php echo $v_cat_url; ?>"  title="<?php echo $v_cat_name ?>">
                                <label style="margin-left: 15px" ><?php echo $v_cat_name; ?></label>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>
        
    <?php } ?>
        
    <?php
        // Hiên thị danh sách loại câu hoi
        // citizens_question : dsp_all_cq, dsp_single_cq : Hiện tại không cho hiển thị
        if ($this->widget == 'citizens_question'):
    ?>
<!--        <div>
            <?php if (sizeof($arr_all_cq_field) > 0): ?>
                  <ul class="widget cp-menu-side-bar">
                    <div class="widget-header"><h6><?php echo __('Loại câu hỏi') ?></h6></div>
                        <?php
                        $v_field_rq_id = get_request_var('field_id',0);
                        foreach ($arr_all_cq_field as $single_cq_field) {
                            $v_field_id = $single_cq_field['PK_FIELD'];
                            $v_field_name = $single_cq_field['C_NAME'];
                            $v_website_id = $single_cq_field['FK_WEBSITE'];
                            $v_field_url = build_url_cq_field($v_website_id, $v_field_id);
                            $v_active = (intval($v_field_rq_id) == intval($v_field_id)) ? 'active' : 'spec';

                            echo "<li class='bg-none  $v_active'><a href='$v_field_url'><label style='margin-left:15px'>$v_field_name</label></a></li>";
                        }
                        ?>
                </ul>
            <?php endif; ?>
        </div>-->
    <?php endif; ?>
        
    <?php $m = isset($arr_all_widget_position['widget_left']) ? count($arr_all_widget_position['widget_left']) : 0; ?>
    <?php for ($i = 0; $i < $m; $i++): ?>
        <?php echo $arr_all_widget_position['widget_left'][$i]['C_CONTENT'] ?>
    <?php endfor; ?>
</div>

