<?php
defined('SERVER_ROOT') or die('no direct access');
?>
<?php

function show_insert_delete_button()
{
    $html = '<div class="button-area">';
    
        $html .= '<input type="button" class="ButtonAdd" onClick="dsp_modal();" value="' . __('add new') . '"></input>';
   
        $html .= '<input type="button" class="ButtonDelete" onClick="delete_multi_category();" value="' . __('delete') . '"></input>';
  
    if (get_system_config_value(CFGKEY_CACHE) == 'true')
    {
        $html .= '<input type="button" class="button ButtonWriteHtmlCache" onClick="btn_cache_onclick();" value="' . __('save cache') . '"></input>';
    }

    $html .= '</div>';

    echo $html;
}
?>
<form name="frmMain" id="frmMain" method="post">
    <?php show_insert_delete_button() ?>
    <?php echo $this->hidden('hdn_controller', $this->get_controller_url()) ?>
    <?php echo $this->hidden('hdn_active_tab', 1); ?>
    <?php echo $this->user_token(); ?>
    <div>
        <table width="100%" class="adminlist" cellspacing="0" border="1" >
            <colgroup>
                <col width="10%" />
                <col width="75%" />
                <col width="15%" />
            </colgroup>
            <tr>
                <th>
                    <input type="checkbox" id="chk-all"/>
                </th>
                <th><?php echo __('category'); ?></th>
                <th><?php echo __('order') ?></th>
            </tr>
            <?php
            $n = count($arr_all_featured);
            ?>
            <?php for ($i = 0; $i < $n; $i++): ?>
                <?php
                $item        = $arr_all_featured[$i];
                $v_id        = $item['PK_HOMEPAGE_CATEGORY'];
                $v_name      = $item['C_NAME'];
                $v_disable   = $item['C_STATUS'] == 0 ? 'line-through' : '';
                $v_cat_id    = $item['PK_CATEGORY'];
                $v_prev_item = isset($arr_all_featured[$i - 1]) ? $arr_all_featured[$i - 1]['PK_HOMEPAGE_CATEGORY'] : false;
                $v_next_item = isset($arr_all_featured[$i + 1]) ? $arr_all_featured[$i + 1]['PK_HOMEPAGE_CATEGORY'] : false;
                $v_website_id= intval(Session::get('session_website_id'));
                ?>
                <tr class="row<?php echo $i % 2 ?>">
                    <td class="Center">
                        <input
                            type="checkbox" name="chk-item[]" class="chk-item"
                            value="<?php echo $v_id; ?>"
                            data-cat-id="<?php echo $v_cat_id ?>"
                            data-website-id="<?php echo $v_website_id ?>"
                            id="item-<?php echo $v_id ?>"
                            />
                    </td>
                    <td>
                        <label class="<?php echo $v_disable ?>" for="item-<?php echo $v_id ?>"><?php echo $v_name; ?></label>
                    </td>
                    <td class="Center">
                       
                            <?php if ($v_prev_item): ?>
                                <img 
                                    height="16" width="16"
                                    src="<?php echo SITE_ROOT ?>public/images/up.png"
                                    onClick="swap_order(<?php echo $v_id ?>,<?php echo $v_prev_item ?>);"
                                    />
                                <?php endif; ?>
                                <?php if ($v_next_item): ?>
                                <img 
                                    height="16" width="16"
                                    src="<?php echo SITE_ROOT ?>public/images/down.png"
                                    onClick="swap_order(<?php echo $v_id ?>,<?php echo $v_next_item ?>);"
                                    />
                                <?php endif; ?>
                    </td>
                </tr>
            <?php endfor; ?>
        </table>
    </div>
    
    <?php show_insert_delete_button() ?>
</form>
<script>
    toggle_checkbox('#chk-all', '.chk-item');
    
    function dsp_modal()
    {
        var url = "<?php echo $this->get_controller_url() ?>dsp_all_category_svc/" 
            + <?php echo intval(Session::get('session_website_id')) ?>;
        
        var v_inserted = '0';
        
        $('.chk-item').each(function(){
            v_inserted += ', ' + $(this).attr('data-cat-id');
        });
        window.showPopWin(url, 800, 600, function(obj){
            if(obj.length == 0)
                return;
            $.ajax({
                type: 'post',
                url: '<?php echo $this->get_controller_url() ?>insert_featured_category/',
                data: {
                    'category': obj
                    , 'website-id': <?php echo $website_id; ?>
                    , 'inserted-category': v_inserted
                    , 'goback': "<?php echo $this->get_controller_url() . 'dsp_all_featured/' ?>"
                },
                success: function(){
                    $('#tabs').tabs('load', 1);
                }
            });
        });
    }
    
    function delete_multi_category()
    {
        if($('.chk-item:checked').length == 0)
        {
            alert("<?php echo __('you must choose atleast one object') ?>");
            return;
        }
        
        if(! confirm("<?php echo __('are you sure to delete all selected object?') ?>"))
        {
            return;
        }
        $.ajax({
            type: 'post',
            url: '<?php echo $this->get_controller_url() . 'delete_featured_category/'; ?>',
            data: $('#frmMain').serialize(),
            success: function(){
                reload_current_tab();
            }
        });
    }
    
    function swap_order($item1, $item2)
    {
        $.ajax({
            type: 'post',
            url: '<?php echo $this->get_controller_url() . 'swap_featured_order/' ?>',
            data: {'item1': $item1, 'item2': $item2},
            success: function()
            {
                reload_current_tab();
            }
        });
    }
</script>