<?php
defined('DS') or die('no direct access');
$v_pos_name = isset($arr_single_position['C_NAME']) ? $arr_single_position['C_NAME'] : '';
$v_pos_id   = isset($arr_single_position['PK_SPOTLIGHT_POSITION']) ? $arr_single_position['PK_SPOTLIGHT_POSITION'] : '';
?>
<?php

function show_button()
{
    $html = '';
    $html .= '<div class="button-area">';
    
    $html .= '<input type="button" class="ButtonAdd" onClick="btn_add_onclick();"
            value="' . __('add new') . '"/>';

    $html .= '<input type="button" class="ButtonDelete" onClick="btn_delete_onclick();"
        value="' . __('delete') . '"/>';
    $html .= '</div>';
   
    echo $html;
}
?>
<form name="frmMain" id="frmMain" method="post" action="#">
    <?php
    echo $this->hidden('controller', $this->get_controller_url());
    echo $this->hidden('XmlData', '');
    echo $this->hidden('hdn_item_id', $v_pos_id);
    ?>
    </br>
    <label for="txt_name">
        <?php echo __('name') ?><span class="required">(*)</span>
    </label>
    <input type="text" name="txt_name" id="txt_name"
           class="inputbox" maxlength="500" size="30"
           data-allownull="no" data-validate="text"
           data-name="<?php echo __('name'); ?>" 
           value="<?php echo $v_pos_name ?>"
           data-xml="no" data-doc="no"
           />
    &nbsp;
        <input 
            type="button" class="ButtonAccept"
            onClick="frmMain_on_submit();" value="<?php echo __('apply') ?>"
            /></br>
        <input 
            type="button" class="ButtonDelete" 
            onclick="$('span[data-id=<?php echo $v_pos_id ?>]').click();"
            value="<?php echo __('delete position'); ?>"
            />
        <?php if (get_system_config_value(CFGKEY_CACHE) == 'true'): ?>
        <input type="button" class="ButtonAccept" onClick="btn_cache_onclick(<?php echo $v_pos_id ?>)" value="<?php echo __('save cache') ?>"/>
    <?php endif; ?>
    <hr></hr>

        <?php show_button(); ?>
        <div>
            <table width="100%" class="adminlist" cellspacing="0" border="1">
                <colgroup>
                    <col width="10%">
                    <col width="70%">
                    <col width="20%">
                </colgroup>
                <tr>
                    <th><input type="checkbox" id="chk_all"/></th>
                    <th><?php echo __('title') ?></th>
                    <th><?php echo __('order') ?></th>
                </tr>
                <?php $n = count($arr_all_spotlight) ?>
                <?php for ($i = 0; $i < $n; $i++): ?>
                    <?php
                    $item    = $arr_all_spotlight[$i];
                    $v_id    = $item['PK_SPOTLIGHT'];
                    $v_title = $item['C_TITLE'];
                    $v_prev  = isset($arr_all_spotlight[$i - 1]) ? $arr_all_spotlight[$i - 1]['PK_SPOTLIGHT'] : 0;
                    $v_next  = isset($arr_all_spotlight[$i + 1]) ? $arr_all_spotlight[$i + 1]['PK_SPOTLIGHT'] : 0;
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
                        <td class="Center">
                            <input 
                                type="checkbox" name="chk_item[]" class="chk_item"
                                value="<?php echo $v_id ?>"
                                id="item_<?php echo $i ?>"
                                />
                        </td>
                        <td>
                            <label class="<?php echo $v_class ?>" for="item_<?php echo $i ?>"><?php echo $v_title ?></label>
                        </td>
                        <td class="Center">
                                <?php if ($v_prev): ?>
                                    <img 
                                        width="16" height="16" 
                                        onClick="swap_order(<?php echo $v_id ?>,<?php echo $v_prev ?>)"
                                        src="<?php echo SITE_ROOT ?>public/images/up.png"
                                        />
                                    <?php endif; ?>
                                    <?php if ($v_next): ?>
                                    <img 
                                        width="16" height="16" 
                                        onClick="swap_order(<?php echo $v_id ?>,<?php echo $v_next ?>)"
                                        src="<?php echo SITE_ROOT ?>public/images/down.png"
                                        />
                                    <?php endif; ?>
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
        </div>
        <?php show_button(); ?>
    
</form>

<script>
    toggle_checkbox('#chk_all', '.chk_item');
    function frmMain_on_submit()
    {
        var f = document.frmMain;
        var xObj = new DynamicFormHelper('','',f);
        if (xObj.ValidateForm(f)){
            $.ajax({
                type: 'post',
                url: $('#controller').val() + 'update_position',
                data: $(f).serialize(),
                success: function(){
                    window.location.reload();
                }
            });
        }
        return false;
    }
    
    function btn_add_onclick(){
        $url_svc = "<?php echo $this->get_controller_url('article', 'admin'); ?>" + 'dsp_all_article_svc';
        $url_insert = "<?php echo $this->get_controller_url(); ?>" + 'insert_spotlight';
        showPopWin($url_svc, 800, 600, function(json){
            if(json)
            {
                $.ajax({
                    type: 'post',
                    url: $url_insert,
                    data: {article: json, position: $('#hdn_item_id').val()},
                    success: function(){
                        reload_current_tab();
                    }
                });
            }
        });
    }
    
    function swap_order($item1, $item2)
    {
        $url = "<?php echo $this->get_controller_url() ?>" + "swap_spotlight_order";
        $.ajax({
            type: 'post',
            url: $url,
            data: {item1: $item1, item2: $item2},
            success: function (){reload_current_tab();}
        });
    }
    
    window.btn_delete_onclick = function(){
        $url = "<?php echo $this->get_controller_url() ?>" + "delete_spotlight";
        if($('.chk_item:checked').length == 0)
        {
            alert("<?php echo __('you must choose atleast one object') ?>");
            return;
        }
        if(!confirm("<?php echo __('are you sure to delete all selected object?') ?>"))
        {
            return;
        }
        $.ajax({
            type: 'post',
            url: $url,
            data: $('#frmMain').serialize(),
            success: function(){reload_current_tab();}
        });
    }
    
    function btn_cache_onclick($pos_id){
        $url = '<?php echo $this->get_controller_url() ?>cache/' + $pos_id;
        window.location.href = $url;
    }
</script>
