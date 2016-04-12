<?php if (!defined('SERVER_ROOT')) { exit('No direct script access allowed');} ?>
<?php
//display header
$this->template->title = __('citizens question manager');
$this->template->display('dsp_header.php');
$tab_select ='#tab_'. $tab_select;
?>

<form id="frmMain" name="frmMain" action="" method="POST">
<?php echo $this->hidden('controller',$this->get_controller_url());?>
<?php echo $this->hidden('hdn_item_id', '');?>
<?php echo $this->hidden('hdn_item_id_list','');?>
<?php echo $this->hidden('hdn_item_id_swap', '');?>
<?php echo $this->hidden('hdn_delete_method', '');?>
<?php echo $this->hidden('hdn_dsp_single_method', '');?>
<?php echo $this->hidden('hdn_tab_select', $tab_select);?>
    <!-- Toolbar -->
    <h2 class="module_title"><?php echo __('citizens question manager'); ?></h2>
    <!-- /Toolbar -->
    <div id="tabs_cq">
        <ul>
            <li><a href="#tab_question" ><?php echo __('manager question');?></a></li>
            <li><a href="#tab_field" ><?php echo __('manager field');?></a></li>
        </ul>
        <div id="tab_question">
                <div class="advanced_search_button" style="margin-bottom: 10px;">
                    <input style="font-weight: bold;" 
                           type="button" 
                           class="ButtonSearch" 
                           data-target="div_question_advanced_search" 
                           onclick="btn_advanced_search_onclick(this)" 
                           value="<?php echo __('advanced search'); ?>">
                </div>
                <div id="div_question_advanced_search" style="display: none;border: 1px solid #DADEDD;margin-bottom: 10px;">
                    <table class="advanced_search">
                        <tr>
                            <td style="width: 30%">
                                <label><?php echo __('time to search'); ?></label>
                            </td>
                            <td>
                                <lable><?php echo __('begin time to search'); ?></label>
                                <input type="textbox" 
                                       id="txt_begin_time" 
                                       name="txt_begin_time" 
                                       value="<?php echo empty($arr_search['txt_begin_time'])?'':$arr_search['txt_begin_time'];?>" 
                                       onclick="DoCal('txt_begin_time')"/>
                                <img height="16" width="16" src="<?php echo SITE_ROOT?>public/images/calendar.png" onclick="DoCal('txt_begin_time')">
                            </td>
                            <td>
                                <lable><?php echo __('end time to search'); ?></label>
                                <input type="textbox" 
                                       id="txt_end_time" 
                                       name="txt_end_time" 
                                       value="<?php echo empty($arr_search['txt_end_time'])?'':$arr_search['txt_end_time'];?>" 
                                       onclick="DoCal('txt_end_time')"/>
                                <img height="16" width="16" src="<?php echo SITE_ROOT?>public/images/calendar.png" onclick="DoCal('txt_end_time')">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label><?php echo __('field'); ?></label>
                            </td>
                            <td colspan="2">
                                <select id="select_field" name="select_field">
                                    <option value="0" <?php echo ($arr_search['select_field']=='0')?'selected':'';?>>
                                        <?php echo '----'.__('select field').'----'?>
                                    </option>
                                    <?php foreach ($arr_all_field as $field):?>
                                        <?php if($field['C_STATUS']==1):?>
                                            <option value="<?php echo $field['PK_FIELD'];?>" <?php echo ($arr_search['select_field']==$field['PK_FIELD'])?'selected':'';?>>
                                                <?php echo $field['C_NAME'];?>
                                            </option>
                                        <?php endif;?>
                                    <?php endforeach;?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label><?php echo __('key words'); ?></label>
                            </td>
                            <td colspan="2">
                                <input type="textbox" size="40" name="txt_advanced_search" id="txt_advanced_search" value="<?php echo $arr_search['txt_advanced_search']?>"/>
                                <input type="button" class="ButtonSearch" onclick="btn_search_onclick('question')" value="<?php echo __('search'); ?>"/>
                            </td>                  
                        </tr>
                    </table>
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
                            <th><?php echo __('title'); ?></th>
                            <th><?php echo __('sender'); ?></th>
                            <th><?php echo __('status'); ?></th>
                            <th><?php echo __('order'); ?></th>
                        </tr>
                        <?php
                            $row = 0;
                            $i = 0
                        ?>
                        <?php
                        for ($i = 0; $i < count($arr_all_question); $i++):
                            $v_question_id = $arr_all_question[$i]['PK_CQ'];
                            $v_title       = $arr_all_question[$i]['C_TITLE'];
                            $v_name        = $arr_all_question[$i]['C_NAME'];
                            $v_status      = $arr_all_question[$i]['C_STATUS'];
                            $next          = isset($arr_all_question[$i + 1]['PK_CQ']) ? $arr_all_question[$i + 1]['PK_CQ'] : false;
                            $prev          = isset($arr_all_question[$i - 1]['PK_CQ']) ? $arr_all_question[$i - 1]['PK_CQ'] : false;
                            ?>

                        <tr class="row<?php echo $row; ?>">
                            <td class="center">
                                <input type="checkbox" name="chk"
                                       value="<?php echo $v_question_id; ?>" 
                                       onclick="if (!this.checked) this.form.chk_check_all.checked=false;" 
                                       />
                            </td>
                            <td>
                                <a href="javascript:void(0)" onclick="row_cq_onclick('question',<?php echo $v_question_id; ?>)" class="<?php echo ($v_status == 0) ? 'line_fail' : '' ?>">
                                    <?php echo $v_title; ?>
                                </a>
                            </td>
                            <td><center><?php echo $v_name; ?></center></td>
                            <td><center><?php echo ($v_status == 1) ? __('approved') : __('not approved'); ?></center></td>
                        <td>
                            <?php if (count($arr_all_question) != 1): ?>
                            <center>
                            <?php if ($i == 0): ?>
                                    <a href="javascript:void(0)" onclick="swap_order('question',<?php echo $v_question_id ?>,<?php echo $next; ?>)">
                                        <img width="16" height="16" src="<?php echo $this->image_directory . "down.png"; ?>">
                                    </a>
                            <?php elseif ($i == count($arr_all_question) - 1): ?>
                                    <a href="javascript:void(0)" onclick="swap_order('question',<?php echo $v_question_id ?>,<?php echo $prev; ?>)">
                                        <img width="16" height="16" src="<?php echo $this->image_directory . "up.png"; ?>">
                                    </a>
                            <?php else: ?>
                                    <a href="javascript:void(0)" onclick="swap_order('question',<?php echo $v_question_id ?>,<?php echo $next; ?>)">
                                        <img width="16" height="16" src="<?php echo $this->image_directory . "down.png"; ?>">
                                    </a>
                                    <a href="javascript:void(0)" onclick="swap_order('question',<?php echo $v_question_id ?>,<?php echo $prev; ?>)">
                                        <img width="16" height="16" src="<?php echo $this->image_directory . "up.png"; ?>">
                                    </a>
                            <?php endif; ?>
                            </center>
                            <?php endif; ?>
                        </td>    
                        </tr>
                            <?php
                            $row = ($row == 1) ? 0 : 1;
                            ?>
                        <?php endfor; ?>
                        <?php $n = get_request_var('sel_rows_per_page', _CONST_DEFAULT_ROWS_PER_PAGE); ?>
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
                    <?php echo $this->paging2($arr_all_question); ?>
                        <div class="button-area">
                            <input type="button" name="trash" class="ButtonDelete" value="<?php echo __('delete'); ?>" onclick="btn_delete_cq_onclick('question');"/>
                        </div>
            
        </div>
        <div id="tab_field" style="border:0" width="500" height="500">
            <div class="advanced_search_button" style="margin-bottom: 10px;">
                    <input style="font-weight: bold;" 
                           type="button" 
                           class="ButtonSearch" 
                           data-target="div_field_advanced_search" 
                           onclick="btn_advanced_search_onclick(this)" 
                           value="<?php echo __('advanced search'); ?>">
                </div>
                <div id="div_field_advanced_search" style="display: none;border: 1px solid #DADEDD;margin-bottom: 10px;">
                    <table class="advanced_search">
                        <tr>
                            <td style="width: 30%">
                                <label><?php echo __('time to search'); ?></label>
                            </td>
                            <td>
                                <lable><?php echo __('begin time to search'); ?></label>
                                <input type="textbox" 
                                       id="txt_field_begin_time" 
                                       name="txt_field_begin_time" 
                                       value="<?php echo $arr_search_field['txt_field_begin_time']?>" 
                                       onclick="DoCal('txt_field_begin_time')"/>
                                <img height="16" width="16" src="<?php echo SITE_ROOT?>public/images/calendar.png" onclick="DoCal('txt_field_begin_time')">
                            </td>
                            <td>
                                <lable><?php echo __('end time to search'); ?></label>
                                <input type="textbox" 
                                       id="txt_field_end_time" 
                                       name="txt_field_end_time" 
                                       value="<?php echo $arr_search_field['txt_field_end_time']?>" 
                                       onclick="DoCal('txt_field_end_time')"/>
                                <img height="16" width="16" src="<?php echo SITE_ROOT?>public/images/calendar.png" onclick="DoCal('txt_field_end_time')">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label><?php echo __('status'); ?></label>
                            </td>
                            <td colspan="2">
                                <select id="select_status" name="select_status">
                                    <option value="-1">
                                        <?php echo '----'.__('status').'----'?>
                                    </option>
                                    <option value="1" <?php echo ($arr_search_field['select_status']=='1')?'selected':'';?>>
                                        <?php echo __('display')?>
                                    </option>
                                    <option value="0" <?php echo ($arr_search_field['select_status']=='0')?'selected':'';?>>
                                        <?php echo __('display none')?>
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label><?php echo __('key words'); ?></label>
                            </td>
                            <td colspan="2">
                                <input type="textbox" size="40" name="txt_field_advanced_search" id="txt_field_advanced_search" value="<?php echo $arr_search_field['txt_field_advanced_search']?>"/>
                                <input type="button" class="ButtonSearch" onclick="btn_search_onclick('field')" value="<?php echo __('search'); ?>"/>
                            </td>                  
                        </tr>
                    </table>
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
                        <th><?php echo __('field name'); ?></th>
                        <th><?php echo __('status'); ?></th>
                        <th><?php echo __('order'); ?></th>
                    </tr>
                    <?php
                    $row = 0;
                    $i = 0
                    ?>
                    <?php
                    for ($i = 0; $i < count($arr_all_field); $i++):
                        $v_field_id = $arr_all_field[$i]['PK_FIELD'];
                        $v_file_name = $arr_all_field[$i]['C_NAME'];
                        $v_field_status = $arr_all_field[$i]['C_STATUS'];
                        $next = isset($arr_all_field[$i + 1]['PK_FIELD']) ? $arr_all_field[$i + 1]['PK_FIELD'] : false;
                        $prev = isset($arr_all_field[$i - 1]['PK_FIELD']) ? $arr_all_field[$i - 1]['PK_FIELD'] : false;
                        $v_count_depend = $arr_all_field[$i]['COUNT_DEPEND'];
                        ?>

                        <tr class="row<?php echo $row; ?>">
                            <td class="center">
                                <input type="checkbox" name="chk"
                                       value="<?php echo $v_field_id; ?>" 
                                       onclick="if (!this.checked) this.form.chk_check_all.checked=false;"
                                       <?php echo ($v_count_depend>0)?'disabled':'';?>
                                       />
                            </td>
                            <td>
                                <a href="javascript:void(0)" onclick="row_cq_onclick('field',<?php echo $v_field_id; ?>)" 
                                   class="<?php echo ($v_field_status == 0) ? 'line_fail' : '' ?>">
                                    <?php echo $v_file_name; ?>
                                </a>
                            </td>
                        <td><center><?php echo ($v_field_status == 1) ? __('approved') : __('not approved'); ?></center></td>
                        <td>
                            <?php if (count($arr_all_field) != 1): ?>
                            <center>
                                <?php if ($i == 0): ?>
                                    <a href="javascript:void(0)" onclick="swap_order('field',<?php echo $v_field_id ?>,<?php echo $next; ?>)">
                                        <img width="16" height="16" src="<?php echo $this->image_directory . "down.png"; ?>">
                                    </a>
                                <?php elseif ($i == count($arr_all_field) - 1): ?>
                                    <a href="javascript:void(0)" onclick="swap_order('field',<?php echo $v_field_id ?>,<?php echo $prev; ?>)">
                                        <img width="16" height="16" src="<?php echo $this->image_directory . "up.png"; ?>">
                                    </a>
                                <?php else: ?>
                                    <a href="javascript:void(0)" onclick="swap_order('field',<?php echo $v_field_id ?>,<?php echo $next; ?>)">
                                        <img width="16" height="16" src="<?php echo $this->image_directory . "down.png"; ?>">
                                    </a>
                                    <a href="javascript:void(0)" onclick="swap_order('field',<?php echo $v_field_id ?>,<?php echo $prev; ?>)">
                                        <img width="16" height="16" src="<?php echo $this->image_directory . "up.png"; ?>">
                                    </a>
                                <?php endif; ?>
                            </center>
                        <?php endif; ?>
                        </td>    
                        </tr>
                        <?php
                        $row = ($row == 1) ? 0 : 1;
                        ?>
                    <?php endfor; ?>
                    <?php $n = get_request_var('sel_rows_per_page', _CONST_DEFAULT_ROWS_PER_PAGE); ?>
                    <?php for ($i; $i < $n; $i++): ?>
                        <tr class="row<?php echo $i % 2 ?>">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endfor; ?>
                </table>
            <div class="button-area">
                <input type="button" class="ButtonAdd" value="<?php echo __('add new')?>" onclick="btn_addnew_field_onclick()">
                <input type="button" class="ButtonDelete" value="<?php echo __('delete')?>" onclick="btn_delete_cq_onclick('field')">
            </div>
        </div>
    </div>
</form>
<script>
 $(document).ready(function(){
     $('#tabs_cq').tabs();
     <?php if($arr_search['boolen_question_search']=='1'):?>
             $('#div_question_advanced_search').toggle();
     <?php endif;?>
         
     <?php if($arr_search_field['boolen_field_search']=='1'):?>
             $('#div_field_advanced_search').toggle();
     <?php endif;?>
     var tab = $('#hdn_tab_select').val();
     $('#tabs_cq').tabs('select',tab);
 });
 
function btn_advanced_search_onclick(button_search)
{
    id='#'+$(button_search).attr('data-target');
    $(id).toggle();
}
function btn_search_onclick(type)
{
    $('#hdn_tab_select').val(type);
    url="<?php echo $this->get_controller_url()?>dsp_all_cq";
    $('#frmMain').attr('action',url);
    $('#frmMain').submit();
}
function swap_order(type,id,id_swap)
{
    $('#hdn_item_id').val(id);
    $('#hdn_item_id_swap').val(id_swap);
    $('#hdn_tab_select').val(type);
    url="<?php echo $this->get_controller_url()?>/swap_order/"+type;
    $('#frmMain').attr('action',url);
    $('#frmMain').submit();
}
function btn_delete_cq_onclick(type)
{
    method="delete_"+type;
    $('#hdn_delete_method').val(method);
    btn_delete_onclick();
}
function row_cq_onclick(type,id)
{
    if(type=='question')
    {
        $('#hdn_dsp_single_method').val('dsp_single_question');
        row_onclick(id);
    }
    else if(type=='field')
    {
        $('#hdn_dsp_single_method').val('dsp_single_field');
        row_onclick(id);
    }
}
function btn_addnew_field_onclick()
{
     $('#hdn_dsp_single_method').val('dsp_single_field');
     btn_addnew_onclick();
}
</script>
<?php
$this->template->display('dsp_footer.php');
?>