<?php
defined('DS') or die('no direct access');
?>
<?php if ($arr_all_widget_class = Session::get('arr_all_widget_class')): ?>
    <label>
        <?php echo __('color style') ?></br>
        <select name="sel_widget_style" style="width:100%;" <?php echo $disabled ?>>
            <?php echo View::generate_select_option($arr_all_widget_class, $widget_style) ?>
        </select>
    </label>
<?php endif; ?>
<label>
    <?php echo __('quantity') ?></br>
    <input type="text" name="txt_widget_most_visited_quantity" style="width: 100%" value="<?php echo $quantity ?>"/>
</label>