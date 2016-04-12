<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');?>
<?php
    $v_month = get_post_var('sel_month',(int) Date('m'));
    $v_year = get_post_var('sel_year',(int) Date('Y'));
    
    $json_data = json_encode($arr_synthesis_chart);
?>
<style>
    #processing_chart
    {
        height: 400px;
    }
    #return_chart
    {
        height: 350px;
        
    }
    .tickLabel{
       font-weight: bold;
       font-size: 10pt;
    }
</style>
<script language="javascript" type="text/javascript" src="<?php echo SITE_ROOT.'public/flot/jquery.flot.js'?>"></script>
<script language="javascript" type="text/javascript" src="<?php echo SITE_ROOT.'public/flot/jquery.flot.categories.js'?>"></script>
<script language="javascript" type="text/javascript" src="<?php echo SITE_ROOT.'public/flot/jquery.flot.stack.js'?>"></script>
<?php echo $this->hidden('hdn_json_data',$json_data)?>
<script>
    var data_json = JSON.parse($('#hdn_json_data').val());
    var previousPoint = null, previousLabel = null;
    $.fn.UseTooltip = function () {
        $(this).bind("plothover", function (event, pos, item) {
            if (item) {
                if ((previousLabel != item.series.label) || (previousPoint != item.dataIndex))
                {
                    previousPoint = item.dataIndex;
                    previousLabel = item.series.label;
                    $("#tooltip").remove();

                    var x = item.datapoint[0]- item.datapoint[2] ;
                    var y = item.datapoint[1];
                    
                    var color = item.series.color;
                    showTooltip(item.pageX,
                    item.pageY,
                    color,
                    "<strong>" + item.series.label + "</strong><br>" + item.series.yaxis.ticks[y].label +
                    " : <strong>" + number_format(parseInt(x),0) + "</strong>");
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
    function create_chart(div_show,data,option)
    {
        $.plot(div_show, data, option);
    }
    function show_chart_info(div_note,data)
    {
        var label = '';
        var color = '';
        var note = '';
        var html  = '';
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
    
    $(document).ready(function(){
        var chart_processing = $("#processing_chart");
        var chart_return = $("#return_chart");
        
        var data_processing = [];
        
        var data_cho_tra  = [];
        var data_som_han  = [];
        var data_qua_han  = [];
        var data_dung_han = [];
        var ticks = [];
        
        
        //tao du lieu cho bieu do tien do
        for(var key in data_json)
        {
            data_cho_tra.push([data_json[key].C_COUNT_CHO_TRA_TRONG_KY,key]);
            data_som_han.push([data_json[key].C_COUNT_TRA_SOM_HAN,key]);
            data_qua_han.push([data_json[key].C_COUNT_TRA_QUA_HAN,key]);
            data_dung_han.push([data_json[key].C_COUNT_TRA_DUNG_HAN,key]);
            
            data_processing.push([data_json[key].C_COUNT_TIEP_NHAN,key]);
            ticks.push([key, data_json[key].C_NAME]);
        }
        
        var dataSet = [{ label: "<?php echo __('receive')?>", data: data_processing, color: "#EDC240", note:'Hồ sơ tiếp nhận trong tháng'}];
        var options = {
            series: {
                bars: {
                    show: true,
                    fillColor: "#D9D9D9"
                }
            },
            bars: {
                align: "center",
                barWidth: 0.2,
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
                ticks: ticks,
                
            },
            legend:false,
            grid: {
                          hoverable: true,
                          borderWidth: 1,
                          borderColor: "#545454",
                          autoHighlight: true
                      }
                  };
        create_chart(chart_processing,dataSet,options);
        $(chart_processing).UseTooltip();
        show_chart_info($('#processing_chart_note'),dataSet);
        
        //bieu do stack
         var stack = 0,
            bars = true,
            lines = false,
            steps = false;
        options.series.stack = stack;
        options.series.lines = {show: lines,fill: true,steps: steps};
        options.legend.show = false;
        dataSet = [{
                        label: '<?php echo __('pending')?>',
                        data: data_cho_tra,
                        color: '#EDC240',
                        note: '<?php echo __('record awaiting to receive citizen')?>'
                   },{
                       label: '<?php echo __('soon')?>',
                        data: data_som_han,
                        color: '#4DA74D',
                        note: '<?php echo __('pay records earlier than specified procedures')?>'
                   },{
                       label: '<?php echo __('on time')?>',
                        data: data_dung_han,
                        color: '#AFD8F8',
                        note: '<?php echo __('pay on time records specified procedures')?>'
                   },{
                       label: '<?php echo __('overdue')?>',
                        data: data_qua_han,
                        color: '#CB4B4B',
                        note: '<?php echo __('late payment records than the time prescribed procedures')?>'
                   }
                  ];
        
        create_chart(chart_return,dataSet,options);
        $(chart_return).UseTooltip();
        show_chart_info($('#return_chart_note'),dataSet);
    });
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
                    <div class="col-md-12">
                        <label class='col-md-1 control-label'><?php echo __('month')?></label>
                        <div class="col-md-2">
                            <select class='form-control' name='sel_month' id='sel_month'>
                                <?php for($i=1;$i<=12;$i++)
                                {
                                    $selected = ($i == $v_month)?'selected':'';
                                    echo "<option $selected value='$i'>$i</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <label class="col-md-1 control-label"><?php echo __('year')?></label>
                        <div class="col-md-2">
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
                        <label class="col-md-2 control-label"><?php echo __('so sánh')?></label>
                        <div class="col-md-4">
                            <select class="form-control" name='sel_compare_type' id='sel_compare_type'>
                                <option value="0">Theo đơn vị cấp huyện</option>
                                <option value="1">Theo đơn vị cấp xã</option>
                            </select>
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
            <div id="result" class="div-synthesis">
                <div class="div_title_bg-title-top"></div>
                <div class=" div_title">
                    <div class="title-content">
                        <label>
                            <?php echo __('receive chart') . " $v_month/$v_year" ?>
                        </label>
                    </div>
                </div>
                <div id="processing_chart"></div>
                <div id="processing_chart_note"></div>
                </div>
            </div><!--  #result -->
            <div class="clear" style="height: 10px;"></div>
        </div>
        <div class="col-md-12 block">
            <div id="result" class="div-synthesis">
                <div class="div_title_bg-title-top"></div>
                <div class=" div_title">
                    <div class="title-content">
                        <label>
                            <?php echo __('return chart'). " $v_month/$v_year" ;?>
                        </label>
                    </div>
                </div>
                <div id="return_chart"></div>
                <div id="return_chart_note"></div>
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
        f.sel_village.value = '';
        f.sel_spec_code.value = '';
        f.sel_month.value = '<?php echo (int)Date('m')?>';
        f.sel_year.value = '<?php echo (int)Date('Y')?>';
    }
</script>