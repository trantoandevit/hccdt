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
    <div class="div-synthesis" style="margin-top: 0">
        <div class="div_title_bg-title-top"></div>
        <div class="div_title">
            <div class="title-content">
                <label>
                    <?php echo __('Thống kê tình trạng gải quyết hồ sơ theo lĩnh vực')?>
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
                        <label class="control-label col-md-2" style="white-space: nowrap"><?php echo __('field')?></label>
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
                        <label class="control-label col-md-2" style=""><?php echo __('units')?></label>
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
                        <label class='col-md-2 control-label'><?php echo __('month')?></label>
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
                        <label class="col-md-2 control-label"><?php echo __('year')?></label>
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
                        <th class="first blue" style="width: 20%;" rowspan="2"><?php echo __('Tên lĩnh vực'); ?></th>
                        <th class="top orange" colspan="3"><?php echo __('receive'); ?></th>
                        <th class="top green" colspan="4"><?php echo __('outstanding'); ?></th>
                        <th class="top purple" colspan="8"><?php echo __('has settled'); ?></th>
                    </tr>
                    <tr>    
                        <th class="orange"><?php echo __('cumulative'); ?></th>
                        <th class="orange"><?php echo __('previous period'); ?></th>
                        <th class="orange"><?php echo __('in month'); ?></th>

                        <th class="green"><?php echo __('total'); ?></th>
                        <th class="green"><?php echo __('not yet'); ?></th>
                        <th class="green"><?php echo __('overdue'); ?></th>
                        <th class="green"><?php echo __('supplements'); ?></th>
                        
                        <th class="purple"><?php echo __('total'); ?></th>
                        <th class="purple"><?php echo __('soon'); ?></th>
                        <th class="purple"><?php echo __('on time'); ?></th>
                        <th class="purple"><?php echo __('overdue'); ?></th>
                        <th class="purple"><?php echo __('Đang chờ trả'); ?></th>
                        
                        <th class="purple"><?php echo __('reject'); ?></th>
                        <th class="purple"><?php echo __('citizens withdraw'); ?></th>
                        <th class="purple"><?php echo __('the rate of soon and on time'); ?></th>
                        
                    </tr>
                            <?php
                            $j = 0;
                            ?>
                            <?php
                            $v_current_spec = '';
                            for ($i =0;$i <count($arr_all_report_data);$i ++):                                
                                $arr_value = $arr_all_report_data[$i];                               
                                $v_member_name = $arr_value['C_NAME'];

                                $v_tong_tiep_nhan_thang= $arr_value['C_COUNT_TONG_TIEP_NHAN_TRONG_THANG'];
                                
                                $v_luy_ke = $arr_value['C_COUNT_LUY_KE'];
                                
                                $v_dang_thu_ly             = $arr_value['C_COUNT_DANG_THU_LY'];
                                $v_dang_cho_tra_ket_qua    = $arr_value['C_COUNT_DANG_CHO_TRA_KET_QUA'];
                                $v_dang_thu_ly_dung_tien_do= $arr_value['C_COUNT_DANG_THU_LY_DUNG_TIEN_DO'];
                                $v_dang_thu_ly_cham_tien_do= $arr_value['C_COUNT_DANG_THU_LY_CHAM_TIEN_DO'];
                                $v_thu_ly_qua_han          = $arr_value['C_COUNT_THU_LY_QUA_HAN'];
                                $v_thue                    = $arr_value['C_COUNT_THUE'];

                                $v_da_tra_ket_qua_truoc_han= $arr_value['C_COUNT_DA_TRA_KET_QUA_TRUOC_HAN'];
                                $v_da_tra_ket_qua_dung_han = $arr_value['C_COUNT_DA_TRA_KET_QUA_DUNG_HAN'];
                                $v_da_tra_ket_qua_qua_han  = $arr_value['C_COUNT_DA_TRA_KET_QUA_QUA_HAN'];
                                $v_da_tra_ket_qua          = $arr_value['C_COUNT_DA_TRA_KET_QUA'];

                                $v_cong_dan_rut= $arr_value['C_COUNT_CONG_DAN_RUT'];
                                $v_tu_choi     = $arr_value['C_COUNT_TU_CHOI'];
                                $v_bo_sung     = $arr_value['C_COUNT_BO_SUNG'];
                                $v_unit_code   = $arr_value['C_UNIT_CODE'];
                                
                                $v_spec_code = $arr_value['C_SPEC_CODE'];
                                $v_spec_name = $arr_value['C_SPEC_NAME'];
                                
                                $v_tong_da_tra          = $v_da_tra_ket_qua_truoc_han + $v_da_tra_ket_qua_dung_han + $v_da_tra_ket_qua_qua_han;
                                $v_tong_dang_giai_quyet = $v_dang_thu_ly + $v_bo_sung;
                                $v_ky_truoc             = ($v_tong_dang_giai_quyet + $v_tong_da_tra + $v_tu_choi + $v_cong_dan_rut + $v_dang_cho_tra_ket_qua) - $v_tong_tiep_nhan_thang;
                                $v_ky_truoc             = ($v_ky_truoc >0) ?  $v_ky_truoc : 0; 
                                $v_ty_le = 0;
                                if ($v_tong_da_tra > 0)
                                {
                                        $v_ty_le = (($v_da_tra_ket_qua_truoc_han + $v_da_tra_ket_qua_dung_han) / $v_tong_da_tra) * 100;
                                }
                                $v_ty_le = ($v_ty_le == 0)?'--':number_format($v_ty_le, 2,',','.').'%';
                            
                            ?>
                    <?php  if($v_spec_code != $v_current_spec ):?>
                        <?php
                            $v_current_spec = $v_spec_code;
                            $j = 0;
                        ?>
                        <tr data-spec-code="<?php echo $v_current_spec?>" data-total="true">
                            <td class="center end left" style="width: 23%"><?php echo $v_spec_name ?></td>
                            <td data-sum="1" data-format='1' data-index="1" class="center end"  style="width: 5%"></td>
                            <td data-sum="1" data-format='1' data-index="2"  class="center end" style="width: 5%"></td>
                            <td data-sum="1" data-format='1' data-index="3"  class="center end" style="width: 5%"></td>
                            <td data-sum="1" data-format='1' data-index="4"  class="center end" style="width: 5%"></td>
                            <td data-sum="1" data-format='1' data-index="5"  class="center end" style="width: 5%"></td>
                            <td data-sum="1" data-format='1' data-index="6"  class="center end" style="width: 5%"></td>
                            <td data-sum="1" data-format='1' data-index="7"  class="center end" style="width: 5%"></td>
                            <td data-sum="1" data-format='1' data-index="8"  class="center end" style="width: 5%"></td>
                            <td data-sum="1" data-format='1' data-index="9"  class="center end" style="width: 5%"></td>
                            <td data-sum="1" data-format='1' data-index="10"  class="center end" style="width: 5%"></td>
                            <td data-sum="1" data-format='1' data-index="11"  class="center end" style="width: 5%"></td>
                            <td data-sum="1" data-format='1' data-index="12"  class="center end" style="width: 5%"></td>
                            <td data-sum="1" data-format='1' data-index="13"  class="center end" style="width: 5%"></td>
                            <td data-sum="1" data-format='1' data-index="14"  class="center end"></td>
                            <td class="center end right"></td>
                        </tr>
                       
                    <?php endif;?>
                        <tr class="<?php echo ($i % 2) ? 'xam' : ''; ?>"  data-unit="<?php echo $v_spec_code?>">
                            <td   class="blue left">
                                <?php echo  $v_member_name ?>
                            </td>
                            <td data-format='1' class="orange center bold">
                                <?php echo $v_luy_ke ?>
                            </td>
                            <td data-format='1'  class="orange center">
                                <?php echo $v_ky_truoc; ?>
                            </td>
                            <td data-format='1'  class="orange center">
                                <?php echo $v_tong_tiep_nhan_thang ?>
                            </td>
                            
                            <td data-format='1' class="green center">
                                <?php echo $v_dang_thu_ly + $v_bo_sung ?>
                            </td>
                            <td data-format='1'class="green center">
                                <?php echo ($v_dang_thu_ly - $v_thu_ly_qua_han) ?>
                            </td>
                            <td data-format='1'class="green center">
                                <?php echo $v_thu_ly_qua_han ?>
                            </td>
                            <td data-format='1'class="green center">
                                <?php echo $v_bo_sung ?>
                            </td>
                            
                            <td data-format='1' class="purple center bold">
                                <?php echo (        $v_da_tra_ket_qua_truoc_han 
                                                +   $v_da_tra_ket_qua_dung_han 
                                                +   $v_da_tra_ket_qua_qua_han 
                                                +   $v_dang_cho_tra_ket_qua
                                                +   $v_cong_dan_rut
                                                +   $v_tu_choi
                                            ) ?>
                            </td>
                            <td  data-format='1' class="purple center">
                                <?php echo $v_da_tra_ket_qua_truoc_han ?>
                            </td>
                            <td data-format='1' class="purple center">
                                <?php echo $v_da_tra_ket_qua_dung_han ?>
                            </td>
                            <td data-format='1' class="purple center">
                                <?php echo $v_da_tra_ket_qua_qua_han ?>
                            </td>
                            <td data-format='1' class="purple center">
                                <?php echo $v_dang_cho_tra_ket_qua ?>
                            </td>
                            
                            <td data-format='1' class="purple center">
                                <?php echo $v_tu_choi ?>
                            </td>
                            <td data-format='1'class="purple center">
                                <?php echo $v_cong_dan_rut ?>
                            </td>
                            <td class="purple right center">
                                <?php echo $v_ty_le ?>
                            </td>
                        </tr>
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
        var unit_code = '';
        
        $('.table_synthesis tr[data-spec-code]').each(function(){
            unit_code = $(this).attr('data-spec-code');
            fill_total_data(this,unit_code);
        });
        
        $('.table_synthesis tr').each(function(){
            $(this).find('td[data-format="1"]').each(function(){
                $(this).html(number_format(parseInt($(this).html()),0));
            });
        });
        
        $('.table_synthesis tr[data-total="true"]').each(function()
        {
            var sum_som_han     = $(this).find('td:eq('+ 9 +')').html() || 0;
            var sum_dung_han    = $(this).find('td:eq('+ 10 +')').html() || 0
            var sum_qua_han     = $(this).find('td:eq('+ 11 +')').html() || 0;
            var sum_tong_tra    = parseInt(sum_som_han) + parseInt(sum_dung_han) + parseInt(sum_qua_han);
            var  ty_le = '';
            if(parseInt(sum_tong_tra) > 0)
            {
                ty_le = number_format(((parseInt(sum_som_han) + parseInt(sum_dung_han)) / parseInt(sum_tong_tra) * 100),2);
            }
            if(ty_le == '')
            {
                ty_le = '--';
            }
            else
            {
                ty_le = ty_le.toString() + '%';
            }
            
            $(this).find('td:eq('+ 15 +')').html(ty_le);
        });
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
        f.sel_month.value = <?php echo (int) Date('m')?>;
        f.sel_year.value = '<?php echo (int)Date('Y')?>';
    }
</script>