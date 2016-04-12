<?php
defined('DS') or die('no direct access');
$title_weblink = isset($title_weblink) ? $title_weblink : '';
?>
<label>
    <input <?php echo ($title_weblink == 1) ? ' checked ' : ''?>  type="checkbox" name="chk_show_title" id="chk_show_title" > Hiển thị tiêu đề
</label>

<?php if ($arr_all_widget_class = Session::get('arr_all_widget_class')): ?>
    <label>
        <?php echo __('color style') ?></br>
        <select name="sel_widget_style" style="width:100%;">
            <?php echo View::generate_select_option($arr_all_widget_class, $widget_style) ?>
        </select>
    </label>    
<?php endif; ?>

 <label>
    <?php echo __('weblink group') ?></br>
    <select name="sel_group_web_link" style="width:100%;">
        <?php
            for($i =0;$i <count($arr_group_weblink);$i++)
            {
                $v_group_weblink_id     = isset($arr_group_weblink[$i]['PK_LIST']) ? $arr_group_weblink[$i]['PK_LIST'] : '';
                $v_group_weblink_name   = isset($arr_group_weblink[$i]['C_NAME']) ? $arr_group_weblink[$i]['C_NAME'] : '';
                $selected = ($group_weblink_id == $v_group_weblink_id) ?  'selected' : '';
                echo "<option $selected value='$v_group_weblink_id'>$v_group_weblink_name</option>";
            }
        ?>
    </select>
</label>    