<?php
defined('DS') or die('no direct access');


?>
<?php if ($arr_all_widget_class = Session::get('arr_all_widget_class')): ?>
    <label>
        <?php echo __('color style') ?></br>
        <select name="sel_rating_color" style="width:100%;">
            <?php echo View::generate_select_option($arr_all_widget_class,$sel_rating_color) ?>
        </select>
    </label>
<?php endif; ?>
<label>
    <?php echo __('title') ?></br>
    <input type="textbox" value="<?php echo $txt_rating_title;?>" name="txt_rating_title" size="30"/>
</label>
<label>
    <?php echo __('Number of image news show') ?></br>
    <input type="textbox" value="<?php echo $txt_rating_quantity;?>" name="txt_rating_quantity" size="30"/>
</label>
