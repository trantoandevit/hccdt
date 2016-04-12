<?php
$VIEW_DATA['title']                 = $this->website_name;
$VIEW_DATA['v_banner']              = $v_banner;
$VIEW_DATA['arr_all_website']       = $arr_all_website;
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;
$VIEW_DATA['arr_css']               = array('component','table','synthesis','lookup');
$VIEW_DATA['arr_script']            = array();

$this->render('dsp_header', $VIEW_DATA, $this->theme_code);

//du lieu
$v_selected_member = get_request_var('unit','');
$v_record_no       = get_post_var('txt_record_no','');
$current_page      = get_request_var('page',1);

//arr member
$arr_all_village  =  $arr_all_member['arr_all_village'];
$arr_all_district =  $arr_all_member['arr_all_district'];

//du lieu chuan bi
$v_html = '';
$arr_list = array();

//dieu kien hien thi
if($v_record_no != '')
{
    //lay short code
    if($v_selected_member != '')
    {
        $v_short_code = $v_selected_member;
    }
    else
    {
        //ma cu
        $v_short_code = preg_replace('/([A-Z-0-9]+)-([A-Z-0-9]+)-([A-Z-0-9]+)/', '$2', $v_record_no);
        //ma moi (thay doi moi theo yeu cau TP BAC GIANG)
        if(!in_array($v_short_code, array_keys($arr_loockup_link)))
        {
            $v_short_code = preg_replace('/([A-Z]+).([A-Z-0-9]+).([0-9]+).([A-Z-0-9]+)/', '$1', $v_record_no);
        }
    }
    if($v_selected_member == '')
    {
        $v_selected_member = $v_short_code;
    }
    //lay url
    $v_url = $arr_loockup_link[$v_short_code]['C_LOOKUP_LINK'];
    
    //lay html
    $v_html = file_get_contents($v_url.$v_record_no);
    
    //neu html='' thong bao loi
    if($v_html == '')
    {
        $v_html ="<h3 style='margin:8px;'>". __('sorry, system not found record') . " $v_record_no</h3>";
    }
}
elseif($v_record_no == '' && $v_selected_member != '')
{
    //lay url danh sach hs 
    $v_url = $arr_loockup_link[$v_selected_member]['C_LOOKUP_LIST_LINK'];
    //arr du lieu
    $arr_list = json_decode(file_get_contents($v_url."$current_page&z=0"),true);
}
?>

<?php
    echo $this->hidden('hdn_current_page',$current_page);
    echo $this->hidden('hdn_unit',$v_selected_member);
?>

<div class="col-md-12 content">
    <div class="col-md-8 block">
        <div class="div-synthesis">
            <div class="div_title_bg-title-top"></div>
            <div class="div_title">
                <div class="title-border-left"></div>
                <div class="title-content">
                   <label>
                       <?php echo __('lookup')?>
                   </label>
                </div>
                <div class="title-border-right"></div>
            </div>
            <div style="overflow: hidden;margin-top: -6px;"></div>
            <form method="POST" id="frmMain" name="frmMain" action="<?php echo build_url_lookup(0);?>" class="form-horizontal" style="margin: 8px;">
                <div class="form-group">
                    <label class="control-label col-md-2"><strong><?php echo __('record no')?></strong></label>
                    <div class="col-md-8">
                        <input type="textbox" name="txt_record_no" id="txt_record_no" class="form-control" value="<?php echo $v_record_no?>"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><strong><?php echo __('receiving unit records')?></strong></label>
                    <div class="col-md-8 " >
                        <select class="form-control" name="sel_member" id="sel_member" onchange="sel_member_onchange(this);">
                            <option data-short_code='' value=''>-- Chọn đơn vị --</option>
                            <?php foreach($arr_all_district as $arr_district):
                                    $v_name       = $arr_district['C_NAME'];
                                    $v_id         = $arr_district['PK_MEMBER'];
                                    $v_short_code = $arr_district['C_SHORT_CODE'];
                                    $v_selected   = ($v_short_code == $v_selected_member)?'selected':'';
                            ?>
                            <option <?php echo $v_selected?> value="<?php echo $v_short_code?>"><?php echo $v_name?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                </div>
                <div class='action' style='text-align: right'>
                    <button type="submit" class="btn btn-primary btn-sm" onclick="lookup_onclick();">
                        <span class="glyphicon glyphicon-search "></span>
                        <?php echo __('search') ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php if(!empty($arr_list) OR $v_record_no != ''):?>
    <div class="col-md-12 block">
        <div class="div-synthesis">
            <div class="div_title_bg-title-top"></div>
            <div class="div_title">
                <div class="title-border-left"></div>
                <div class="title-content">
                   <label>
                       <?php echo __('record list')?>
                   </label>
                </div>
                <div class="title-border-right"></div>
            </div>
            <div style="overflow: hidden;margin-top: -6px;"></div>
            <div id="lookup_content">
                <?php if($v_record_no != ''):
                        echo $v_html;
                ?>
                <?php else:?>
                <table>
                    <tr>
                        <th class='bg_th' width='1%'>#</th>
                        <th class='bg_th' width='20%'><?php echo __('record no')?></th>
                        <th class='bg_th' width='25%'><?php echo __('citizen name')?></th>
                        <th class='bg_th' width='10%'><?php echo __('receive date');?></th>
                        <th class='bg_th' width='10%'><?php echo __('return date');?></th>
                        <th class='bg_th' width='*'><?php echo __('activity');?></th>
                    </tr>
                    <?php foreach($arr_list as $list): 
                            $v_record_no      = $list['C_RECORD_NO'];
                            $v_row_no         = $list['RN'];
                            $v_citizen_name   = $list['C_CITIZEN_NAME'];
                            $v_receive_date   = jwDate::yyyymmdd_to_ddmmyyyy($list['C_RECEIVE_DATE']);
                            $v_return_date    = jwDate::yyyymmdd_to_ddmmyyyy($list['C_RETURN_DATE']);
                            $v_xml_processing = $list['C_XML_PROCESSING'];
                            $v_activity       = $list['C_ACTIVITY'];
                            $activity_label = '';
                            
                            if($v_activity == 1)
                            {
                                $dom = simplexml_load_string($v_xml_processing);
                                $result = $dom->xpath('//next_task/@group_name');
                                $activity_label = (string) $result[0] .' '. __('processing');
                                $activity_class = "green";
                            }
                            elseif($v_activity == 2)
                            {
                                $activity_label = __('return');
                                $activity_class = "blue";
                            }
                            elseif($v_activity == 3)
                            {
                                $activity_label = __('reject');
                                $activity_class = "red";
                            }
                    ?>
                    <tr>
                        <td class='center'><?php echo $v_row_no?></td>
                        <td>
                            <a href="javascript:void(0)" onclick="record_onclick(this);">
                                <?php echo $v_record_no?>
                            </a>
                        </td>
                        <td><?php echo $v_citizen_name?></td>
                        <td class="center"><?php echo $v_receive_date?></td>
                        <td class="center"><?php echo $v_return_date?></td>
                        <td class="bold <?php echo $activity_class?>"><?php echo $activity_label?></td>
                    </tr>
                    <?php endforeach;?>
                </table>
                <!--button filter paging-->
                <div class="div_pagination" align="right">
                    <?php
                    $v_url = build_url_lookup();
                    $v_total_record = isset($arr_list[0]['TOTAL_RECORD']) ? $arr_list[0]['TOTAL_RECORD'] : 0;
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
                                <a href="<?php echo build_url_lookup($first_page,$v_selected_member); ?>" title="<?php echo __("first page") ?>">
                                
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
                                                            <a href="<?php echo build_url_lookup($i,$v_selected_member)?>">
                                                                <strong><?php echo $i; ?></strong>
                                                            </a>
                                                        </li>
                                <?php endfor; ?>
                            <li data-val="<?php echo $n; ?>">
                                <a href="<?php echo build_url_lookup($last_page,$v_selected_member); ?>" title="<?php echo __("last page") ?>">
                                    <?php echo __("last page") ?>
                                </a>
                            </li>
                        </ul>
                    <?php endif; //n > 1 ?>
                </div><!--end pagging-->
                <?php endif;?>
            </div><!--end lookup content-->
        </div><!--end synthesis-->
    </div><!--end col 12 block-->
    <?php endif;?>
</div><!--col md 12-->
<div class="clear" style="height: 10px;"></div>
<script>
    $(document).ready(function(){
        var page = parseInt($('#hdn_current_page').val());
        if(page == 0)
        {
            page = 1;
        }
        $('.pagination li[data-val='+page+']').addClass('active');
    });
    function lookup_onclick()
    {
        var action = $('#frmMain').attr('action');
        var current_unit = $('#hdn_unit').val();
        var current_page = $('#hdn_current_page').val();
        var select_unit  = $('#sel_member').val();
        
        if(current_unit != select_unit)
        {
            action += '?page=1&unit='+ select_unit;
        }
        else
        {
            action += '?page='+ current_page +'&unit='+ select_unit;
        }
        $('#frmMain').attr('action',action);
        $('#frmMain').submit();
    }
    function record_onclick(a)
    {
        var record_no = $(a).html();
        record_no = record_no.trim();
        $('#txt_record_no').val(record_no);
        lookup_onclick();
    }
</script>
<?php
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;
$this->render('dsp_footer', $VIEW_DATA, $this->theme_code);
?>
