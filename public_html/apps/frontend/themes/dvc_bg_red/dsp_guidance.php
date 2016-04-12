<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed'); ?>
<?php
//du lieu header
$VIEW_DATA['title'] = $this->website_name;
$VIEW_DATA['v_banner'] = $v_banner;
$VIEW_DATA['arr_all_website'] = $arr_all_website;
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;
$VIEW_DATA['arr_css'] = array('component', 'guidance', 'single-page');
$VIEW_DATA['arr_script'] = array();

$arr_all_village = $arr_all_member['arr_all_village'];
$arr_all_district = $arr_all_member['arr_all_district'];

$v_website_id = $this->website_id;

$v_total_rows = isset($arr_all_guidance['count_all_record']) ? $arr_all_guidance['count_all_record'] : 0;

$arr_all_list_guidance = isset($arr_all_guidance['arr_all_record_type']) ? $arr_all_guidance['arr_all_record_type'] : array();
$arr_all_list = isset($arr_all_list) ? $arr_all_list : array();

//$v_total_page           = ceil($v_total_rows / CONTS_LIMIT_GUIDANCE_LIST);

$sel_record_list    = isset($_REQUEST['sel_record_list']) ? $_REQUEST['sel_record_list'] : '';
$v_record_type_code = isset($_REQUEST['txt_record_type_code']) ? $_REQUEST['txt_record_type_code'] : '';
$sel_record_type    = isset($_REQUEST['sel_record_type']) ? $_REQUEST['sel_record_type'] : '';
$sel_record_level   = isset($_REQUEST['sel_record_level']) ? $_REQUEST['sel_record_level'] : '';
$sel_cap_do         = isset($_REQUEST['sel_cap_do']) ? $_REQUEST['sel_cap_do'] : '';
?>
<?php $this->render('dsp_header', $VIEW_DATA, $this->theme_code); ?>
<div class="clear"></div>
<div  class="group-option col-md-12" id="page-guidance"> 
    <div id="page-search" class="col-md-12 block" >
        <div class="col-md-3 block" id="left-sidebar">

            <?php $m = isset($arr_all_widget_position['widget_left']) ? count($arr_all_widget_position['widget_left']) : 0; ?>
            <?php for ($i = 0; $i < $m; $i++): ?>
                <?php echo $arr_all_widget_position['widget_left'][$i]['C_CONTENT'] ?>
            <?php endfor; ?>
        </div> 
        <div class="col-md-9">

            <!--End #box-chart-->
            <form class="form-horizontal" name="frmMain" id="frmMain" action="" method="GET"  >
                <div id="box-search"  >
                    <div class="content-widgets light-gray span12">
                        <div class="widget-head blue">
                            <h3><?php echo __('lookup') ?></h3>
                        </div>
                        <!--End .widget-head blue-->

                        <div class="widget-container" id="filter">
                            <div class="Row">
                                <div class="left-Col">
                                    <label ><?php echo __('field') ?></label>
                                </div>
                                <div class="right-Col">
                                    <select style="" class="span6" name="sel_record_list" id="sel_record_list" onchange="sel_record_list_onchange(this)">
                                        <option value="">----------- <?php echo __('select field'); ?> ----------</option>
                                        <?php
                                        echo $this->generate_select_option($arr_all_list, $sel_record_list);
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <!--End #sel_record_list-->
                            <div class="Row">
                                <div class="left-Col">
                                    <label><?php echo __('record type code') ?> </label>
                                </div>
                                <div class="right-Col">
                                    <div class="left">
                                        <input type="text" class="input-small"
                                               name="txt_record_type_code" id="txt_record_type_code"
                                               value="<?php echo $v_record_type_code; ?>"
                                               class="inputbox upper_text" size="10" maxlength="10"                                       
                                               onkeypress="txt_record_type_code_onkeypress(event);"
                                               onchange="txt_record_type_onchane(this);"
                                               autofocus="autofocus" accesskey="1" />

                                    </div>
                                    <div class="right">
                                        <select name="sel_record_type" id="sel_record_type"
                                                style="color: #000000;"
                                                onchange="sel_record_type_onchange(this)">
                                            <option value="">-- <?php echo __('select record type'); ?> --</option>
                                            <?php
                                            $arr_all_record_type = isset($arr_all_record_type) ? $arr_all_record_type : array();
                                            echo $this->generate_select_option($arr_all_record_type, $sel_record_type);
                                            ?>
                                        </select>
                                    </div>

                                </div>
                            </div>
                            <div class="Row">
                                <div class="left-Col">
                                    <label><?php echo __('Cấp tiếp nhận'); ?></label>
                                </div>
                                <div class="right-Col">
                                    <select name="sel_cap_do" id="sel_cap_do" onchange="this.form.submit()">
                                            <option value="0,1,2,3">-- <?php echo __('Tất cả')?> --</option>
                                            <option value="0,1" <?php echo ((string)$sel_cap_do === '0,1') ? 'selected' : '' ?> > <?php echo __('commune/ward')?></option>
                                            <option value="2,3" <?php echo ((string)$sel_cap_do === '2,3') ? 'selected' : '' ?> ><?php echo __('district')?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="Row">
                                <div class="left-Col">
                                    <label><?php echo __('service level'); ?></label>
                                </div>
                                <div class="right-Col">
                                    <select class="span6" name="sel_record_level" id="sel_record_level" >
                                        <?php
                                        echo $this->generate_select_option('', $sel_record_level, 'xml_muc_do_dich_vu_cong_truc_tuyen.xml');
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="Row">
                                <div class="left-Col">&nbsp;</div>
                                <div class="right-Col">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <span class="glyphicon glyphicon-search "></span>
                                        <?php echo __('search') ?>
                                    </button>

                                </div>
                            </div>
                            <!--End btn submit-->
                        </div>
                    </div>
                </div>
                <!--End #box-serach-->
                 <div id="procedure">
                            <div class="clear">&nbsp;</div>
                            <table border="0" cellpadding="0" cellspacing="0">
                                <colgroup>
                                    <col style="width: 5%">
                                    <col style="width: 10%">
                                    <col style="width: 57%;" />
                                    <col style="width: 10%; "/>
                                    <col style="width: 8%" />
                                    <col style="width: 10%" />
                                </colgroup>
                                <thead style="cursor: context-menu;background: rgba(250, 250, 250, 0.29);" >
                                <th style=" color: rgb(94, 86, 86);text-align: center;; font-weight: bold; cursor: context-menu "><?php echo __('row'); ?></th>
                                <th style="width:100px;text-align: center;color:  rgb(94, 86, 86);; font-weight: rgb(94, 86, 86);;cursor: context-menu "><?php echo __('record type code') ?></th>
                                <th style="color:white;text-align: left;color:  rgb(94, 86, 86);; font-weight: bold;cursor: context-menu "><?php echo __('list of procedures'); ?></th>
                                <th style="color:white;text-align: center;color:  rgb(94, 86, 86);; font-weight: bold;cursor: context-menu "><?php echo __('attachments') ?></th>
                                <th style="color:white;text-align: center;color:  rgb(94, 86, 86);; font-weight: bold;cursor: context-menu "><?php echo __('scope'); ?></th>
                                <th style="color:white;text-align: center;color:  rgb(94, 86, 86);; font-weight: bold;cursor: context-menu "><?php echo __('action'); ?></th>
                                </thead>
                                <?php
                                //$stt đánh số thu tu cho cac tu tuc khi chuyen trang
                                $v_start = 1;
                                if (count($arr_all_list_guidance) <= 0)
                                {
                                    if (isset($_GET['keyword']))
                                    {
                                        echo '<tr style="background:white;"><td colspan="3">';
                                        echo '<h1 style="color:red; width:100%;text-align:center ;margin:20px 0">Không tìn thầy thủ tục nào phù hợp. <a style="color:blue;" href="javascript::void()" onclick="window.history.back(-1)" >Quay lại trang trước</a></h1>';
                                        echo '</tr></td>';
                                    }
                                    else
                                    {
                                        echo '<tr><td colspan="6" style="text-align:center;">';
//                                echo '<h2 class="mes-error">Không tìm thấy kết quả nào phù hợp.</h2>';
                                        echo '</td></tr>';
                                    }
                                }
                                else
                                {
                                    for ($i = 0; $i < count($arr_all_list_guidance); $i++):
                                        ?>
                                        <?php
                                        $v_name = $arr_all_list_guidance[$i]['C_NAME'];
                                        $v_id = $arr_all_list_guidance[$i]['PK_RECORD_TYPE'];
                                        $v_code = $arr_all_list_guidance[$i]['C_CODE'];
                                        $v_village_name = isset($arr_all_list_guidance[$i]['C_SCOPE']) ? $arr_all_list_guidance[$i]['C_SCOPE'] : '';
                                        $v_send_internet = isset($arr_all_list_guidance[$i]['C_SEND_OVER_INTERNET']) ? (int) $arr_all_list_guidance[$i]['C_SEND_OVER_INTERNET'] : 0;
                                        $arr_all_file = isset($arr_all_list_guidance[$i]['arr_all_file']) ? $arr_all_list_guidance[$i]['arr_all_file'] : array();
                                        $v_send_over_internet = isset($arr_all_list_guidance[$i]['C_SEND_OVER_INTERNET']) ? $arr_all_list_guidance[$i]['C_SEND_OVER_INTERNET'] : 0;
                                        $v_url = build_url_guidance(false, $v_id);
                                        ?>

                                        <tr class="<?php echo ($i % 2) ? 'odd' : 'even'; ?>">
                                            <td class="stt" style="text-align: center"><?php echo $v_start; ?></td>
                                            <td class="stt mtt" style="text-align: center"><?php echo $v_code; ?></td>
                                            <td style="padding-left: 5px;">
                                                <a href="<?php echo $v_url ?>"><span class="all-list-content"><?php echo $v_name; ?></span></a>
                                            </td>
                                            <td>
                                                <?php foreach ($arr_all_file as $single_file): ?>
                                                    <?php
                                                    $v_file_name = $single_file['file_name'];
                                                    $v_name = $single_file['name'];
                                                    $v_file_type = $single_file['type'];

                                                    $arr_all_icon_file = json_decode(CONTS_ICON_FILE_GUIDANCE, TRUE);
                                                    $v_url_icon = CONST_SITE_THEME_ROOT . 'images/icon-attach-default.png';
//                                                    if (key_exists($v_file_type, $arr_all_icon_file))
//                                                    {
//                                                        $v_url_icon = CONST_SITE_THEME_ROOT . 'images/' . $arr_all_icon_file[$v_file_type];
//                                                    }
                                                    $v_url = $this->get_controller_url() . 'download?file_name=' . md5($v_file_name) . '&record_code=' . $v_code . '&name=' . $v_name;
                                                    ?>
                                                    <a  target='_blank'  class="icon-file-dowload" style="padding:5px; padding-top: 3px;padding-bottom: 3px;display: block;float: left" title="<?php echo $v_name; ?>" href="<?php echo $v_url; ?>"><img src="<?php echo $v_url_icon; ?>" width="15px" height="auto" ></a>
        <?php endforeach; ?>
                                            </td>
                                            <td class="level" style="text-align: center"> 
                                                <div class="down-regis">
                                                    <?php
                                                    echo $v_send_over_internet > 0 ? '3' : '2, 1';
                                                    ?>  
                                                </div>
                                            </td>
                                            <td class="acction">
                                                <div class="down-regis">
                                                    <?php
                                                    if ($v_send_over_internet > 0)
                                                    {
                                                        $v_url = build_url_send_internet_record('', '', $v_id);
                                                        echo "<a href='$v_url' > Ðăng ký</button</a>";
                                                    }
                                                    ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php
                                        $v_start++;
                                    endfor;
                                    ?>
<?php } ?>
                            </table>
                            <!--button filter page-->
                            <div class="div_pagination" align="right">
                                <?php
                                $v_page = get_request_var('page', 1);
                                $v_total_record = isset($arr_all_guidance['count_all_record']) ? $arr_all_guidance['count_all_record'] : 0;
                                $n = ceil($v_total_record / _CONTS_LIMIT_GUIDANCE_LIST);
                                if (!$n)
                                {
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
                                        <li class="<?php echo ($filter_page_no == 1) ? 'active' : '' ?>">
                                            <a href="<?php echo build_url_guidance(false, false, $sel_record_list, $v_record_type_code, $sel_record_type, $sel_record_level, $first_page); ?>" title="<?php echo __("first page") ?>">
    <?php echo __("first page") ?>
                                            </a>
                                        </li>
                                        <?php
                                        for ($i, $j = 1; $i <= $n && $j <= 5; $i++, $j++):
                                            if ($i == 1 or $i == $n)
                                            {
                                                continue;
                                            }
                                            ?>
                                            <li data-val="<?php echo $i; ?>">
                                                <a href="<?php echo build_url_guidance(false, false, $sel_record_list, $v_record_type_code, $sel_record_type, $sel_record_level, $i); ?>">
                                                    <strong><?php echo $i; ?></strong>
                                                </a>
                                            </li>
    <?php endfor; ?>
                                        <li class="<?php echo ($filter_page_no == $last_page) ? 'active' : '' ?>" >
                                            <a href="<?php echo build_url_guidance(false, false, $sel_record_list, $v_record_type_code, $sel_record_type, $sel_record_level, $last_page); ?>" title="<?php echo __("last page") ?>">
    <?php echo __("last page") ?>
                                            </a>
                                        </li>
                                    </ul>
<?php endif; //n > 1    ?>
                            </div>
                        </div>

                <script>
                                        $page = <?php echo (int) get_request_var('page', 1); ?>;
                                        if ($page == 0)
                                        {
                                            $page = 1;
                                        }
                                        $('.pagination li[data-val=' + $page + ']').addClass('active').html('<a>' + $page + '</a>');
                </script>
            </form>
        </div> 
    </div>
    <!--End .span12-->
</div>
<script>
    function txt_record_type_onchane(selector)
    {
        var record_type_code = $(selector).val() || '';
        $('#sel_goto_page').val('1');
        $(selector).val($(selector).val().toUpperCase());
        if (record_type_code == '' || typeof(record_type_code) == 'undefined')
        {
            $('#sel_record_type').find('option').removeAttr('selected')
        }
        $('#sel_record_type').find('option').removeAttr('selected')
                .end().find('[value="' + record_type_code + '"]').attr('selected', 'selected');
    }
    function sel_record_list_onchange(selector)
    {
        var record_listtype_id = $(selector).val() || 0;
        $('#txt_record_type_code').val('');
        $('#sel_goto_page').val('1');
        document.forms.frmMain.submit();
    }
    function sel_record_type_onchange(selector)
    {
        var record_type_code = $(selector).val() || '';
        $('#txt_record_type_code').val(record_type_code);
        $('#sel_goto_page').val('1');
        document.forms.frmMain.submit();
    }
    function btn_go_page_onlick(page)
    {
        if (parseInt(page) > 0)
        {
            window.location.href = '<?php echo FULL_SITE_ROOT . 'huong-dan-thu-tuc/'; ?>' + page;
        }
    }
</script>
<script>
    $(document).ready(function() {
        $('.pagination li[data-val=<?php echo $filter_page_no; ?>]').attr('class', 'active');
        $('.pagination li[data-val=<?php echo $filter_page_no; ?>]').html($('.pagination li[data-val=<?php echo $filter_page_no; ?>] strong').html());
    });
</script>


<?php
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;
$this->render('dsp_footer', $VIEW_DATA, $this->theme_code);