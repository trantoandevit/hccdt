<?php
defined('DS') or die('no direct access');
?>
<label>
    <?php echo __('category name') ?></br>
    <select name="sel_widget_category_slide" id="sel_widget_category_slide">
            <option value="0"> -- <?php echo __('choose category') ?> -- </option>
            <?php foreach ($arr_all_category as $item): ?>
                <?php
                $v_level  = strlen($item['C_INTERNAL_ORDER']) / 3 - 1;
                $v_indent = '';
                for ($i = 0; $i < $v_level; $i++)
                {
                    $v_indent .= ' -- ';
                }
                ?>
                <option value="<?php echo $item['PK_CATEGORY'] ?>" data-slug="<?php echo $item['C_SLUG'] ?>" <?php echo ($item['PK_CATEGORY'] == $sel_widget_category_slide)?'selected':'';?> >
                    <?php echo $v_indent . $item['C_NAME'] ?>
                </option>
            <?php endforeach; ?>
            <script>$('#sel_category').val(<?php echo get_request_var('hdn_category') ?>);</script>
    </select>
</label>
<label>
        <?php echo __('some news show');?></br>
        <input type="textbox" name="txt_some_news_show" id="txt_some_news_show" value="<?php echo isset($txt_some_news_show)?$txt_some_news_show:'15';?>" />
</label>