<?php
defined('DS') or die('no direct access');
?>
<label>
    <?php echo __('advertising name') ?></br>
    <select name="sel_widget_adv" style="width:100%;">
        <option value="0"></option>
        <?php echo View::generate_select_option($arr_all_adv, $selected_position); ?>
    </select>
</label>