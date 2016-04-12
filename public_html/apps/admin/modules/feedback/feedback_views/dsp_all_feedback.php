<?php defined('DS') or die('no direct access') ?>
<?php
$this->template->title = __('feedback');
$this->template->display('dsp_header.php');

?>


<h2 class="module_title"><?php echo __('feedback'); ?></h2>
<form name="frmMain" id="frmMain" action="" method="POST">
<?php
    echo $this->hidden('controller', $this->get_controller_url());
    echo $this->hidden('hdn_item_id', 0);
    echo $this->hidden('hdn_item_id_list', '');

    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_feedback');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_feedback');
    echo $this->hidden('hdn_update_method', 'update_feedback');
    echo $this->hidden('hdn_delete_method', 'delete_feedback');
?>
    <!--seach-->
    <div class="div_filter_magazine">
        <div class="float_right">
            
        </div>
    </div>
    <div class="clear" style="height: 10px">&nbsp;</div> 
    <!--danh sach nguoi dat bao-->
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
            <th><?php echo __('title'); ?></th>
            <th><?php echo __('email'); ?></th>
            <th><?php echo __('date'); ?></th>
            <th><?php echo __('status'); ?></th>
        </tr>
        <?php for($i=0;$i<count($arr_all_feedback);$i++):
                $arr_feedback = $arr_all_feedback[$i];
        
                $v_id    = $arr_feedback['PK_FEEDBACK'];
                $v_name  = $arr_feedback['C_TITLE'];
                $v_email = $arr_feedback['C_EMAIL'];
                $v_phone = $arr_feedback['C_INIT_DATE'];
                
                $v_status = $arr_feedback['C_REPLY'];
                $v_status = ($v_status == NULL OR $v_status == '')? __('unanswered') : __('answered');
                
                $row = ($i % 2 == 0)?0:1;
        ?>
         <tr class="row<?php echo $row; ?>">
                <td class="center">
                    <input type="checkbox" name="chk"
                           value="<?php echo $v_id; ?>" 
                           onclick="if (!this.checked) this.form.chk_check_all.checked=false;" 
                           />
                </td>
            <td>
                <a href="javascript:void(0)" onclick="row_onclick(<?php echo $v_id; ?>)">
                    <?php echo $v_name;?>
                </a>
            </td>
            <td style="text-align: center;">
                <?php echo $v_email;?>
            </td>
            <td style="text-align: center;">
                <?php echo $v_phone;?>
            </td>
            <td style="text-align: center;">
                <?php echo $v_status;?>
            </td>
        </tr>
        <?php endfor;?>
    </table>
    <?php echo $this->paging2($arr_all_feedback);?>
    <div class="button-area">
        <input type="button" name="btn_delete" onclick="btn_delete_onclick()" class="ButtonDelete" value="<?php echo __('delete')?>">
    </div>
</form>
<?php
$this->template->display('dsp_footer.php');
?>
<script>
function btn_filtter_onclick()
{
    m = $('#controller').val();
    $('#frmMain').attr('action',m);
    $('#frmMain').submit();
}
</script>