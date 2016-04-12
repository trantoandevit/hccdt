<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');?>
<?php
    $arr_all_village  =  $arr_all_member['arr_all_village'];
    $arr_all_district =  $arr_all_member['arr_all_district'];  
    
    $v_spec_code = get_request_var('sel_spec_code','');
    $sel_village = get_request_var('sel_village','');
    $v_month     = get_request_var('sel_month', Date('m'));
    $sel_year_month      = get_request_var('sel_year_month', Date('Y'));
    $sel_year_quarter      = get_request_var('sel_year_quarter', Date('Y'));
    
    $period_checked = get_request_var('rad_period','month');
    $quarter_selected = get_request_var('sel_quarter','1');
    $v_current_fields = get_request_var('sel_spec_code','');
    $v_village_id     = get_request_var('hdn_village_id',0);
?>
<div class="col-md-12 block">
    <div class="div-synthesis">
        <div class="div_title_bg-title-top"></div>
        <div class="div_title">
            <div class="title-content">
                <label>
                    <?php echo __('statistics records for receiving and returning results')?>
                </label>
            </div>
        </div>
        <div style="overflow: hidden;margin-top: -6px;"></div>
        <div class="clear" style="height: 10px;"></div>
        <form class="form-horizontal" action="" method="GET" name="frmMain" id="frmMain" role="form">
        <div class="col-md-12">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label class="control-label col-md-2" style=""><?php echo __('units')?>:</label>
                        <div class="col-md-10">
                             <!--đơn vị--> 
                            <div class="col-md-5 block">
                                <input type="hidden" id="hdn_village_id" name="hdn_village_id" value="<?php echo $v_village_id;?>"/>
                                <select name="sel_village" id="sel_village" class="form-control" onchange="sel_village_onchange()">
                                    <option value=" ">-- <?php echo __('all unit')?> --</option>
                                    <?php foreach($arr_all_district as $arr_district):
                                            $v_name       = $arr_district['C_NAME'];
                                            $v_member_code= $arr_district['C_CODE'];
                                            $v_id         = $arr_district['PK_MEMBER'];                                            
                                            $v_selected   = ($v_member_code == $sel_village)?'selected':'';
                                    ?>
                                    <option  <?php echo $v_selected?> data-id="<?php echo $v_id; ?>" value="<?php echo $v_member_code?>"><?php echo $v_name?></option>
                                    
                                    <?php foreach($arr_all_village as $key => $arr_village):
                                            $v_village_name      = $arr_village['C_NAME'];
                                            $v_member_child_id   = $arr_village['PK_MEMBER'];
                                            $v_fk_village_id     = $arr_village['FK_VILLAGE_ID'];
                                            $v_member_code  = $arr_village['C_CODE'];
                                            $v_parent_id    = $arr_village['FK_MEMBER'];
                                            $v_member_code = $v_member_code.'_'.$v_fk_village_id;
                                            if($v_parent_id != $v_id)
                                            {
                                                continue;
                                            }
                                            $v_selected = ($v_member_code == $sel_village)?'selected':'';
                                            
                                    ?>
                                    <option <?php echo $v_selected?> data-id="<?php echo $v_member_child_id; ?>" value="<?php echo $v_member_code ?>"><?php echo '---- '.$v_village_name?></option>
                                    <?php 
                                        unset($arr_all_village[$key]);
                                        endforeach;
                                    ?>
                                    <?php endforeach;?>
                                </select>
                            </div>
                            <!--linh vuc-->
                            <label class="control-label col-md-2" style="white-space: nowrap"><?php echo __('field')?>:</label> 
                            <div class="col-md-5 block">
                                <select name="sel_spec_code" id="sel_spec_code" class="form-control col-md-5">
                                    <option value="">-- <?php echo __('all field')?> --</option>
                                    <?php 
                                        for($i = 0;$i <count($arr_all_spec); $i ++)
                                        {
                                            $single_fields                      = $arr_all_spec[$i];
                                            $v_fields_code                      = $single_fields['C_CODE'];
                                            $v_fields_name                      = $single_fields['C_NAME'];
                                            $v_fields_unit_code                 = $single_fields['C_UNIT_CODE'];                                            
                                            $v_fields_unit_code_fk_village_id   = $single_fields['C_UNIT_CODE_FK_FILLAGE_ID'];
                                            $v_fields_unit_code_fk_village_id   .= " $v_fields_unit_code_fk_village_id";
                                            echo "<option value='$v_fields_code' class='{$v_fields_unit_code}{$v_fields_unit_code_fk_village_id}' >$v_fields_name</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                           
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label class='col-md-2 control-label'><?php echo __('period')?>:</label>
                        <div class="col-md-10">
                            <label><input type="radio" name="rad_period" onclick="rad_period_onclick(this)" id="rad_period_month" value="month" <?php echo ($period_checked == 'month')?'checked':'';?> /> <?php echo __('month');?></label>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <label><input type="radio" name="rad_period" onclick="rad_period_onclick(this)" id="rad_period_quarter" value="quarter" <?php echo ($period_checked == 'quarter')?'checked':'';?> /> <?php echo __('quarter');?></label>
                        </div>
                    </div>
                </div>
            </div>
            <!--tra cuu ky theo thang-->
            <div class="form-group period_option" id="period_month" style="display:none">
                <div class="row">
                    <div class="col-md-12">
                        <label class='col-md-2 control-label'><?php echo __('month')?></label>
                        <div class="col-md-10">
                            <div class="col-md-5 block">
                                <select class='form-control' name='sel_month' id='sel_month'>
                                    <?php for($i=1;$i<=12;$i++)
                                    {
                                        $selected = ($i == $v_month)?'selected':'';
                                        echo "<option $selected value='$i'>$i</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <label class='col-md-2 control-label'><?php echo __('year')?></label>
                            <div class="col-md-5 block">
                                <select class="form-control" name='sel_year_month' id='sel_year'>
                                    <?php
                                        $v_max_year = $arr_year['C_MAX_YEAR'];
                                        $v_min_year = $arr_year['C_MIN_YEAR'];
                                        $v_loop = ($v_max_year - $v_min_year) + 1;
                                        for($i=0;$i<$v_loop;$i++)
                                        {
                                            $year = $v_min_year + $i;
                                            $selected = ($year == $sel_year_month)?'selected':'';
                                            echo "<option $selected value='$year'>$year</option>";
                                        }
                                    ?>

                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--tra cuu bao cao theo quy-->
            <div class="form-group period_option" id="period_quarter" style="display:none">
                <div class="row">
                    <div class="col-md-12">
                        <label class='col-md-2 control-label'><?php echo __('quarter')?></label>
                        <div class="col-md-10">
                            <div class="col-md-5 block">
                                <select class='form-control' name='sel_quarter' id='sel_month'>
                                    <?php for($i=1;$i<=4;$i++)
                                    {
                                        $selected = ($i == $quarter_selected)?'selected':'';
                                        echo "<option $selected value='$i'>". __('quarter') . " " .romanic_number($i)."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <label class='col-md-2 control-label'><?php echo __('year')?></label>
                            <div class="col-md-5 block">
                                <select class="form-control" name='sel_year_quarter' id='sel_year'>
                                    <?php
                                        $v_max_year = $arr_year['C_MAX_YEAR'];
                                        $v_min_year = $arr_year['C_MIN_YEAR'];
                                        $v_loop = ($v_max_year - $v_min_year) + 1;
                                        for($i=0;$i<$v_loop;$i++)
                                        {
                                            $year = $v_min_year + $i;
                                            $selected = ($year == $sel_year_quarter)?'selected':'';
                                            echo "<option $selected value='$year'>$year</option>";
                                        }
                                    ?>

                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-actions" style="text-align: right">
                <button type="submit" class="btn btn-primary btn-sm">
                    <span class="glyphicon glyphicon-search "></span>
                    <?php echo __('search')?>
                </button>
                <button type="button" class="btn btn-sm" onclick="btn_reset_onclick();">
                    <i class="glyphicon glyphicon-remove "></i>
                    <?php echo __('clear')?>
                </button>
            </div>
        </div> <!--end col md 12 block-->
        <div class="clear" style="height: 10px;"></div>
        </div><!--end synthesis-->
    
        <div class="col-md-12 block">
            <div id="result" class="div-synthesis">
                <div class="div_title_bg-title-top"></div>
                <div class="div_title">
                        <div class="title-content">
                            <label>
                                <?php echo __('record list') ?>
                            </label>
                        </div>
                </div>
                <table class="table_synthesis">
                    <tr>
                        <th class="first blue"  rowspan="2"><?php echo __('unit name'); ?></th>
                        <th class="top green" colspan="2"><?php echo __('receive'); ?></th>
                        <th class="top orange" colspan="3"><?php echo __('outstanding'); ?></th>
                        <th class="top green" colspan="4"><?php echo __('return'); ?></th>
                        <th class="top purple" colspan="2" ><?php echo __('Tạm dừng'); ?></th>
                        <th class="top red" colspan="2" ><?php echo __('Hủy hồ sơ'); ?></th>
                        <th class="top orange" colspan="3" ><?php echo __('Chờ trả kết quả'); ?></th>
                        <th class="top purple" rowspan="2"><?php echo __('Tỷ lệ giải quyết'); ?></th>
                    </tr>
                    <tr>
                        <th class="green"><?php echo __('previous period'); ?></th>
                        <th class="green"><?php echo __('receive'); ?></th>

                        <th class="orange"><?php echo __('total'); ?></th>
                        <th class="orange"><?php echo __('not yet'); ?></th>
                        <th class="orange"><?php echo __('overdue'); ?></th>
                        
                        <th class="green"><?php echo __('total'); ?></th>
                        <th class="green"><?php echo __('soon'); ?></th>
                        <th class="green"><?php echo __('on time'); ?></th>
                        <th class="green"><?php echo __('overdue'); ?></th>
                        
                        <th class="purple"><?php echo __('Bổ sung HS'); ?></th>
                        <th class="purple"><?php echo __('Thực hiện NVTC'); ?></th>
                        
                        <th class="red"><?php echo __('Từ chối'); ?></th>
                        <th class="red"><?php echo __('Công dân rút'); ?></th>
                        
                        <th class="orange"><?php echo __('total'); ?></th>
                        <th class="orange"><?php echo __('Trong kỳ'); ?></th>
                        <th class="orange"><?php echo __('Kỳ trước'); ?></th>
                    </tr>
                            <?php
                            $j = 0;
                            ?>
                            <?php
                            $v_cur_unit_code = isset($arr_all_report_data[0]['C_UNIT_CODE']) ? $arr_all_report_data[0]['C_UNIT_CODE'] : '';
                            for ($i =0;$i <count($arr_all_report_data);$i ++):                               
                                $arr_value = $arr_all_report_data[$i];                               
                                $v_unit_code               = $arr_value['C_UNIT_CODE'];
                                
                                $v_member_name             = isset($arr_value['C_NAME']) ? $arr_value['C_NAME'] : '';
                                $count_tiep_nhan_ky_truoc  = isset($arr_value['C_COUNT_KY_TRUOC']) ? $arr_value['C_COUNT_KY_TRUOC'] : 0;
                                $count_tong_tiep_nhan_thang= isset($arr_value['C_COUNT_TIEP_NHAN']) ? $arr_value['C_COUNT_TIEP_NHAN'] : 0;

                                $count_thu_ly_som_han      = isset($arr_value['C_COUNT_THU_LY_CHUA_DEN_HAN']) ? $arr_value['C_COUNT_THU_LY_CHUA_DEN_HAN'] : 0;
                                $count_thu_ly_qua_han      = isset($arr_value['C_COUNT_THU_LY_QUA_HAN']) ? $arr_value['C_COUNT_THU_LY_QUA_HAN'] : 0;

                                $count_tra_som_han         = isset($arr_value['C_COUNT_TRA_SOM_HAN']) ? $arr_value['C_COUNT_TRA_SOM_HAN'] : 0;
                                $count_tra_dung_han        = isset($arr_value['C_COUNT_TRA_DUNG_HAN']) ? $arr_value['C_COUNT_TRA_DUNG_HAN'] : 0;
                                $count_tra_qua_han         = isset($arr_value['C_COUNT_TRA_QUA_HAN']) ? $arr_value['C_COUNT_TRA_QUA_HAN'] : 0;

                                $count_bo_sung             = isset($arr_value['C_COUNT_BO_SUNG']) ? $arr_value['C_COUNT_BO_SUNG'] : 0;
                                $count_nvtc                = isset($arr_value['C_COUNT_NVTC']) ? $arr_value['C_COUNT_NVTC'] : 0;

                                $count_tu_choi             = isset($arr_value['C_COUNT_TU_CHOI']) ? $arr_value['C_COUNT_TU_CHOI'] : 0;
                                $count_con_dan_rut         = isset($arr_value['C_COUNT_CONG_DAN_RUT']) ? $arr_value['C_COUNT_CONG_DAN_RUT'] : 0;

                                $count_tro_tra_trong_ky    = isset($arr_value['C_COUNT_CHO_TRA_KY_TRUOC']) ? $arr_value['C_COUNT_CHO_TRA_KY_TRUOC'] : 0;
                                $count_tro_tra_truoc_ky    = isset($arr_value['C_COUNT_CHO_TRA_TRONG_KY']) ? $arr_value['C_COUNT_CHO_TRA_TRONG_KY'] : 0;
                                
                        if($v_unit_code != $v_cur_unit_code ):?>
                        <tr data-unit_code="<?php echo $v_cur_unit_code?>" data-total="true">
                            <td  class="center end left"><?php echo __('total'); ?> (<?php echo $j  ?> <?php echo __('units') ?>)</td>
                            <td data-sum="1" data-format='1' data-index="1" class="center end"></td>
                            <td data-sum="1" data-format='1' data-index="2"  class="center end"></td>
                            <td data-sum="1" data-format='1' data-index="3"  class="center end"></td>
                            <td data-sum="1" data-format='1' data-index="4"  class="center end"></td>
                            <td data-sum="1" data-format='1' data-index="5"  class="center end"></td>
                            <td data-sum="1" data-format='1' data-index="6"  class="center end"></td>
                            <td data-sum="1" data-format='1' data-index="7"  class="center end"></td>
                            <td data-sum="1" data-format='1' data-index="8"  class="center end"></td>
                            <td data-sum="1" data-format='1' data-index="9"  class="center end"></td>
                            <td data-sum="1" data-format='1' data-index="10"  class="center end"></td>
                            <td data-sum="1" data-format='1' data-index="11"  class="center end"></td>
                            <td data-sum="1" data-format='1' data-index="12"  class="center end"></td>
                            <td data-sum="1" data-format='1' data-index="13"  class="center end"></td>
                            <td data-sum="1" data-format='1' data-index="14"  class="center end"></td>
                            <td data-sum="1" data-format='1' data-index="15"  class="center end"></td>
                            <td data-sum="1" data-format='1' data-index="16"  class="center end"></td>
                            <td class="center end right"></td>
                        </tr>
                        <?php
                            $v_cur_unit_code = $v_unit_code;
                            $j = 0;
                        ?>
                    <?php endif;?>
                        <tr class="<?php echo ($i % 2) ? 'xam' : ''; ?>" data-unit="<?php echo $v_unit_code; ?>">
                            <td class="blue left" style="width: 23%;text-transform: uppercase" >
                                <?php echo $v_member_name ?>
                            </td>
                            <td class="green center" >
                                <?php echo $count_tiep_nhan_ky_truoc ?>
                            </td>
                            <td class="green center" >
                                <?php echo $count_tong_tiep_nhan_thang ?>
                            </td>
                            <td class="orange center" >
                                <?php echo $count_thu_ly_som_han + $count_thu_ly_qua_han; ?>
                            </td>
                            <td class="orange center" >
                                <?php echo $count_thu_ly_som_han ?>
                            </td>
                            <td class="orange center" >
                                <?php echo $count_thu_ly_qua_han ?>
                            </td>
                            <td class="green center" >
                                <?php echo ($count_tra_som_han + $count_tra_dung_han + $count_tra_qua_han); ?>
                            </td>
                            
                            <td class="green center" >
                                <?php echo $count_tra_som_han ?>
                            </td>
                            <td class="green center" >
                                <?php echo $count_tra_dung_han ?>
                            </td>
                            <td class="green center" >
                                <?php echo $count_tra_qua_han ?>
                            </td>
                            
                            <td class="purple center bold" >
                                <?php echo $count_bo_sung; ?>
                            </td>
                            <td class="purple center" >
                                <?php echo $count_nvtc ?>
                            </td>
                            
                            <td class="red center" >
                                <?php echo $count_tu_choi ?>
                            </td>
                            <td class="red center" >
                                <?php echo $count_con_dan_rut ?>
                            </td>
                            <td class="orange right center" >
                                <?php echo ($count_tro_tra_trong_ky + $count_tro_tra_truoc_ky) ?>
                            </td>
                            
                            <td class="orange right center" >
                                <?php echo $count_tro_tra_truoc_ky ?>
                            </td>
                            <td class="orange right center" >
                                <?php echo $count_tro_tra_trong_ky ?>
                            </td>
                            <td class="purple right center" >
                                <?php  
                                    $v_tyle  = '---';
                                    if(($count_tra_som_han + $count_tra_dung_han + $count_tra_qua_han) >0)
                                    {
                                        $v_tyle = ((($count_tra_som_han + $count_tra_dung_han)/($count_tra_som_han + $count_tra_dung_han + $count_tra_qua_han)) *100);   
                                        $v_tyle = round($v_tyle,3) .'%';
                                    }
                                    echo $v_tyle; 
                                ?>
                            </td>
                        </tr>
                             <?php  if(($i +1) == count($arr_all_report_data)): ?>
                                <?php  $j += 1;?>
                                <tr data-unit_code="<?php echo $v_cur_unit_code?>" data-total="true">
                                    <td class="center end left"><?php echo __('total'); ?> (<?php echo $j  ?> <?php echo __('units') ?>)</td>
                                    <td data-sum="1" data-format='1' data-index="1" class="center end"></td>
                                    <td data-sum="1" data-format='1' data-index="2"  class="center end"></td>
                                    <td data-sum="1" data-format='1' data-index="3"  class="center end"></td>
                                    <td data-sum="1" data-format='1' data-index="4"  class="center end"></td>
                                    <td data-sum="1" data-format='1' data-index="5"  class="center end"></td>
                                    <td data-sum="1" data-format='1' data-index="6"  class="center end"></td>
                                    <td data-sum="1" data-format='1' data-index="7"  class="center end"></td>
                                    <td data-sum="1" data-format='1' data-index="8"  class="center end"></td>
                                    <td data-sum="1" data-format='1' data-index="9"  class="center end"></td>
                                    <td data-sum="1" data-format='1' data-index="10"  class="center end"></td>
                                    <td data-sum="1" data-format='1' data-index="11"  class="center end"></td>
                                    <td data-sum="1" data-format='1' data-index="12"  class="center end"></td>
                                    <td data-sum="1" data-format='1' data-index="13"  class="center end"></td>
                                    <td data-sum="1" data-format='1' data-index="14"  class="center end"></td>
                                    <td data-sum="1" data-format='1' data-index="15"  class="center end"></td>
                                    <td data-sum="1" data-format='1' data-index="16"  class="center end"></td>
                                    <td class="center end right"></td>
                                </tr>
                                <?php
                                    $v_cur_unit_code = $v_unit_code;
                                    $j = 0;
                                ?>
                                <?php endif;?>
                        <?php  $j += 1;?>
                    <?php endfor; ?>
                </table>
            </div><!--  #result -->
        </div>
    </form>
</div><!-- .container-fluid -->
<div class="clear" style="height: 10px;"></div>
<script>
    
    $(document).ready(function(){
        $("#sel_spec_code").chained("#sel_village");
        var sel_spec_code = '<?php echo $v_current_fields?>';
        $("#sel_spec_code option[value='"+sel_spec_code+"']").attr('selected','true');
        
        var unit_code = '';
        $('.table_synthesis tr[data-unit_code]').each(function(){
            unit_code = $(this).attr('data-unit_code');
            fill_total_data(this,unit_code);
        });
        
        $('.table_synthesis tr').each(function(){
            $(this).find('td[data-format="1"]').each(function(){
                $(this).html(number_format(parseInt($(this).html()),0));
            });
        });
        
        $('.table_synthesis tr[data-total="true"]').each(function()
        {
            var sum_som_han     = $(this).find('td:eq('+ 7 +')').html() || 0;
            sum_som_han = sum_som_han.replace('.','');
            var sum_dung_han    = $(this).find('td:eq('+ 8 +')').html() || 0
            sum_dung_han = sum_dung_han.replace('.','');
            var sum_tra_tong_so = $(this).find('td:eq('+ 6 +')').html() || 0;
            sum_tra_tong_so = sum_tra_tong_so.replace('.','');
            
            var  ty_le = '';
            if(parseInt(sum_tra_tong_so) > 0)
            {
                ty_le = number_format(((parseInt(sum_som_han) + parseInt(sum_dung_han)) / parseInt(sum_tra_tong_so) * 100),2);
                ty_le = ty_le.toString() + '%';
            }
            else
            {
                ty_le = '---';
            }
            $(this).find('td:eq('+ 17 +')').html(ty_le);
        });
        
        rad_period_onclick($('input[name="rad_period"]:checked'));
    });
    function fill_total_data(tr,unit_code)
    {
        $(tr).find('td[data-sum="1"]').each(function(){
           var index = $(this).attr('data-index');
           var data = sum_data(unit_code,index);
           $(this).html(data);
        });
    }
    function sum_data(unit_code,index)
    {
        var data = 0;
        $('.table_synthesis tr[data-unit="'+unit_code+'"]').each(function(){
            data = data + parseInt($(this).find('td:eq('+index+')').html());
        });
        
        return data;
    }
    function number_format(n,d)
    {
        var number = String(n.toFixed(d).replace('.',','));
        return number.replace(/./g, function(c, i, a) {
                    return i > 0 && c !== "," && (a.length - i) % 3 === 0 ? "." + c : c;
                });
    }
    function btn_reset_onclick()
    {
        var f = document.frmMain;
        f.sel_village.value = ' ';
        f.sel_spec_code.value = '';
        f.sel_month.value = <?php echo (int) Date('m')?>;
        f.sel_year.value = '<?php echo (int)Date('Y')?>';
    }
    
    function rad_period_onclick(rad_period)
    {
        var rad_period_value = $(rad_period).val();
        var selected = '#period_' + rad_period_value;
        
        $('.period_option').hide();
        $(selected).show();
    }
    function sel_village_onchange()
    {
        var village_selected = $('#sel_village option:selected').attr('data-id')||0;
        $('#hdn_village_id').val(village_selected)
    }
   
</script>
