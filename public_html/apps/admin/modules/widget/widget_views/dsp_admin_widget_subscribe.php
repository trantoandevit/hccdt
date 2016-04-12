<?php
defined('DS') or die('no direct access');
?>
<?php if ($arr_all_widget_class = Session::get('arr_all_widget_class')): ?>
    <label>
        <?php echo __('color style') ?></br>
        <select name="widget_style" style="width:100%;">
            <?php echo View::generate_select_option($arr_all_widget_class, $widget_style) ?>
        </select>
    </label>
<?php endif; ?>
