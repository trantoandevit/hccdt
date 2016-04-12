<?php
defined('DS') or die('no direct access');
?>

<form name="frm_filter_<?php echo $v_default ?>" id="frm_filter_<?php echo $v_default ?>" action="" method="post">
    <?php if ($v_default == 0): ?>
        <select name="sel_category" id="sel_category" onChange="sel_category_onchange();">
            <?php if (!empty($arr_all_category)): ?>
                <?php foreach ($arr_all_category as $item): ?>
                    <?php
                    $v_level  = strlen($item['C_INTERNAL_ORDER']) / 3 - 1;
                    $v_indent = '';
                    for ($i = 0; $i < $v_level; $i++)
                    {
                        $v_indent .= ' -- ';
                    }
                    ?>
                    <option value="<?php echo $item['PK_CATEGORY'] ?>">
                        <?php echo $v_indent . $item['C_NAME'] ?>
                    </option>
                <?php endforeach; ?>
                <script>
                    $('#sel_category').val(<?php echo get_post_var('sel_category', $arr_all_category[0]['PK_CATEGORY']); ?>);
                </script>
            <?php endif; ?>
        </select>
    <?php endif; ?>
</form>
<div class="button-area">
    <?php //if (Session::check_permission('THEM_MOI_NOI_BAT')): ?>
        <input 
            type="button" class="ButtonAdd" 
            onClick="add_sticky();" value="<?php echo __('add new') ?>"
            />
        <?php //endif; ?>
        <?php //if (Session::check_permission('XOA_NOI_BAT')): ?>
        <input 
            type="button" class="ButtonDelete" 
            onClick="delete_sticky();" value="<?php echo __('delete') ?>"
            />
        <?php //endif; ?>
        <?php //if (get_system_config_value(CFGKEY_CACHE) == 'true'): ?>
        <input 
            type="button" class="ButtonAccept" 
            onClick="html_cache_onclick();" value="<?php echo __('save cache') ?>"
            />
        <?php ///endif; ?>
</div>

<form name="frmMain_<?php echo $v_default; ?>" id="frmMain_<?php echo $v_default; ?>" action="" method="post">
    <table width="100%" class="adminlist" cellspacing="0" border="1">
        <colgroup>
            <col width="10%">
            <col width="75%">
            <col width="15%">
        </colgroup>
        <tr>
            <th><input type="checkbox" id="chk_all"/></th>
            <th><?php echo __('title') ?></th>
            <th><?php echo __('order') ?></th>
        </tr>
        <?php $n = count($arr_all_sticky); ?>
        <?php if ($n == 0): ?>
            <tr>
                <td colspan="3" class="Center">
                    <b><?php echo __('there are no record') ?></b>
                </td>
            </tr>
        <?php endif; ?>
        <?php for ($i = 0; $i < $n; $i++): ?>
            <?php
            $item    = $arr_all_sticky[$i];
            $prev_id = isset($arr_all_sticky[$i - 1]) ? $arr_all_sticky[$i - 1]['PK_STICKY'] : '';
            $next_id = isset($arr_all_sticky[$i + 1]) ? $arr_all_sticky[$i + 1]['PK_STICKY'] : '';
            $v_class = '';
            if (
                    $item['C_STATUS'] < 3
                    OR $item['CK_BEGIN_DATE'] < 0
                    OR $item['CK_END_DATE'] < 0
                    OR $item['C_CAT_STATUS'] == 0
            )
            {
                $v_class = 'line-through';
            }
            ?>
            <tr class="row<?php echo $i % 2 ?>">
                <td class="center">
            <center>
                <input 
                    type="checkbox" name="chk_item[]" class="chk_item" 
                    value="<?php echo $item['PK_STICKY'] ?>" id="item_<?php echo $item['PK_STICKY'] ?>"
                    />
            </center>
            </td>
            <td>
                <label class="<?php echo $v_class ?>" for="item_<?php echo $item['PK_STICKY'] ?>">
                    <?php echo $item['C_TITLE']; ?>
                </label>
            </td>
            <td class="center">
            <center>
                <?php if ($prev_id ): ?>
                    <img 
                        height="16" width="16" src="<?php echo SITE_ROOT ?>public/images/up.png"
                        onclick="swap_order(<?php echo $item['PK_STICKY'] ?>, <?php echo $prev_id ?>)"
                        />
                    <?php endif; ?>
                    <?php if ($next_id ): ?>
                    <img 
                        height="16" width="16" src="<?php echo SITE_ROOT ?>public/images/down.png"
                        onclick="swap_order(<?php echo $item['PK_STICKY'] ?>, <?php echo $next_id ?>)"
                        />
                    <?php endif; ?>
            </center>
            </td>
            </tr>
        <?php endfor; ?>
        <?php $n = get_request_var('sel_rows_per_page', _CONST_DEFAULT_ROWS_PER_PAGE); ?>
        <?php for ($i; $i < $n; $i++): ?>
            <tr class="row<?php echo $i % 2 ?>">
                <td></td>
                <td></td>
                <td></td>
            </tr>
        <?php endfor; ?>
    </table>
</form>
<div class="button-area">
    <?php //if (Session::check_permission('THEM_MOI_NOI_BAT')): ?>
        <input 
            type="button" class="ButtonAdd" 
            onClick="add_sticky();" value="<?php echo __('add new') ?>"
            />
        <?php //endif; ?>
        <?php //if (Session::check_permission('XOA_NOI_BAT')): ?>
        <input 
            type="button" class="ButtonDelete" 
            onClick="delete_sticky();" value="<?php echo __('delete') ?>"
            />
        <?php //endif; ?>
        <?php if (get_system_config_value(CFGKEY_CACHE) == 'true'): ?>
        <input 
            type="button" class="ButtonAccept" 
            onClick="html_cache_onclick();" value="<?php echo __('save cache') ?>"
            />
        <?php endif; ?>
</div>
<script>
    toggle_checkbox('#chk_all', '.chk_item');
    
    function sel_category_onchange()
    {
        $.ajax({
            type: 'post',
            url: $('#controller').val() + 'dsp_all_sticky/0',
            data: $('#frm_filter_<?php echo $v_default ?>').serialize(),
            success:function(obj){
                $('#frm_filter_<?php echo $v_default ?>').parent().html(obj);
            }
        });
        
    }

    function add_sticky()
    {
        var $default = "<?php echo $v_default; ?>";
        var $svc_url = "<?php echo $this->get_controller_url('article', 'admin') ?>dsp_all_article_svc";
        if($default == 0)
        {
            $svc_url += '&disable_website=1&disable_category=1';
            $svc_url += '&hdn_category=' + $('#sel_category').val();
        }
        $svc_url += '&status=3';
        showPopWin($svc_url, 800, 600, function(json){
            if(json)
            {
                $.ajax({
                    type: 'post',
                    url: $('#controller').val() + $('#hdn_insert_method').val(),
                    data: {
                        'default': $default,
                        'article': json
                    },
                    success: function(json){
                        console.log(json);
                        reload_ajax();
                    }
                });
            }
        });
    }
    
    function delete_sticky()
    {
        if(!confirm("<?php echo __('are you sure to delete all selected object?') ?>"))
        {
            return;
        }
        $.ajax({
            type: 'post',
            url: $('#controller').val() + $('#hdn_delete_method').val(),
            data: $('#frmMain_<?php echo $v_default ?>').serialize(),
            success: function(json){
                reload_ajax();
            }
        });
    }
    
    function swap_order($item1, $item2)
    {
        $.ajax({
            type: 'post',
            url: $('#controller').val() + 'swap_sticky_order',
            data: {item1: $item1, item2: $item2},
            success: function(json){
                reload_ajax();
            }
        });
    }
    
    function reload_ajax()
    {
        load_current_tab();
    }
    
    function html_cache_onclick()
    {
        url='<?php echo $this->get_controller_url(); ?>create_cache';
        $('#frmMain_<?php echo $v_default; ?>').attr('action',url);
        $('#frmMain_<?php echo $v_default; ?>').submit();
    }
    
</script>