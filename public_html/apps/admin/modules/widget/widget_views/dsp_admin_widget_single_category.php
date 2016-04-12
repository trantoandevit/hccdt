<?php
defined('DS') or die('no direct access');
?>

<label>
    <?php echo __('category') ?></br>
    <select name="sel_category" style="width:100%">
        <option value="">-- <?php echo __('category')?> -- </option>
        <?php echo View::generate_select_option($arr_all_category,$category_id);?>
    </select>
</label>
<label>
    <?php echo __('limit') ?></br>
    <input type="textbox" value="<?php echo $limit?>" name="txt_limit" >
</label>