<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');?>
<?php
    $arr_all_village  =  $arr_all_member['arr_all_village'];
    $arr_all_district =  $arr_all_member['arr_all_district'];  
    
    $v_spec_code = get_post_var('sel_spec_code','');
    $v_member_id = get_post_var('sel_village','');
    $v_month     = get_post_var('sel_month',(int) Date('m'));
    $v_year      = get_post_var('sel_year',(int) Date('Y'));
?>
<div class="col-md-12 block">
    <div class="div-synthesis">
        <div class="div_title_bg-title-top"></div>
        <div class="div_title">
             <div class="title-content">
                <label>
                    <?php echo __('status of statistical records')?>
                </label>
             </div>
        </div>
        <div style="overflow: hidden;margin-top: -6px;"></div>
        <div class="clear" style="height: 10px;"></div>
        <form class="form-horizontal" action="" method="post" name="frmMain" id="frmMain" role="form">
            <?php echo $this->hidden('controller',$this->get_controller_url());?>
            <div class="col-md-12">
            <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="control-label col-md-2"><?php echo __('field')?>:</label>
                            <div class="col-md-10">
                                <select name="sel_spec_code" id="sel_spec_code" class="form-control">
                                    <option value="">-- <?php echo __('all field')?> --</option>
                                    <?php foreach ($arr_all_spec as $v_code => $v_name): ?>
                                        <?php $v_selected = ($v_code == $v_spec_code) ? ' selected' : ''; ?>
                                        <option value="<?php echo $v_code; ?>" <?php echo $v_selected; ?>><?php echo $v_name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="control-label col-md-2"><?php echo __('units')?>:</label>
                            <div class="col-md-10">
                                <select name="sel_village" id="sel_village" class="form-control">
                                    <option value="">-- <?php echo __('all unit')?> --</option>
                                    <?php foreach($arr_all_district as $arr_district):
                                            $v_name       = $arr_district['C_NAME'];
                                            $v_id         = $arr_district['PK_MEMBER'];
                                            $v_selected   = ($v_id == $v_member_id)?'selected':'';
                                    ?>
                                    <option  <?php echo $v_selected?> value="<?php echo $v_id?>"><?php echo $v_name?></option>
                                    <?php foreach($arr_all_village as $key => $arr_village):
                                            $v_village_name = $arr_village['C_NAME'];
                                            $v_village_id   = $arr_village['PK_MEMBER'];
                                            $v_parent_id    = $arr_village['FK_MEMBER'];
                                            if($v_parent_id != $v_id)
                                            {
                                                continue;
                                            }
                                            $v_selected = ($v_village_id == $v_member_id)?'selected':'';
                                    ?>
                                    <option <?php echo $v_selected?> value="<?php echo $v_village_id?>"><?php echo '---- '.$v_village_name?></option>
                                    <?php 
                                        unset($arr_all_village[$key]);
                                        endforeach;
                                    ?>
                                    <?php endforeach;?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label class='col-md-2 control-label'><?php echo __('month')?>:</label>
                        <div class="col-md-6">
                            <select class='form-control' name='sel_month' id='sel_month'>
                                <?php for($i=1;$i<=12;$i++)
                                {
                                    $selected = ($i == $v_month)?'selected':'';
                                    echo "<option $selected value='$i'>$i</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="col-md-2 control-label"><?php echo __('year')?>:</label>
                        <div class="col-md-6">
                            <select class="form-control" name='sel_year' id='sel_year'>
                                <?php
                                    $v_max_year = $arr_year['C_MAX_YEAR'];
                                    $v_min_year = $arr_year['C_MIN_YEAR'];
                                    $v_loop = ($v_max_year - $v_min_year) + 1;
                                    for($i=0;$i<$v_loop;$i++)
                                    {
                                        $year = $v_min_year + $i;
                                        $selected = ($year == $v_year)?'selected':'';
                                        echo "<option $selected value='$year'>$year</option>";
                                    }
                                ?>

                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-actions" style="text-align: right;padding: 10px;">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <span class="glyphicon glyphicon-search "></span>
                        <?php echo __('search')?>
                    </button>
                    <button type="button" class="btn btn-sm" onclick="btn_reset_onclick();">
                        <i class="glyphicon glyphicon-remove "></i>
                        <?php echo __('clear')?>
                    </button>
                </div>
            </div>
            <div class="clear" style="height: 10px;"></div>
    </div><!--end synthesis-->
            
        </div> <!--end col md 12 block-->
        
    
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
                        <th class="top blue" rowspan="2"><?php echo __('unit name')?></th>
                        <th class="top orange" rowspan="2"><?php echo __('receive')?></th>
                        <th class="top orange" rowspan="2"><?php echo __('previous period')?></th>
                        <th class="top green" colspan="3"><?php echo __('processing')?></th>
                        <th class="last purple" colspan="4"><?php echo __('return')?></th>
                    </tr>
                    <tr>                
                        <th class="green"><?php echo __('total')?></th>
                        <th class="green"><?php echo __('not yet')?></th>
                        <th class="green"><?php echo __('overdue')?></th>
                        <th class="purple"><?php echo __('total')?></th>
                        <th class="purple"><?php echo __('soon')?></th>
                        <th class="purple"><?php echo __('on time')?></th>
                        <th class="right purple"><?php echo __('overdue')?></th>
                    </tr>
                    <?php 
                    $v_cur_unit_code = $arr_all_report_data[0]['C_UNIT_CODE'];
                    for ($i=0, $n=sizeof($arr_all_report_data); $i<$n; $i++)
                    {
                        $arr_report_data = $arr_all_report_data[$i];
                        $v_unit_name     = $arr_report_data['C_NAME'];
                        $v_total_record  = $arr_report_data['C_COUNT_TONG_TIEP_NHAN_TRONG_THANG'];
                        $v_unit_code     = $arr_report_data['C_UNIT_CODE'];
                        $v_ky_truoc      = $arr_report_data['C_COUNT_KY_TRUOC'];
                        
                        $v_dang_thu_ly              = $arr_report_data['C_COUNT_DANG_THU_LY'];
                        $v_thu_ly_qua_han           = $arr_report_data['C_COUNT_THU_LY_QUA_HAN'];
                        
                        $v_da_tra_ket_qua_truoc_han = $arr_report_data['C_COUNT_DA_TRA_KET_QUA_TRUOC_HAN'];
                        $v_da_tra_ket_qua_dung_han  = $arr_report_data['C_COUNT_DA_TRA_KET_QUA_DUNG_HAN'];
                        $v_da_tra_ket_qua_qua_han   = $arr_report_data['C_COUNT_DA_TRA_KET_QUA_QUA_HAN'];
                        ?>
                        <?php 
                        if((($i+1) <= count($arr_all_report_data)) &&($v_unit_code != $v_cur_unit_code OR $i == 0)):
                            $v_cur_unit_code = $v_unit_code;
                        ?>
                            <tr data-unit_code="<?php echo $v_unit_code?>">
                                <td class="end"><?php echo __('total')?></td>
                                <td data-sum="1" data-format='1' data-index="1" class="center end"></td>
                                <td data-sum="1" data-format='1' data-index="2" class="center end"></td>
                                <td data-sum="1" data-format='1' data-index="3" class="center end"></td>
                                <td data-sum="1" data-format='1' data-index="4" class="center end"></td>
                                <td data-sum="1" data-format='1' data-index="5" class="center end"></td>
                                <td data-sum="1" data-format='1' data-index="6" class="center end"></td>
                                <td data-sum="1" data-format='1' data-index="7" class="center end"></td>
                                <td data-sum="1" data-format='1' data-index="8" class="center end"></td>
                                <td data-sum="1" data-format='1' data-index="9" class="center end"></td>
                            </tr>
                        <?php endif;?>
                        <tr data-unit="<?php echo $v_unit_code?>">
                            <td class="blue"><?php echo $v_unit_name;?></td>
                            <td data-format='1' class="orange center"><?php echo $v_total_record;?></td>
                            <td data-format='1' class="orange center"><?php echo $v_ky_truoc;?></td>
                            <td data-format='1' class="green center"><?php echo $v_dang_thu_ly?></td>
                            <td data-format='1' class="green center"><?php echo ($v_dang_thu_ly - $v_thu_ly_qua_han)?></td>
                            <td data-format='1' class="green center"><?php echo $v_thu_ly_qua_han?></td>
                            <td data-format='1' class="purple center"><?php echo ($v_da_tra_ket_qua_truoc_han + $v_da_tra_ket_qua_dung_han + $v_da_tra_ket_qua_qua_han)?></td>
                            <td data-format='1' class="purple center"><?php echo $v_da_tra_ket_qua_truoc_han;?></td>
                            <td data-format='1' class="purple center"><?php echo $v_da_tra_ket_qua_dung_han;?></td>
                            <td data-format='1' class="right purple center"><?php echo $v_da_tra_ket_qua_qua_han;?></td>
                        </tr>
                        <?php 
                    }//end for i
                    ?>
                </table>
            </div><!--  #result -->
        </div>
    </form>
</div><!-- .container-fluid -->
<div class="clear" style="height: 10px;"></div>
<script>
    $(document).ready(function(){
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
        insert_to_table_synthesis();
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
        f.sel_village.value = '';
        f.sel_spec_code.value = '';
        f.sel_month.value = '';
        f.sel_year.value = '<?php echo (int)Date('Y')?>';
    }
</script>