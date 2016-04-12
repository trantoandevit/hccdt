<?php
defined('DS') or die('no direct access');

?>
<label>
    <?php echo __('title') ?></br>
    <input style="width: 98%;" type="textbox" value="<?php echo $title?>" name="txt_title" />
</label>
<label>
    <?php echo __('online list type') ?></br>
    <select name="sel_widget_online_tv" style="width:100%;">
        <option value="0"></option>
        <?php foreach($arr_all_listtype as $value):
                $v_listtype_id   = $value['PK_LISTTYPE'];
                $v_listtype_name = $value['C_NAME'];
        ?>
        <option value="<?php echo $v_listtype_id?>" <?php echo ($v_listtype_id == $selected_listtype)?'selected':'';?>>
            <?php echo $v_listtype_name?>
        </option>
        <?php endforeach;?>
    </select>
</label>
<label>
    Đài phát thanh(radio): &nbsp; 
    <input type="checkbox" value="1" name="chk_radio" <?php echo ($chk_radio == '1')?'checked':'';?>> Có
</label>