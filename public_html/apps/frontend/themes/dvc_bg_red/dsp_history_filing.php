<?php
$this->count_video = 0;
?>
<?php
$VIEW_DATA['arr_css']    = array( 'synthesis', 'component', 'lookup','single-page','detail_form');
$VIEW_DATA['arr_script'] = array('gp-slide');
$VIEW_DATA['title']                 = __('history filing');

$this->render('dsp_header', $VIEW_DATA, $this->theme_code);

$v_record_no           = get_request_var('txt_record_no','');
$v_begin_send_date     = get_request_var('txt_begin_send_date','');
$v_end_send_date       = get_request_var('txt_end_send_date','');
$v_fact_begin_date     = get_request_var('txt_fact_begin_date','');
$v_fact_end_date       = get_request_var('txt_fact_end_date','');
$v_detail_record_no    = get_request_var('hdn_record_no','');
$v_xml_data = isset($v_xml_data) ? $v_xml_data  : '</root>';
echo $this->hidden('XmlData', $v_xml_data);

$v_role = Session::get('citizen_role');
?>
<style>
    #frmMainFrom input,textarea,select
    {
        width: 100% !important;
        border: solid 0;
        border-bottom: dotted 1px #4E4D4D;
        padding: 0;
        margin: 0;
        line-height: 25px;
        background: transparent !important;
        font-weight: bold;
    }
    #frmMainFrom input[type="checkbox"],#frmMainFrom input[type="radio"]
    {
        width: auto !important;
        margin: 0;
        margin-left: 10px;
    }
    #frmMainFrom .blue
    {
        background: rgba(190, 72, 22, 0.78);
    }
     #frmMainFrom .blue h3
     {
         color: white;
        font-size: 16px;
        line-height: 30px;
        padding-left: 15px;
     }
</style>
<div class="col-md-12 content" id="single-article">
    <div class="col-md-12">
    <div id="main-content" class="col-md-12 block">
        <div class="col-md-12 box-content block">
            <div class="div_title_bg-title-top"></div>
            <div class="div_title">
                <div class="title-border-left"></div>
                <div class="title-content">
                  <label><?php echo __('history filing')?></label>
                </div>
            </div>
        </div>
        <?php if($v_role != -1):?>
        
        <form action="" name="frmViewDetail" id="frmViewDetail" method="POST">
            <?php
                echo $this->hidden('hdn_record_no',$v_detail_record_no);
            ?>    
        </form>
        <?php if($v_detail_record_no == ''):?>    
        <div id="box-filter" class="col-md-12 block" >
        <form name="frmMain" id="frmMain" action="" method="get">
            <div class="form-group">
                  <div class="row">
                    <div class="row col-md-6 block">
                        <div class="col-md-12 block">
                            <label class="col-md-4 block control-label"><b><?php echo __('record no')?></b>:</label>
                            <div class="col-md-6" >
                                <input type="text" name="txt_record_no" id="txt_record_no" 
                                       value="<?php echo $v_record_no; ?>"
                                       class="form-control" >
                            </div>
                        </div>
                    </div>
                  </div>
            </div>            
            
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6 block">
                        <label class="col-md-4 block control-label"><b><?php echo __('receive date')?></b>:</label>
                        <div class="col-md-5" >
                            <input type="text" name="txt_begin_send_date" id="txt_begin_send_date" 
                                   value="<?php echo $v_begin_send_date; ?>"
                                   class="form-control" 
                                   >
                        </div>
                        <div class="col-md-1 block">
                            <img width="35px" src="<?php echo CONST_SITE_THEME_ROOT."images/calendar.gif"; ?>" onclick="DoCal('txt_begin_send_date')">
                        </div>
                    </div>
                    <div class="col-md-6 block">
                        <label class="col-md-2 block control-label"><b><?php echo __('to date')?></b>:</label>
                        <div class="col-md-5" >
                            <input type="text" name="txt_end_send_date" id="txt_end_send_date" 
                                   value="<?php echo $v_end_send_date; ?>"
                                   class="form-control" 
                                   >
                        </div>
                         <div class="col-md-1 block">
                            <img width="35px" src="<?php echo CONST_SITE_THEME_ROOT."images/calendar.gif"; ?>" onclick="DoCal('txt_end_send_date')">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6 block">
                        <label class="col-md-4 block control-label"><b><?php echo __('fact receive date')?></b>:</label>
                        <div class="col-md-5" >
                            <input type="text" name="txt_fact_begin_date" id="txt_fact_begin_date"  
                                   value="<?php echo $v_fact_begin_date; ?>"
                                   class="form-control" 
                                    >
                        </div>
                        <div class="col-md-1 block">
                            <img width="35px" src="<?php echo CONST_SITE_THEME_ROOT."images/calendar.gif"; ?>" onclick="DoCal('txt_fact_begin_date')">
                        </div>
                    </div>
                    <div class="col-md-6 block">
                        <label class="col-md-2 block control-label"><b><?php echo __('to date')?></b>:</label>
                        <div class="col-md-5" >
                            
                            <input type="text" name="txt_fact_end_date" id="txt_fact_end_date" 
                                   value="<?php echo $v_fact_end_date; ?>"
                                   class="form-control" 
                                    >
                        </div>
                         <div class="col-md-1 block">
                            <img width="35px" src="<?php echo CONST_SITE_THEME_ROOT."images/calendar.gif"; ?>" onclick="DoCal('txt_fact_end_date')">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 " style="text-align: right">
                 <button type="submit" class="btn btn-primary btn-sm">
                            <span class="glyphicon glyphicon-search "></span>
                            <?php echo __('search') ?>
                        </button>
            </div>
            </form>          
        </div>
        <?php endif;  ?>
        <!--End Form search-->
        <div class="clear" style="height: 10px;"></div>
        <?php if($v_detail_record_no != ''):?>
        <link rel='stylesheet' href="<?php echo CONST_SITE_THEME_ROOT ?>css/table.css" />
        <div class="col-md-12 block" id="wrp-form">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#lookup_content" data-toggle="tab">Tiến độ xử lý</a>
                </li>
                <li>
                    <a href="#tab_form_detail" data-toggle="tab">Xem đơn</a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="lookup_content">
                    <div id="lookup_content"  style="">
                        <?php

                            echo $v_html_detail_record;
                        ?>
                    </div>
                </div>
                <div class="tab-pane" id="tab_form_detail">
                      <form name="frmMainFrom" id="frmMainFrom" action="" method="get">
                        <div class="col-md-12" id="from-detail">
                           <?php echo $v_html_detail_form; ?>
                           <script>
                            $(document).ready(function() {
                                   //Fill data
                                   var formHelper = new DynamicFormHelper('', '', document.frmMainFrom);
                                   formHelper.BindXmlData();
                                   try {
                                       ;
                                   } catch (e) {
                                       ;
                                   }
                                   $('#from-detail').find('.btndate').remove().end()
                                       .find('input,select,textarea')
                                       .attr('disabled','disabled')
                                       .css('background','#ECECEC');

                               });
                           </script>
                       </div>
                    </form>
                </div>
            </div>
        </div>
       
        
        <div class="btn-back" style="margin: 10px 0 20px 0;text-align: right">
            <button type="button" class="btn btn-back" onclick="window.history.back()"><?php echo __('go back')?></button>
        </div>
        <?php else: ;?>
        <div id="procedure">
            <table class="table table-bordered">
                <colgroup>
                    <col width="3%" />
                    <col width="10%" />
                    <col width="15%" />
                    <col width="30%" />
                    <col width="10%" />
                    <col width="10%" />
                    <col width="10%" />
                    <col width="18%" />
                </colgroup>
              <thead>
                <tr>
                  <th  class="bg_th"><?php echo __('#')?></th>
                  <th  class="bg_th"><?php echo __('record code')?></th>
                  <th  class="bg_th"><?php echo __('citizen name')?></th>
                  <th  class="bg_th"><?php echo __('Tên thủ tục')?></th>
                  <th  class="bg_th"><?php echo __('submitted date');?></th>
                  <th  class="bg_th"><?php echo __('fact receive date')?></th>
                  <th  class="bg_th"><?php echo __('return date');?> </th>
                  <th class="bg_th"><?php echo __('status')?></th>
                </tr>
              </thead>

              <tbody>
                <?php
                for($i =0;$i <sizeof($arr_all_record) ;$i++)
                {
                    $stt                    = $arr_all_record[$i]['RN'];
                    $v_record_code          = $arr_all_record[$i]['C_RECORD_NO'];
                    $v_citizen_name         = $arr_all_record[$i]['C_CITIZEN_NAME'];
                    $v_submitted_date       = $arr_all_record[$i]['C_SUBMITTED_DATE'];
                    $v_submitted_date       = jwDate::ddmmyyyy_to_yyyymmdd($v_submitted_date,true);
                    $v_xml_processing       = $arr_all_record[$i]['C_XML_PROCESSING'];
                    $v_record_type_name     = $arr_all_record[$i]['C_RECORD_TYPE_NAME'];
                    $v_deleted              = $arr_all_record[$i]['C_DELETED'];
                    $v_processing           = $arr_all_record[$i]['C_PROCESSING_RECORD'];
                    $v_status               = isset($arr_all_record[$i]['C_STATUS']) ?$arr_all_record[$i]['C_STATUS'] : '';
                    $activity_label         = $activity_class = $v_receive_date = $v_return_date = $v_fact_receive_date = null;
                    $v_receive_date         = $arr_all_record[$i]['C_RECEIVE_DATE'];
                    $v_return_date          = $arr_all_record[$i]['C_RETURN_DATE'];
                    $v_fact_receive_date    = isset($arr_all_record[$i]['C_FACT_RECEIVE_DATE']) ? $arr_all_record[$i]['C_FACT_RECEIVE_DATE'] : '';
                    
                    if($v_deleted == 1)
                    {
                            $activity_label =  __('Đã bị xóa');
                            $activity_class = "red";
                    }
                    else
                    {
                        $activity_label = $v_processing;
                        $activity_class = "green";
                    }
                    $html = "<tr>
                                <td >$stt</td>
                                <td> ";
                    if($v_deleted == 1 OR $v_processing == 'Một cửa chưa xác nhận')
                    {
                        $html .= $v_record_code;
                    }
                    else
                    {
                        $html .= "<a href='javascript:void(0)' onclick='record_onclick(this);'>
                                    $v_record_code
                                </a>";
                    }
                                    
                     $html .= " </td>
                                <td>$v_citizen_name</td>
                                <td>$v_record_type_name</td>
                                <td>$v_submitted_date</td>                                
                                <td>$v_receive_date</td>
                                <td>$v_return_date</td>
                                <td class='bold $activity_class' > $activity_label</td>
                        </tr>";
                     echo $html;
                }
                    echo $this->add_empty_rows(count($arr_all_record),_CONST_DEFAULT_ROWS_PER_PAGE,8);
                ?>
              </tbody>
            </table>
        </div>
          <!--button filter paging-->
                <div class="div_pagination" align="right">
                    <?php
                    $v_url_current = '';
                    if(trim($v_record_no)!= '')
                    {
                        $v_url_current .= "&txt_record_no=$v_record_no";
                    }
                    if(trim($v_begin_send_date)!= '')
                    {
                        $v_url_current .= "&txt_begin_send_date=$v_begin_send_date";
                    }
                    if(trim($v_end_send_date)!= '')
                    {
                        $v_url_current .= "&txt_end_send_date=$v_end_send_date";
                    }
                    if(trim($v_fact_begin_date)!= '')
                    {
                        $v_url_current .= "&txt_fact_begin_date=$v_fact_begin_date";
                    }
                    if(trim($v_fact_end_date)!= '')
                    {
                        $v_url_current .= "&txt_fact_end_date=$v_fact_end_date";
                    }
                    
                    $v_url = build_url_single_account_citizen(1,true);
                    $v_total_record = isset($arr_all_record[0]['TOTAL_RECORD']) ? $arr_all_record[0]['TOTAL_RECORD'] : 0;
                    $n = ceil($v_total_record / _CONST_DEFAULT_ROWS_PER_PAGE);
                    if (!$n) {
                        $n = 1;
                    }
                    $filter_page_no = get_request_var('page', 1);
                    $first_page = 1;
                    $previous_page = $filter_page_no <= 1 ? 1 : $filter_page_no - 1;
                    $next_page = $filter_page_no == $n ? $n : $filter_page_no + 1;
                    $last_page = $n;
                    $i = $filter_page_no <= 2 ? 1 : $filter_page_no - 1;
                    ?>
                    <?php if ($n > 1): ?>
                        <ul class="pagination">
                            <li data-val="1">
                                <a href="<?php echo build_url_single_account_citizen($first_page,true).$v_url_current; ?>" title="<?php echo __("first page") ?>">
                                    <?php echo __("first page") ?>
                                </a>
                            </li>
                                <?php
                                for ($i, $j = 1; $i <= $n && $j <= 9; $i++, $j++):
                                    if ($i == 1 or $i == $n) {
                                        continue;
                                    }
                                    ?>
                                    <li data-val="<?php echo $i; ?>">
                                        <a href="<?php echo build_url_single_account_citizen($i,true).$v_url_current ?>">
                                            <strong><?php echo $i; ?></strong>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                            <li data-val="<?php echo $n; ?>">
                                <a href="<?php echo build_url_single_account_citizen($last_page,true).$v_url_current ; ?>" title="<?php echo __("last page") ?>">
                                    <?php echo __("last page") ?>
                                </a>
                            </li>
                        </ul>
                    <?php endif; //n > 1 ?>
                </div><!--end pagging-->
        <?php  endif;?>
        
        <?php  
            else:
                echo '<div style="height:200px;color:Red;text-align:center">
                        <h1 style="margin-top:100px">Tài khoản này chưa được kích hoạt bạn không thể sử dụng chức năng này<h1>
                    </div>';
            endif;
        ?>
    </div>
    </div>
</div>
<!--End #Main-content-->
<script>
    
    $(document).ready(function(){
        $('#txt_begin_send_date,#txt_fact_begin_date,#txt_end_send_date,#txt_fact_end_date').datepicker({format:'dd/mm/yyyy'});
        $("#sel_record_type").chained("#sel_listtype"); 

        var page = parseInt($('#hdn_current_page').val());
        if(page == 0)
        {
            page = 1;
        }
        $('.pagination li[data-val='+page+']').addClass('active');
    });
    


function record_onclick(a)
{
    var action       = $('#frmViewDetail').attr('action');
    var record_no = $(a).html();
    record_no = record_no.trim();
    $('#hdn_record_no').val(record_no);
    
    $('#frmViewDetail').attr('action',action);
    $('#frmViewDetail').submit();
}
</script>
<?php
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;
$this->render('dsp_footer', $VIEW_DATA, $this->theme_code);
?>