<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');?>
<?php
    $arr_all_village  =  $arr_all_member['arr_all_village'];
    $arr_all_district =  $arr_all_member['arr_all_district'];  
    
    $v_current_fields = get_request_var('sel_spec_code','');
    $sel_village = get_request_var('sel_village',0);
    $v_month     = get_post_var('sel_month',(int) Date('m'));
    $v_year      = get_post_var('sel_year',(int) Date('Y'));
    
    $json_data = json_encode($arr_synthesis_chart);
?>
<style>
    #chart_processing 
    {
        height: 400px;
    }
    #chart_return
    {
        height: 400px;
        
    }
</style>
<script language="javascript" type="text/javascript" src="<?php echo SITE_ROOT.'public/flot/jquery.flot.js'?>"></script>
<script language="javascript" type="text/javascript" src="<?php echo SITE_ROOT.'public/flot/jquery.flot.pie.js'?>"></script>
<?php echo $this->hidden('hdn_json_data',$json_data)?>
<script>
    $('document').ready(function(){
        $("#sel_spec_code").chained("#sel_village");
        var sel_spec_code = '<?php echo $v_current_fields?>';
        $("#sel_spec_code option[value='"+sel_spec_code+"']").attr('selected','true');
    });
    function sel_village_onchange()
    {
        var village_selected = $('#sel_village option:selected').attr('data-id')||0;
        $('#hdn_village_id').val(village_selected)
    }
    
    data_json = JSON.parse($('#hdn_json_data').val());
    var previousPoint = null, previousLabel = null;
    $.fn.UseTooltip = function () {
        $(this).bind("plothover", function (event, pos, item) {
            if (item) {
                if ((previousLabel != item.series.label) ||(previousPoint != item.dataIndex)) 
                {
                       previousPoint = item.dataIndex;
                       previousLabel = item.series.label;
                       $("#tooltip").remove();
                       
                       var color = item.series.color;
                       showTooltip(pos.pageX,
                       pos.pageY,
                       color,
                       "<strong>" + item.series.label + "</strong><br>" +
                       " : <strong>" + number_format(parseInt(item.series.data[0][1]),0) + "</strong>");
                }
            } else {
                $("#tooltip").remove();
                previousPoint = null;
            }
        });
    };
 
    function showTooltip(x, y, color, contents) {
        $('<div id="tooltip">' + contents + '</div>').css({
            position: 'absolute',
            display: 'none',
            top: y - 10,
            left: x + 10,
            border: '2px solid ' + color,
            padding: '3px',
            'font-size': '10pt',
            'border-radius': '5px',
            'background-color': '#fff',
            'font-family': 'Tahoma',
            opacity: 0.9
        }).appendTo("body").fadeIn(200);
    }
    
    $(document).ready(function(){
        var data_return = [ {
                                label: "<?php echo __('return')?> - <?php echo __('soon')?>",
                                data: data_json.C_COUNT_TRA_SOM_HAN,
                                color: '#4DA74D',
                                note: '<?php echo __('pay records earlier than specified procedures')?>'
                            },  {
                                label: "<?php echo __('return')?> - <?php echo __('on time')?>",
                                data: data_json.C_COUNT_TRA_DUNG_HAN,
                                color: '#AFD8F8',
                                note: '<?php echo __('pay on time records specified procedures')?>'
                            },  {
                                label: "<?php echo __('return')?> - <?php echo __('overdue')?>",
                                data: data_json.C_COUNT_TRA_QUA_HAN,
                                color: '#CB4B4B',
                                note: '<?php echo __('late payment records than the time prescribed procedures')?>'
                            },  {
                                label: "<?php echo __('pending')?>",
                                data: data_json.C_COUNT_CHO_TRA_TRONG_KY,
                                color: '#EDC240',
                                note: '<?php echo __('records are pending results')?>'
                            }
                        ];
        var data_processing = [  {
                                label: "<?php echo __('processing')?> - <?php echo __('on time')?>",
                                data: data_json.C_COUNT_THU_LY_CHUA_DEN_HAN,
                                color: '#AFD8F8',
                                note: '<?php echo __('records are processing schedule as planned');?>'
                            },  {
                                label: "<?php echo __('processing')?> - <?php echo __('overdue')?>",
                                data: data_json.C_COUNT_THU_LY_QUA_HAN,
                                color: '#CB4B4B',
                                note: '<?php echo __('records are processing overdue compared to the return date');?>'
                            }
                        ];
        var chart_processing = $("#chart_processing");
        var chart_return = $("#chart_return");
        var options = {series: {
                        pie: { 
                                show: true
                        }
                },
                legend: {
                        show: false
                },
                grid: {
                          hoverable: true,
                      }
            };
        
        $.plot(chart_processing, data_processing, options);
        
        $.plot(chart_return, data_return, options);
        
        $(chart_return).UseTooltip();
        $(chart_processing).UseTooltip();
        
        show_chart_info($('#chart_processing_note'),data_processing);
        show_chart_info($('#chart_return_note'),data_return);
    });
    
    function show_chart_info(div_note,data)
    {
        var label = '';
        var color = '';
        var note = '';
        var value  = '';
        var html  = '';
        for (var key in data)
        {
            label = data[key].label;
            color = data[key].color;
            value = number_format(parseInt(data[key].data),0);
            note = data[key].note;
            if(note != '' && parseInt(value) > 0)
            {
                $(div_note).append('<div><label class="width_50" style="background-color: '+color+'">&nbsp;</label>&nbsp;'+value+' - '+note+'</div>');
            }
        }
    }
</script>

<div class="col-md-12 block">
    <div class="div-synthesis">
        <div class="div_title_bg-title-top"></div>
        <div class="div_title">
             <div class="title-content">
                <label>
                    <?php echo __('search')?>
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
                            <label class="control-label col-md-2"><?php echo __('units')?></label>
                            <div class="col-md-10">
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
                        </div>
                        <div class="col-md-6">
                            <label class="control-label col-md-2"><?php echo __('field')?></label>
                            <div class="col-md-10">
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
            </div><!--end synthesis-->
            <div class="clear" style="height: 10px;"></div>
        </div> <!--end col md 12 block-->
    
        <div class="col-md-12 block">
            <div id="result" class="div-synthesis" style="float: left;width: 98%;">
                <div class="div_title_bg-title-top"></div>
                <div class=" div_title">
                    <div class="title-content">
                        <label>
                            <?php echo __('progress chart') . " $v_month/$v_year" ?>
                        </label>
                    </div>
                </div>
                <div class="Row col-md-12">
                    <div id="chart_processing" class="col-md-7"></div>
                    <div id="chart_processing_note" class="col-md-5 block"></div>
                </div>
            </div><!--  #result -->
            <div class="clear" style="height: 10px;"></div>
        </div>
        <div class="col-md-12 block">
            <div id="result" class="div-synthesis" style="float: left;width: 98%;">
                <div class="div_title_bg-title-top"></div>
                <div class=" div_title">
                    <div class="title-content">
                        <label>
                            <?php echo __('return chart') . " $v_month/$v_year"?>
                        </label>
                    </div>
                </div>
                <div style="overflow: hidden;margin-top: -6px;"></div>
                <div class="Row col-md-12">
                    <div id="chart_return" class="col-md-7"></div>
                    <div id="chart_return_note" class="col-md-5 block"></div>
                </div>
            </div><!--  #result -->
            <div class="clear" style="height: 10px;"></div>
        </div>
    </form>
</div><!-- .container-fluid -->
<script>
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
        f.sel_month.value = '<?php echo (int)Date('m')?>';
        f.sel_year.value = '<?php echo (int)Date('Y')?>';
    }
</script>