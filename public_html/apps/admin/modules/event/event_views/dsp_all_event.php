<?php if (!defined('SERVER_ROOT'))
{
    exit('No direct script access allowed');
} ?>
<?php
//header
//@session::init();
$this->template->title = __('event manager');
$this->template->display('dsp_header.php');
?>
<h2 class="module_title"><?php echo __('event manager'); ?></h2>
<form name="frmMain" id="frmMain" action="" method="POST">
    <?php
    echo $this->hidden('controller', $this->get_controller_url());
    echo $this->hidden('hdn_item_id', 0);
    echo $this->hidden('hdn_item_id_list', '');

    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_event');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_event');
    echo $this->hidden('hdn_update_method', 'update_event');
    echo $this->hidden('hdn_delete_method', 'delete_event');
    echo $this->hidden('hdn_item_id_swap', 0);
    ?>
    <div id="div_search">
        <div class="normal_search">
            <label><?php echo __('type'); ?></label>
            <select name="type_event" id="type_event" onchange="type_event_onchange()">
                <option value=""><?php echo '----- ' . __('type') . ' -----'; ?></option>
                <option value="0" <?php echo ($arr_search['type_event'] == '0') ? 'selected' : ''; ?>><?php echo __('event'); ?></option>
                <option value="1" <?php echo ($arr_search['type_event'] == '1') ? 'selected' : ''; ?>><?php echo __('report'); ?></option>
            </select>
            <label><?php echo __('file name'); ?></label>
            <input type="textbox" size="40" name="txt_search" id="txt_search" value="<?php echo $arr_search['txt_search']; ?>"/>
            <input type="button" class="ButtonSearch" value="<?php echo __('search'); ?>" onclick="btn_submit_onclick()"/>
        </div>
    </div>
    <table width="100%" class="adminlist" cellspacing="0" border="1">
        <colgroup>
            <col width="5%" />
            <col width="45%" />
            <col width="15%" />
            <col width="15%" />
            <col width="10%" />
        </colgroup>
        <tr>
            <th><input type="checkbox" name="chk_check_all" onclick="toggle_check_all(this,this.form.chk);"/></th>
            <th><?php echo __('event name'); ?></th>
            <th><?php echo __('total record'); ?></th>
            <th><?php echo __('status'); ?></th>
            <th><?php echo __('order1'); ?></th>
        </tr>
        <?php
        $row = 0;
        $i   = 0
        ?>
        <?php
        for ($i = 0; $i < count($arr_all_event); $i++):
            $v_event_id      = $arr_all_event[$i]['PK_EVENT'];
            $v_name          = $arr_all_event[$i]['C_NAME'];
            $v_total_article = $arr_all_event[$i]['C_TOTAL_ARTICLE'];
            $v_status        = $arr_all_event[$i]['C_STATUS'];
            $next            = isset($arr_all_event[$i + 1]['PK_EVENT']) ? $arr_all_event[$i + 1]['PK_EVENT'] : false;
            $prev            = isset($arr_all_event[$i - 1]['PK_EVENT']) ? $arr_all_event[$i - 1]['PK_EVENT'] : false;
            ?>

            <tr class="row<?php echo $row; ?>">
                <td class="center">
                    <input type="checkbox" name="chk"
                           value="<?php echo $v_event_id; ?>" 
                           onclick="if (!this.checked) this.form.chk_check_all.checked=false;" 
                           />
                </td>
                <td>
                    <a href="javascript:void(0)" onclick="row_onclick(<?php echo $v_event_id; ?>)"><?php echo $v_name; ?></a>
                </td>
                <td><center><?php echo $v_total_article; ?></center></td>
            <td><center><?php echo ($v_status == 1) ? __('Hiển thị') : __('Không hiển thị'); ?></center></td>
            <td>
            <center>
                <?php if ($i == 0): ?>
                    <a href="javascript:void(0)" onclick="swap_order_event(<?php echo $v_event_id ?>,<?php echo $next; ?>)">
                        <img width="16" height="16" src="<?php echo $this->image_directory . "down.png"; ?>">
                    </a>
                <?php elseif ($i == count($arr_all_event) - 1): ?>
                    <a href="javascript:void(0)" onclick="swap_order_event(<?php echo $v_event_id ?>,<?php echo $prev; ?>)">
                        <img width="16" height="16" src="<?php echo $this->image_directory . "up.png"; ?>">
                    </a>
    <?php else: ?>
                    <a href="javascript:void(0)" onclick="swap_order_event(<?php echo $v_event_id ?>,<?php echo $next; ?>)">
                        <img width="16" height="16" src="<?php echo $this->image_directory . "down.png"; ?>">
                    </a>
                    <a href="javascript:void(0)" onclick="swap_order_event(<?php echo $v_event_id ?>,<?php echo $prev; ?>)">
                        <img width="16" height="16" src="<?php echo $this->image_directory . "up.png"; ?>">
                    </a>
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
                <td></td>
                <td></td>
            </tr>
<?php endfor; ?>
    </table>
        <?php echo $this->paging2($arr_all_event); ?>
    <div class="button-area">
        <input type="button" name="addnew" class="ButtonAdd" value="<?php echo __('add new'); ?>" onclick="btn_addnew_onclick();"/>
        <input type="button" name="trash" class="ButtonDelete" value="<?php echo __('delete'); ?>" onclick="btn_delete_onclick();"/>

    <?php if (get_system_config_value(CFGKEY_CACHE) == 'true'): ?>
            <input type="button" name="trash" class="ButtonAccept" value="<?php echo __('save cache html'); ?>" onclick="save_cache_onclick();"/>
    <?php endif; ?>
    </div>

</form>
<script type="text/javascript">
    function swap_order_event(id,swap_id)
    {
        $('#hdn_item_id').attr('value',id);
        $('#hdn_item_id_swap').attr('value',swap_id);
        var str = "<?php echo $this->get_controller_url() . "swap_order" ?>";
        $('#frmMain').attr('action',str);
        $('#frmMain').submit();
    }
    function btn_submit_onclick()
    {
        $('#frmMain').attr('action','<?php echo $this->get_controller_url() . "dsp_all_event" ?>');
        $('#frmMain').submit();
    }
    function type_event_onchange()
    {
        btn_submit_onclick();
    }
    function save_cache_onclick()
    {
        url = '<?php echo $this->get_controller_url(); ?>create_cache';
        $('#frmMain').attr('action',url);
        $('#frmMain').submit();
    }
</script>
<?php
$this->template->display('dsp_footer.php');