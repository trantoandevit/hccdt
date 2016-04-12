<?php
defined('SERVER_ROOT') or die('No direct script');

function show_insert_delete_button()
{
    $html = '<div class="button-area">';
    
        $html .= '<input type="button" class="ButtonAdd" onClick="dsp_single_category(0);" value="' . __('add new') . '"></input>';
   
        $html .= '<input type="button" class="ButtonDelete" onClick="delete_multi_category();" value="' . __('delete') . '"></input>';
   
    if (get_system_config_value(CFGKEY_CACHE) == 'true')
    {
        $html .= '<input type="button" class="button ButtonWriteHtmlCache" onClick="btn_cache_onclick();" value="' . __('save cache') . '"></input>';
    }
    $html .= '</div>';

    echo $html;
}
?>
<?php show_insert_delete_button(); ?>

<form name="frmMain" id="frmMain" method="post">
    <?php echo $this->hidden('hdn_controller', $this->get_controller_url()) ?>
    <?php echo $this->hidden('hdn_active_tab', 0); ?>
    <?php echo $this->user_token(); ?>
    <div>
        <table width="100%" class="adminlist" cellspacing="0" border="1" >
            <colgroup>
                <col width="10%" />
                <col width="60%" />
                <col width="15%" />
                <col width="15%" />
            </colgroup>
            <tr>
                <th><input type="checkbox" id="chk-all"/></th>
                <th><?php echo __('category'); ?></th>
                <th><?php echo __('order'); ?></th>
                <th><?php echo __('status'); ?></th>
            </tr>
            <?php $n = count($arr_all_category); ?>
            <?php for ($i = 0; $i < $n; $i++): ?>
                <?php
                $item             = $arr_all_category[$i];
                $v_category_id    = $item['PK_CATEGORY'];
                $v_name           = $item['C_NAME'];
                $v_status         = $item['C_STATUS'];
                $v_order          = $item['C_ORDER'];
                $v_internal_order = $item['C_INTERNAL_ORDER'];
                $v_parent         = $item['FK_PARENT'];
                $v_indent         = strlen($item['C_INTERNAL_ORDER']) / 3 - 1;
                $v_indent_text    = '';
                $v_disable        = ($item['C_COUNT_CHILD_CAT'] > 0) ? 'disabled' : '';
                $v_disable        = ($item['C_COUNT_CHILD_ART'] > 0 ) ? 'disabled' : $v_disable;
                $line_throught    = $v_status == 0 ? 'line-through' : '';

                for ($j = 0; $j < $v_indent; $j++)
                {
                    $v_indent_text .= ' -- ';
                }

                $v_next_item = $v_category_id;
                $v_prev_item = $v_category_id;

                $j = $i - 1;
                while (isset($arr_all_category[$j]))
                {
                    if ($arr_all_category[$j]['FK_PARENT'] == $v_parent)
                    {
                        $v_prev_item = $arr_all_category[$j]['PK_CATEGORY'];
                        break;
                    }
                    else
                    {
                        $j--;
                    }
                }

                $j = $i + 1;
                while (isset($arr_all_category[$j]))
                {
                    if ($arr_all_category[$j]['FK_PARENT'] == $v_parent)
                    {
                        $v_next_item = $arr_all_category[$j]['PK_CATEGORY'];
                        break;
                    }
                    else
                    {
                        $j++;
                    }
                }
                ?>
                <tr class="row<?php echo $i % 2; ?>">
                    <td class="Center">
                        <input 
                            type="checkbox" class="chk-item" name="chk-item[]" 
                            value="<?php echo $v_category_id; ?>" <?php echo $v_disable ?>
                            />
                    </td>
                    <td 
                        class="<?php echo $line_throught ?>"
                        style="cursor:pointer;"
                       
                            onClick="dsp_single_category(<?php echo $v_category_id; ?>);"
                      
                        >
                            <?php echo $v_indent_text . $v_name; ?>
                    </td>
                    <td class="Center">
                      
                            <?php if ($v_prev_item != $v_category_id): ?>
                                <img 
                                    width="16" height="16" src="<?php echo SITE_ROOT; ?>public/images/up.png"
                                    onClick="swap_order(<?php echo $v_prev_item ?>,<?php echo $v_category_id; ?>);"
                                    />
                                <?php endif; ?>
                                <?php if ($v_next_item != $v_category_id): ?>
                                <img 
                                    width="16" height="16" src="<?php echo SITE_ROOT; ?>public/images/down.png"
                                    onClick="swap_order(<?php echo $v_next_item ?>,<?php echo $v_category_id; ?>);"
                                    />
                                <?php endif; ?>
                           
                    </td>
                    <td class="Center">
                        <?php echo $v_status ? __('active status') : __('inactive status'); ?>
                    </td>
                </tr>
            <?php endfor; ?>
        </table>
    </div>
    <?php show_insert_delete_button(); ?>
</form>

<script>
    $(document).ready(function(){
        toggle_checkbox('#chk-all', '.chk-item');        
        var has_edit_right = 1;
    });
    function swap_order(p_item1, p_item2)
    {
        $.ajax({
            type: 'post',
            url: '<?php echo $this->get_controller_url() . 'swap_category_order'; ?>',
            data: {item1: p_item1, item2: p_item2},
            success: function(){ 
                reload_current_tab();
            }
        })
    };
    
    function dsp_single_category(id)
    {
        window.location = "<?php echo $this->get_controller_url() . 'dsp_single_category/' ?>" + id;
    }
    
    function delete_multi_category()
    {
        if($('.chk-item:checked').length == 0)
        {
            alert("<?php echo __('you must choose atleast one object') ?>");
            return;
        }
        
        if(confirm('<?php echo __('are you sure to delete all selected object?') ?>'))
        {
            var $url = "<?php echo $this->get_controller_url() . 'delete_category' ?>";
            $.ajax({
                type: 'post',
                url: $url,
                data: $('#frmMain').serialize(),
                success: function(){
                    reload_current_tab();
                }
            });
        }
        
    }

</script>