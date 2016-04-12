<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');?>
<?php
    $v_month = get_post_var('sel_month',(int) Date('m'));
    $v_year = get_post_var('sel_year',(int) Date('Y'));
?>
<html>
    <head>
        <!--style-->
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>apps/frontend/slideshow.css" type="text/css"  />
        <!--jquery-->
        <script type="text/javascript" src="<?php echo SITE_ROOT.'public/bootstrap/'?>js/jquery.min.js"></script>
        <!--bootstrap-->
        <link rel="stylesheet" href="<?php echo SITE_ROOT.'public/bootstrap'?>/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo SITE_ROOT.'public/bootstrap'?>/css/bootstrap-theme.min.css">
        <script src="<?php echo SITE_ROOT.'public/bootstrap'?>/js/bootstrap.min.js"></script>
        <!--flot chart-->
        <script language="javascript" type="text/javascript" src="<?php echo SITE_ROOT.'public/flot/jquery.flot.js'?>"></script>
        <script language="javascript" type="text/javascript" src="<?php echo SITE_ROOT.'public/flot/jquery.flot.categories.js'?>"></script>
        <script language="javascript" type="text/javascript" src="<?php echo SITE_ROOT.'public/flot/jquery.flot.stack.js'?>"></script>
        <script language="javascript" type="text/javascript" src="<?php echo SITE_ROOT.'public/flot/jquery.flot.pie.js'?>"></script>
        <style>
            .tickLabel{
               font-weight: bold;
               font-size: 10pt;
            }
        </style>
    </head>
    <body>
        <?php echo $this->hidden('hdn_all_progress_fields',  json_encode($arr_all_progress_fields))?>
        <?php echo $this->hidden('hdn_json_data_village',  json_encode($arr_all_village))?>
        <script>
            var data_all_progress_fields           = JSON.parse($('#hdn_all_progress_fields').val());
            var data_json_village   = JSON.parse($('#hdn_json_data_village').val());
            var interval;
            var interval_status = 1;
            var index = 1;
            //tao bieu do 
            function create_chart(div_show,data,option)
            {
                $.plot(div_show, data, option);
            }
            //function tao bieu do pie
            function create_pie_chart(div_show,data)
            {
                var options = {
				series: {
					pie: { 
						show: true,
						radius: 1,
						label: {
							show: true,
							radius: 3/4,
							formatter: labelFormatter,
							background: {
								opacity: 0
							}
						}
					}
				},
				legend: {
					show: false
				}
			};
                create_chart(div_show,data,options);
            }
            
            //formart label
            function labelFormatter(label, series) 
            {
		return "<div style='font-size:8pt; background-color: transparent;text-align:center; padding:0px; color:white;'>" 
                        + label + "<br/>" + Math.round(series.percent) + "% - " + number_format(series.data[0][1],0) + "\n\
                        </div>";
            }
            
            //function tao bieu do stack
            function create_stack_chart(div_show,data,ticks,div_note)
            {
                var stack = 0,
                    bars = true,
                    lines = false,
                    steps = false;
    
                var options = {
                            series: {
                                bars: {
                                    show: true,
                                    fillColor: "#D9D9D9"
                                }
                            },
                            bars: {
                                align: "center",
                                barWidth: 0.5,
                                horizontal: true,
                                fillColor: { colors: [{ opacity: 0.5 }, { opacity: 1}] },
                                lineWidth: 1
                            },
                            xaxis: {
                                axisLabelPadding: 10,
                                color: "#D9D9D9"
                            },
                            yaxis: {
                                axisLabelPadding: 3,
                                axisLabelFontSizePixels: 11,
                                axisLabelFontFamily: 'Tahoma',
                                ticks: ticks
                            },
                            legend: {
                                noColumns: 0,
                                labelBoxBorderColor: "#858585",
                                position: "ne"
                            },
                            grid: {
                                          borderWidth: 1,
                                          borderColor: "#545454",
                                          autoHighlight: true
                                      }
                                  };
                options.series.stack = stack;
                options.series.lines = {show: lines,fill: true,steps: steps};
                options.legend.show = false;
                
                //tao bieu do
                create_chart(div_show,data,options);
                //tao note
                var label = '';
                var color = '';
                var note = '';
                for (var key in data)
                {
                    label = data[key].label;
                    color = data[key].color;
                    note = data[key].note;
                    if(note != '')
                    {
                        $(div_note).append('<div><label class="width_50" style="background-color: '+color+'">&nbsp;</label>&nbsp;'+label+': '+note+'</div>');
                    }
                }
            }
            function show_slide(div_slide)
            {
                if($(div_slide).find('.item').length == index)
                {
                    index = 0;
                }
                $(div_slide).find('.item').hide(2000);
                $(div_slide).find('.item:eq('+index+')').show(2000);
                index++;
            }
            //format number
            function number_format(n,d)
            {
                var number = String(n.toFixed(d).replace('.',','));
                return number.replace(/./g, function(c, i, a) {
                            return i > 0 && c !== "," && (a.length - i) % 3 === 0 ? "." + c : c;
                        });
            }
        </script>
        
        <div id="myCarousel" >
            <div id="carousel-inner">
                <div class="benner" style="width: 100%;text-align: center;padding: 0 15px 0 15px;background: rgb(254, 232, 185); ">
                    <img src="<?php echo FULL_SITE_ROOT.'public/images/DuAnTangCuongCCHC.png' ?>" width="100%">
                </div>
                <div class="item">
                    <div class="div-synthesis">
                        <div class="div_title">
                            <img src="<?php echo $this->image_directory . 'icon_title.gif'?>" />
                            <label>
                                Bảng tổng hợp giải quyết thủ tục hành chính tháng <?php echo Date('m').'/'.Date('Y')?>
                            </label>
                        </div>
                        <div>
                    <table class="table_synthesis">
                    <tr>
                        <th class="first blue"  rowspan="2"><?php echo __('unit name'); ?></th>
                        <th class="top green" colspan="2"><?php echo __('receive'); ?></th>
                        <th class="top orange" colspan="3"><?php echo __('outstanding'); ?></th>
                        <th class="top purple" colspan="4"><?php echo __('return'); ?></th>
                        <th class="top orange" colspan="2" ><?php echo __('Tạm dừng'); ?></th>
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
                        
                        <th class="purple"><?php echo __('total'); ?></th>
                        <th class="purple"><?php echo __('soon'); ?></th>
                        <th class="purple"><?php echo __('on time'); ?></th>
                        <th class="purple"><?php echo __('overdue'); ?></th>
                        
                        <th class="orange"><?php echo __('Bổ sung HS'); ?></th>
                        <th class="orange"><?php echo __('Thực hiện NVTC'); ?></th>
                        
                        <th class="red"><?php echo __('Từ chối'); ?></th>
                        <th class="red"><?php echo __('Công dân rút'); ?></th>
                        
                        <th class="purple"><?php echo __('total'); ?></th>
                        <th class="purple"><?php echo __('Trong kỳ'); ?></th>
                        <th class="purple"><?php echo __('Kỳ trước'); ?></th>
                    </tr>
                    <?php
                    $i = 1;
                    ?>
                    <?php
                        foreach ($arr_synthesis as $arr_value):
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

                            $count_tro_tra_trong_ky    = isset($arr_value['C_COUNT_CHO_TRA_KY_TRUOC']) ? $arr_value['C_COUNT_CHO_TRA_TRONG_KY'] : 0;
                            $count_tro_tra_truoc_ky    = isset($arr_value['C_COUNT_CHO_TRA_TRONG_KY']) ? $arr_value['C_COUNT_CHO_TRA_KY_TRUOC'] : 0;
                    ?>
                         <tr class="<?php echo ($i % 2) ? 'xam' : ''; ?>">
                            <td class="blue left" style="text-transform: uppercase">
                                <a class="blue show_liveboard" href="<?php echo $liveboard_link?>" target="_blank">
                                    <?php echo $i . '.  ' . $v_member_name ?>
                                </a>
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
                            <td class="purple center" >
                                <?php echo ($count_tra_som_han + $count_tra_dung_han + $count_tra_qua_han); ?>
                            </td>
                            
                            <td class="purple center" >
                                <?php echo $count_tra_som_han ?>
                            </td>
                            <td class="purple center" >
                                <?php echo $count_tra_dung_han ?>
                            </td>
                            <td class="purple center" >
                                <?php echo $count_tra_qua_han ?>
                            </td>
                            
                            <td class="orange center bold" >
                                <?php echo $count_bo_sung; ?>
                            </td>
                            <td class="orange center" >
                                <?php echo $count_nvtc ?>
                            </td>
                            
                            <td class="red center" >
                                <?php echo $count_tu_choi ?>
                            </td>
                            <td class="red center" >
                                <?php echo $count_con_dan_rut ?>
                            </td>
                            <td class="purple right center" >
                                <?php echo ($count_tro_tra_trong_ky + $count_tro_tra_truoc_ky) ?>
                            </td>
                            <td class="purple right center" >
                                <?php echo $count_tro_tra_trong_ky ?>
                            </td>
                            <td class="purple right center" >
                                <?php echo $count_tro_tra_truoc_ky ?>
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
                        <?php $i++; ?>
                    <?php endforeach; ?>
                    <tr>
                        <td class="center end left"><?php echo __('total'); ?> (<?php echo $i - 1 ?> <?php echo __('units') ?>)</td>
                        <td class="center end"></td>
                        <td class="center end"></td>
                        <td class="center end"></td>
                        <td class="center end"></td>
                        <td class="center end"></td>
                        <td class="center end"></td>
                        <td class="center end"></td>
                        <td class="center end"></td>
                        <td class="center end"></td>
                        <td class="center end"></td>
                        <td class="center end"></td>
                        <td class="center end"></td>
                        <td class="center end"></td>
                        <td class="center end"></td>
                        <td class="center end"></td>
                        <td class="center end"></td>
                        <td class="center end right"></td>
                    </tr>
                </table>
                            <script>
                                $(document).ready(function(){
                                    var obj_sum = {
                                                    count_tiep_nhan_ky_truoc                : parseInt(sum_col_table_synthesis(1)),
                                                    count_tiep_nhan_trong_ky                : parseInt(sum_col_table_synthesis(2)),

                                                    count_tong_dan_giai_quyet               : parseInt(sum_col_table_synthesis(3)),
                                                    count_dang_giai_quyet_chua_den_han      : parseInt(sum_col_table_synthesis(4)),
                                                    count_dang_giai_quyet_qua_han           : parseInt(sum_col_table_synthesis(5)),

                                                    count_tra_ket_qua_tong_so               : parseInt(sum_col_table_synthesis(6)),
                                                    count_tra_ket_qua_som_han               : parseInt(sum_col_table_synthesis(7)),
                                                    count_tra_ket_qua_dung_han              : parseInt(sum_col_table_synthesis(8)),
                                                    count_tra_ket_qua_qua_dung_han          : parseInt(sum_col_table_synthesis(9)),

                                                    count_bo_sung                           : parseInt(sum_col_table_synthesis(10)),
                                                    count_nvtc                              : parseInt(sum_col_table_synthesis(11)),

                                                    count_tu_choi                           : parseInt(sum_col_table_synthesis(12)),
                                                    count_con_dan_rut                       : parseInt(sum_col_table_synthesis(13)),

                                                    count_tro_tra_tong_so                   : parseInt(sum_col_table_synthesis(14)),
                                                    count_tro_tra_trong_ky                  : parseInt(sum_col_table_synthesis(15)),
                                                    count_tro_tra_ky_truoc                  : parseInt(sum_col_table_synthesis(16))
                                            };

                                    //tinh toan bang sum cua bang tong hop
                                    insert_to_table_synthesis(obj_sum);
                                });
                                function insert_to_table_synthesis(obj_sum)
                                {
                                    var tong_da_tra = obj_sum.count_bo_sung + obj_sum.count_nvtc + obj_sum.count_bo_sung;
                                    if(tong_da_tra == 0)
                                    {
                                        var tong_ty_le = '--';
                                    }
                                    else
                                    {
                                        var tong_ty_le = '0,00%'
                                        if(obj_sum.count_tra_ket_qua_tong_so >= 0)
                                        {
                                            tong_ty_le = ((obj_sum.count_tra_ket_qua_som_han + obj_sum.count_tra_ket_qua_dung_han) / obj_sum.count_tra_ket_qua_tong_so) * 100;
                                            tong_ty_le = number_format(tong_ty_le, 2) + '%';
                                        }
                                    }

                                    $('table.table_synthesis tr td.end').eq(1).html(number_format(obj_sum.count_tiep_nhan_ky_truoc,0));
                                    $('table.table_synthesis tr td.end').eq(2).html(number_format(obj_sum.count_tiep_nhan_trong_ky,0));

                                    $('table.table_synthesis tr td.end').eq(3).html(number_format(obj_sum.count_tong_dan_giai_quyet,0));
                                    $('table.table_synthesis tr td.end').eq(4).html(number_format(obj_sum.count_dang_giai_quyet_chua_den_han,0));
                                    $('table.table_synthesis tr td.end').eq(5).html(number_format(obj_sum.count_dang_giai_quyet_qua_han,0));

                                    $('table.table_synthesis tr td.end').eq(6).html(number_format(obj_sum.count_tra_ket_qua_tong_so,0));
                                    $('table.table_synthesis tr td.end').eq(7).html(number_format(obj_sum.count_tra_ket_qua_som_han,0));
                                    $('table.table_synthesis tr td.end').eq(8).html(number_format(obj_sum.count_tra_ket_qua_dung_han,0));
                                    $('table.table_synthesis tr td.end').eq(9).html(number_format(obj_sum.count_tra_ket_qua_qua_dung_han,0));

                                    $('table.table_synthesis tr td.end').eq(10).html(number_format(obj_sum.count_bo_sung,0));
                                    $('table.table_synthesis tr td.end').eq(11).html(number_format(obj_sum.count_nvtc,0));

                                    $('table.table_synthesis tr td.end').eq(12).html(number_format(obj_sum.count_tu_choi,0));
                                    $('table.table_synthesis tr td.end').eq(13).html(number_format(obj_sum.count_con_dan_rut,0));

                                    $('table.table_synthesis tr td.end').eq(14).html(number_format(obj_sum.count_tro_tra_tong_so,0));
                                    $('table.table_synthesis tr td.end').eq(15).html(number_format(obj_sum.count_tro_tra_trong_ky,0));
                                    $('table.table_synthesis tr td.end').eq(16).html(number_format(obj_sum.count_tro_tra_ky_truoc,0));

                                    $('table.table_synthesis tr td.end').eq(17).html(tong_ty_le);
                                    $('.result .val').text(tong_ty_le);

                                }
                                function sum_col_table_synthesis(index,searchVal,v_float)
                                {
                                    if(typeof searchVal == 'undefined')
                                    {
                                        searchVal = ' ';
                                    }

                                    if(typeof v_float == 'undefined')
                                    {
                                        v_float = 0;
                                    }

                                    var return_val = 0;
                                    var val = '';
                                    var selector = 'td:eq('+index+'):not(.end)';

                                    //tinh toan du lieu
                                    $('table.table_synthesis tr').find(selector).each(function(){
                                        val = $(this).html();
                                        //format number
                                        val = val.trim();
                                        val = val.replace(searchVal,val);

                                        if(v_float == 0)
                                        {
                                            $(this).html(number_format(parseInt(val),0) + searchVal);
                                            return_val = parseInt(val) + return_val;
                                        }
                                        else
                                        {
                                            $(this).html(number_format(parseFloat(val),2) + searchVal);
                                            return_val = parseFloat(val) + return_val;
                                        }
                                    });

                                    return return_val;
                                }
                            </script>
                        </div>
                    <div class="div-footer">
                    </div>
                    </div><!--end synthesis-->
                </div>
            <?php 
                $cur_unit_code = isset($synthesis_chart[0]['C_UNIT_CODE']) ? $synthesis_chart[0]['C_UNIT_CODE'] : '';
                $html          = '';               
            ?>
            <?php 
            foreach($arr_all_progress_fields as $synthesis_chart)
            {
                $v_unit_code     = $synthesis_chart['C_UNIT_CODE'];
                $v_unit_name     = $synthesis_chart['C_NAME'];                
                $v_count_village = $synthesis_chart['C_COUNT_VILLAGE'];
                if($cur_unit_code != $v_unit_code)
                {
                    $v_synthesis_chart_title = "Tình hình giải quyết TTHC";
                    $v_progress_chart_title = "Biểu đồ tiến độ";
                    $v_return_title = "Biểu đồ trả kết quả";
                    if($cur_unit_code == '')
                    {
                        $v_active = "active";
                    }
                    else
                    {
                        $v_active = '';
                    }
                    $html = '<div class="item '.$v_active.'">
                                    <div class="row">
                                        <div class="col-md-12 title">'. $v_unit_name . ' - Tháng ' . date('m') . '/' . date('Y').'</div>
                                        <div class="col-md-12 sub_title"><b>'. $v_synthesis_chart_title .'</b></div>    
                                    </div>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="clear"></div>
                                            <div class="chart" id="synthesis_chart_'.$v_unit_code.'"></div>
                                            <div id="synthesis_note_'.$v_unit_code.'"></div>
                                        </div>
                                        <div class="col-md-4"> 
                                            <div class="row pie_chart">
                                                <div class="col-md-12 sub_title"><b>'.$v_progress_chart_title.'</b></div>
                                                <div class="col-md-12 tiny_chart" id="progress_chart_'.$v_unit_code.'"></div>
                                            </div>
                                            <div class="row pie_chart">
                                                <div class="col-md-12 sub_title"><b>'.$v_return_title.'</b></div>
                                                <div class="col-md-12 tiny_chart" id="return_chart_'.$v_unit_code.'"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>';
                    if((int)$v_count_village > 0)
                    {
                        
                        $html .= '<div class="item">
                                <div class="row">
                                    <div class="col-md-12 title">
                                        Tình hình giải quyết TTHC các xã trực thuộc '.$v_unit_name.'
                                    </div>
                                    <div class="col-md-12 chart" id="synthesis_chart_'.$v_unit_code.'_of_village">
                                       
                                    </div>
                                     <div id="synthesis_note_'.$v_unit_code.'_of_village" ></div>
                                </div>
                            </div>';
                    }
                    $cur_unit_code = $v_unit_code;
                    echo $html;
                }
            }
           
            ?>
            </div><!--end carousel-inner-->
        </div><!--end my carousel-->
        <script>
            $(document).keypress(function(event){
               if(event.charCode == 32) 
               {
                   event.preventDefault();
                   if(interval_status == 1)
                   {
                        clearInterval(interval);
                        interval_status = 0;
                   }
                   else if(interval_status == 0)
                   {
                       show_slide('#myCarousel');
                       interval = setInterval(function(){
                            show_slide('#myCarousel');   
                        },8000);
                        interval_status = 1;
                   }
               }
            });
            $(document).ready(function() 
            {
                //tao bien luu tru thong tin cua stack
                var data_receive = [];
                var data_processing = [];
                var data_return = [];
                
                var ticks = [];
                var div_note = '';                
                var div_id = '';
                
                var data_return_soon = 0;
                var data_return_on_time = 0;
                var data_return_overdue = 0;
                
                //data set dung chung
                var dataSet = [];
                
                //tao bien luu tru thong tin cua pie
                var data_progress_notyet = 0;
                var data_progress_overdue = 0;
                
                var count_tong_tiep_nhan  = 0;
                var count_tong_thu_ly     = 0;
                var count_tong_tra        = 0;
                
                //tao du lieu cho bieu do tien do
                var unit_code_first = data_all_progress_fields[0].C_UNIT_CODE || '';
                var current_unit_code = '';
                for(var i=0;i<data_all_progress_fields.length;i++)
                {
                    //Tao bieu do stack
                    count_tong_tiep_nhan  = (parseInt(data_all_progress_fields[i].C_COUNT_KY_TRUOC) + parseInt(data_all_progress_fields[i].C_COUNT_TIEP_NHAN));
                    count_tong_thu_ly     = (parseInt(data_all_progress_fields[i].C_COUNT_THU_LY_CHUA_DEN_HAN) + parseInt(data_all_progress_fields[i].C_COUNT_THU_LY_QUA_HAN));
                    count_tong_tra        = (parseInt(data_all_progress_fields[i].C_COUNT_TRA_SOM_HAN) + parseInt(data_all_progress_fields[i].C_COUNT_TRA_DUNG_HAN) + parseInt(data_all_progress_fields[i].C_COUNT_TRA_QUA_HAN));
                    
                    data_progress_notyet  = data_progress_notyet + parseInt(data_all_progress_fields[i].C_COUNT_THU_LY_CHUA_DEN_HAN);
                    data_progress_overdue = data_progress_overdue + parseInt(data_all_progress_fields[i].C_COUNT_THU_LY_QUA_HAN);
                    if(typeof data_all_progress_fields[i + 1] != 'undefined')
                    {
                        current_unit_code     = data_all_progress_fields[i + 1].C_UNIT_CODE;
                    }
                    else
                    {
                        current_unit_code = '_';
                    }
                    data_receive.push([count_tong_tiep_nhan,i]);
                    data_processing.push([count_tong_thu_ly,i]);
                    data_return.push([count_tong_tra,i]);
                    ticks.push([i, data_all_progress_fields[i].C_SPEC_NAME]);
                    
                    data_return_soon = data_return_soon + parseInt(data_all_progress_fields[i].C_COUNT_TRA_SOM_HAN);
                    data_return_on_time  = data_processing + parseInt(data_all_progress_fields[i].C_COUNT_TRA_DUNG_HAN);
                    data_return_overdue      = data_return + parseInt(data_all_progress_fields[i].C_COUNT_TRA_QUA_HAN);
                    
                    //tao selector
                    if((unit_code_first != current_unit_code) ||(i == data_all_progress_fields.length - 1))
                    {
                        div_id   = '#synthesis_chart_' + unit_code_first;
                        div_note = '#synthesis_note_' + unit_code_first;
                        dataSet = [{
                                    label: '<?php echo __('receive')?>',
                                    data: data_receive,
                                    color: '#EDC240',
                                    note: '<?php echo __('receiving records in month')?>'
                               },{
                                   label: '<?php echo __('processing')?>',
                                    data: data_processing,
                                    color: '#4DA74D',
                                    note: '<?php echo __('records are processing in month')?>'
                               },{
                                   label: '<?php echo __('return')?>',
                                    data: data_return,
                                    color: '#AFD8F8',
                                    note: '<?php echo __('return reocrd in month')?>'
                               }
                              ];
                        //tao bieu do stack
                        create_stack_chart(div_id,dataSet,ticks,div_note);
                        
                        //tao bieu do (pie) Dang xu ly
                        div_id = '#progress_chart_' + unit_code_first;
                        dataSet = [ 
                                    {
                                        label: "<?php echo __('not yet') ?>",
                                        data: data_progress_notyet,
                                        color: '#4DA74D',
                                        note: '<?php echo __('records are processing schedule as planned'); ?>'
                                    },  {
                                        label: "<?php echo __('overdue') ?>",
                                        data: data_progress_overdue,
                                        color: '#CB4B4B',
                                        note: '<?php echo __('records are processing overdue compared to the return date'); ?>'
                                    }
                                ];
                        create_pie_chart(div_id,dataSet,ticks,div_note);
                        
                        //Build chart return
                        div_id = '#return_chart_' + unit_code_first;
                        dataSet = [ 
                                    {
                                        label: "<?php echo __('soon') ?>",
                                        data: data_return_soon,
                                        color: '#4DA74D',
                                        note: '<?php echo __('records are processing schedule as planned'); ?>'
                                    },  {
                                        label: "<?php echo __('on time') ?>",
                                        data: data_return_on_time,
                                        color: '#AFD8F8',
                                        note: '<?php echo __('records are processing overdue compared to the return date'); ?>'
                                    },  {
                                        label: "<?php echo __('overdue') ?>",
                                        data: data_return_overdue,
                                        color: '#CB4B4B',
                                        note: '<?php echo __('records are processing overdue compared to the return date'); ?>'
                                    }
                                ];
                        create_pie_chart(div_id,dataSet,ticks,div_note);
                        
                        data_receive = [];
                        data_processing = [];
                        data_return = [];
                        ticks = [];
                        unit_code_first = current_unit_code;                        
                        data_progress_notyet = data_progress_overdue= 0;
                        data_return_soon = data_return_on_time = data_return_overdue = 0;
                    }
                    
                }
                
                // #####################build chart village
                unit_code_first = data_json_village[0]['C_UNIT_CODE'] || '';
                //reset bien
                data_receive    = [];
                data_processing = [];
                data_return     = [];
                dataSet = [];
                ticks = [];
                i=1;
                
                for(var j=0;j < data_json_village.length;j++)
                {
                    count_tong_tiep_nhan  = parseInt(data_json_village[j].C_COUNT_KY_TRUOC) + parseInt(data_json_village[j].C_COUNT_TIEP_NHAN);
                    count_tong_thu_ly     = parseInt(data_json_village[j].C_COUNT_THU_LY_CHUA_DEN_HAN) + parseInt(data_json_village[j].C_COUNT_THU_LY_QUA_HAN);
                    count_tong_tra        = parseInt(data_json_village[j].C_COUNT_TRA_SOM_HAN)+ parseInt(data_json_village[j].C_COUNT_TRA_DUNG_HAN) + parseInt(data_json_village[j].C_COUNT_TRA_QUA_HAN);
                    
                    //du lieu bieu do stack
                    data_receive.push([count_tong_tiep_nhan,j] );
                    data_processing.push([count_tong_thu_ly ,j]);
                    data_return.push(count_tong_tra,j);

                    ticks.push([j, data_json_village[j].C_NAME]);                            

                    if(typeof data_json_village[j + 1] != 'undefined')
                    {
                        current_unit_code     = data_json_village[j + 1].C_UNIT_CODE;
                    }
                    else
                    {
                        current_unit_code = '_';
                    }
                    if((current_unit_code != unit_code_first) ||(j == data_json_village.length - 1))
                    {
                        //tao selector
                        div_id   = '#synthesis_chart_' + unit_code_first+ '_of_village';
                        div_note = '#synthesis_note_' + unit_code_first + '_of_village';
                        //neu so luong xa qua it mac dinh them 10 row
                        if(data_receive.length < 10)
                        {
                            var count = 10 - data_receive.length;
                            for(var loop=0; loop > (count*-1); loop--)
                            {
                                data_receive.push([0,loop]);
                                data_processing.push([0,loop]);
                                data_return.push([0,loop]);
                                ticks.push([loop,'']);
                            }
                        }
                        //tao dataset
                        dataSet = [{
                                    label: '<?php echo __('receive')?>',
                                    data: data_receive,
                                    color: '#EDC240',
                                    note: '<?php echo __('receiving records in month')?>'
                               }
                               ,{
                                   label: '<?php echo __('processing')?>',
                                    data: data_processing,
                                    color: '#4DA74D',
                                    note: '<?php echo __('records are processing in month')?>'
                               }
                               ,{
                                   label: '<?php echo __('return')?>',
                                    data: data_return,
                                    color: '#AFD8F8',
                                    note: '<?php echo __('return reocrd in month')?>'
                               }
                              ];
                        //Build chart village tao bieu do stack cap xa
                        create_stack_chart(div_id,dataSet,ticks,div_note);                        
                       
                        //reset bien
                        data_receive    = [];
                        data_processing = [];
                        data_return     = [];
                        dataSet = [];
                        ticks = [];
                        //gan dv moi
                        unit_code_first = current_unit_code;
                    }
                }
                $('#myCarousel').find('.item').hide();
                $('#myCarousel').find('.item:eq(0)').show();
                
                interval = setInterval(function(){
                    show_slide('#myCarousel');   
                },8000);
            });
        </script>
    </body>
</html>
