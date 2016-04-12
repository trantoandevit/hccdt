<table width="100%" class="adminlist" cellspacing="0" border="1">
    <colgroup>
        <col width="5%" />
        <col width="85%" />
        <col width="10%" />
    </colgroup>
    <tr>
        <th><input type="checkbox" name="chk_check_all" onclick="toggle_check_all(this,this.form.chk);"/></th>
        <th><?php echo __('menu name'); ?></th>
        <th><?php echo __('order1'); ?></th>
    </tr>
    <?php
    $row = 0;
    $i   = 0
    ?>
    <?php
    for ($i = 0; $i < count($arr_all_menu); $i++):
        $v_menu_id        = $arr_all_menu[$i]['PK_MENU'];
        $v_name           = $arr_all_menu[$i]['C_NAME'];
        $v_order          = $arr_all_menu[$i]['C_ORDER'];
        $v_internal_order = $arr_all_menu[$i]['C_INTERNAL_ORDER'];
        $v_parent         = $arr_all_menu[$i]['FK_PARENT'];
        $v_level          = strlen($arr_all_menu[$i]['C_INTERNAL_ORDER']) / 3 - 1;
        $v_level_text     = '';
        for ($j = 0; $j < $v_level; $j++)
        {
            $v_level_text .= ' -- ';
        }

        $v_next_item = $v_menu_id;
        $v_prev_item = $v_menu_id;

        $j = $i - 1;
        while (isset($arr_all_menu[$j]))
        {
            if ($arr_all_menu[$j]['FK_PARENT'] == $v_parent)
            {
                $v_prev_item = $arr_all_menu[$j]['PK_MENU'];
                break;
            }
            else
            {
                $j--;
            }
        }

        $j = $i + 1;
        while (isset($arr_all_menu[$j]))
        {
            if ($arr_all_menu[$j]['FK_PARENT'] == $v_parent)
            {
                $v_next_item = $arr_all_menu[$j]['PK_MENU'];
                break;
            }
            else
            {
                $j++;
            }
        }
        ?>

        <tr class="row<?php echo $row; ?>">
            <td class="center">
                <input type="checkbox" name="chk"
                       value="<?php echo $v_menu_id; ?>" 
                       onclick="if (!this.checked) this.form.chk_check_all.checked=false;" 
                       />
            </td>
            <td>
                <a href="javascript:void(0)" onclick="row_onclick(<?php echo $v_menu_id; ?>)"><?php echo $v_level_text . $v_name; ?></a>
            </td>
            <td>
        <center>
            
                <?php if ($v_prev_item != $v_menu_id): ?>
                    <img 
                        width="16" height="16" src="<?php echo SITE_ROOT; ?>public/images/up.png"
                        onClick="swap_order_menu(<?php echo $v_menu_id ?>,<?php echo $v_prev_item; ?>);"
                        />
                    <?php endif; ?>
                    <?php if ($v_next_item != $v_menu_id): ?>
                    <img 
                        width="16" height="16" src="<?php echo SITE_ROOT; ?>public/images/down.png"
                        onClick="swap_order_menu(<?php echo $v_menu_id ?>,<?php echo $v_next_item; ?>);"
                        />
                    <?php endif; ?>
                
        </center>
    </td>    
    </tr>
    <?php
    $row = ($row == 1) ? 0 : 1;
    ?>
<?php endfor; ?>
<?php $n   = get_request_var('sel_rows_per_page', _CONST_DEFAULT_ROWS_PER_PAGE); ?>
<?php for ($i; $i < $n; $i++): ?>
    <tr class="row<?php echo $i % 2 ?>">
        <td></td>
        <td></td>
        <td></td>
    </tr>
<?php endfor; ?>
</table>

<div class="button-area">


        <input type="button" name="addnew" class="ButtonAdd" value="<?php echo __('add new'); ?>" onclick="btn_addnew_onclick();"/>
   
        <input type="button" name="trash" class="ButtonDelete" value="<?php echo __('delete'); ?>" onclick="btn_delete_onclick();"/>
   
</div>
