<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed'); ?>
<?php
$VIEW_DATA['title']                 = $this->website_name ;
$VIEW_DATA['v_banner']              = $v_banner;
$VIEW_DATA['arr_all_website']       = $arr_all_website;
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;

$VIEW_DATA['arr_css'] = array('single-page', 'synthesis', 'component', 'breadcrumb');
$VIEW_DATA['arr_script'] = array();
$this->render('dsp_header', $VIEW_DATA, $this->theme_code);
$v_sel_district_id  = isset($_GET['sel_district']) ? $_GET['sel_district'] : '';
$v_sel_village_id   = isset($_GET['sel_village']) ? $_GET['sel_village'] : '';
$v_txt_member_name  = isset($_GET['txt_member_name']) ? $_GET['txt_member_name'] : '';

?>
<?php
    $file_path_menu_top_two = __DIR__ . DS . 'menu_top_two_evaluation.php';
    if (is_file($file_path_menu_top_two)) {
        require $file_path_menu_top_two;
    }
?>
<div class="col-md-12 content">
     <div class="clear" style="height: 20px;"></div>
    <div class="row" style="margin: 0 10px;">
        <form name="frmMain" id="frmMain" action="<?php echo build_url_evaluation();?>">
            <div id="evaluation-filter" style="padding: 0px;padding-bottom: 20px">
                <div class="col-xs-12 col-sm-3 col-md-4" style="padding-left: 0">
                    <div class="col-md-12 block">
                        <div class="col-xs-12 col-md-3 block"><b class="title"><?php echo __('units')?></b></div>
                        <div class="col-xs-12 col-md-9 block">
                            <select  id="sel_district" name="sel_district">
                            <option value=""> --<?php echo __('all')?>-- </option>
                            <?php
                                    $v_path_xml = SERVER_ROOT.'public'.DS.'xml'.DS.'xml_scope.xml';
                                    if($this->load_abs_xml($v_path_xml))
                                    {
                                        $arr_scope = $this->dom[0];
                                        foreach ($arr_scope as $key=>$val)
                                        {
                                            $v_scope_id = $val[0]->attributes()->scope;
                                            echo '<optgroup label="'.$val.'"></optgroup>';
                                            $i=0;
                                            foreach ($arr_all_member_evaluation as $arr_single_member_evaluation)
                                            {
                                                $v_member_id    = $arr_single_member_evaluation['PK_MEMBER'];
                                                $v_mb_scope_id  = $arr_single_member_evaluation['C_SCOPE_ID'];
                                                $v_member_name  = $arr_single_member_evaluation['C_NAME'];
                                                $v_mb_selected  = ($v_member_id == $v_sel_district_id) ? 'selected' : '';
                                                if($v_scope_id  == $v_mb_scope_id)
                                                {
                                                    echo '<option '. $v_mb_selected . ' value="' . $v_member_id . '" >' . $v_member_name . '</option>';
                                                }
                                                $i ++;
                                            }
                                        }
                                    }
                            ?>
                        </select>
                        </div>
                    </div>
                    <div class="clear" style="height: 5px;"></div>
                </div>
                <!--End #scope-->

                <div class="col-xs-12 col-sm-3 col-md-4" style="padding-left: 0">
                    <div class="col-md-12 block">
                        <div class="col-xs-12 col-md-4 block"><b class="title"><?php echo __('communal units')?></b></div>
                        <div class="col-xs-12 col-md-8 block">
                            <select id="sel_village" name="sel_village">
                                <option value=""> --<?php echo __('all') ?>-- </option>
                                <?php
                                 foreach ($arr_all_member_evaluation as $arr_single_member_evaluation)
                                {
                                    $v_member_id        = $arr_single_member_evaluation['PK_MEMBER'];
                                    $v_mb_scope_id      = $arr_single_member_evaluation['C_SCOPE_ID'];
                                    $v_member_name      = $arr_single_member_evaluation['C_NAME'];
                                    $v_member_child_id  = $arr_single_member_evaluation['FK_MEMBER'];
                                    $v_mb_selected      = ($v_member_id == $v_sel_village_id) ? 'selected' : '';
                                    echo '<option class="'.$v_member_child_id.'"  '. $v_mb_selected . ' value="' . $v_member_id . '" >' . $v_member_name . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-3 col-md-3" style="padding-left: 0;">
                    <div class="col-xs-12 block col-xs-none"><b class="title">&nbsp;</b></div>
                    <div class="col-xs-12 col-md-12 block">
                        <input type="text" name="txt_member_name" id="txt_member_name" value="<?php echo $v_txt_member_name;?>">
                    </div>
                </div>
                <div class="col-xs-12 col-sm-3 col-md-1" style="padding-left: 0">
                    <div class="col-xs-12 block col-xs-none"><b class="title">&nbsp;</b></div>
                    <div class="col-xs-12 block">
                        <button type="submit" class="btn btn-primary btn-sm" style="font-size: 12px;">
                            <span class="glyphicon glyphicon-search "></span>
                            <?php echo __('search') ?>
                        </button>
                    </div>
                    <div class="clear" style="height: 5px;"></div>
                </div>
            </div>
            <style>
                .col-xs-none{display: none;}
                @media (max-width:992px)
                {
                    .col-xs-none{display: block};
                }
                .single-staff:hover,.single-staff:visited,.single-staff:focus,.single-staff:active
                {
                    background: rgb(249, 249, 249);
                }
            </style>
            <!--End village-->
        </form>
    </div>
    <div class="clearfix" style="height: 20px"></div>
    <div class="row" style="margin: 0 10px;">
        <?php for($i =0;$i <count($arr_evaluation_results);$i++):; ?>
            <?php
                $v_trong_so   = isset($arr_evaluation_results[$i]['C_TOTAL_POINT'])? $arr_evaluation_results[$i]['C_TOTAL_POINT'] : 0;
                $v_staff_name = isset($arr_evaluation_results[$i]['C_NAME'])? $arr_evaluation_results[$i]['C_NAME'] :'';
                $v_staff_id   = isset($arr_evaluation_results[$i]['PK_LIST'])? $arr_evaluation_results[$i]['PK_LIST'] :'';
                $v_staff_code = isset($arr_evaluation_results[$i]['C_CODE'])? $arr_evaluation_results[$i]['C_CODE'] :'';
                $v_xml_data   = isset($arr_evaluation_results[$i]['C_XML_DATA'])? $arr_evaluation_results[$i]['C_XML_DATA'] :'';
                $v_member_name= isset($arr_evaluation_results[$i]['C_UNIT_NAME'])? $arr_evaluation_results[$i]['C_UNIT_NAME'] :'';
                $v_scope      = isset($arr_evaluation_results[$i]['C_SCOPE'])? $arr_evaluation_results[$i]['C_SCOPE'] :'1';
                $v_dir_img_staff_logo = CONST_DIRECT_VOTE_IMAGES . $v_staff_code . '.jpg';
                //Anh logo mac dinh
                $v_url_img_staff_logo = SITE_ROOT.'public/images/logo_default.jpg';
                if (file_exists($v_dir_img_staff_logo)) 
                {
                    $v_url_img_staff_logo = CONST_URL_VOTE_IMAGES . $v_staff_code . '.jpg';
                }
                 @$dom = simplexml_load_string($v_xml_data);
                if($dom)
                {
                    $v_member_id  = $dom->xpath('//item[@id="ddl_member"]/value');
                    $v_member_id  = (string)$v_member_id[0];

                    $v_birthday   = $dom->xpath('//item[@id="txt_birthday"]/value');
                    $v_birthday   = (string)$v_birthday[0];

                    $v_education  = $dom->xpath('//item[@id="txt_education"]/value');
                    $v_education  = (string)$v_education[0];

                    $v_jo_title   = $dom->xpath('//item[@id="txt_job_title"]/value');
                    $v_jo_title   = (string)$v_jo_title[0];

                    $v_email      = $dom->xpath('//item[@id="txt_email"]/value');
                    $v_email      = (string)$v_email[0];
                }
              $url_single_staff =   build_url_single_staff($v_staff_id)
            ?>
        <?php   echo ($i%2 == 0) ? '<div class="row">': ''; ?>
        <div class="col-xs-12 col-sm-6 col-md-6 single-staff">
            <div class="clear" style="height: 10px"></div>
            <div class="col-xs-12">
                <div class="col-xs-12 col-sm-4 col-md-3 block logo" style="">
                    <img src="<?php echo $v_url_img_staff_logo; ?>" width="102.5"> 
                </div>
                <div class="col-xs-12 col-sm-8 col-md-9 block" style="padding-left: 10px;">
                    <b style="text-transform: uppercase;"><?php echo $v_staff_name; ?></b>
                    <ul>
                        <li>
                            <table>
                                <tbody><tr>
                                    <td>Ngày sinh:</td>
                                    <td><?php echo $v_education; ?></td>
                                </tr>
                                <tr>
                                    <td>Trình độ:</td>
                                    <td><?php echo $v_birthday; ?></td>
                                </tr>
                                <tr>
                                    <td>Chức vụ:</td>
                                    <td><?php echo $v_jo_title; ?></td>
                                </tr>
                                <tr>
                                    <td>Đơn vị:</td>
                                    <td><?php echo $v_member_name; ?></td>
                                </tr>
                                <tr>
                                    <td>Điểm:</td>
                                    <td> <?php echo $v_trong_so; ?></td>
                                </tr>
                            </tbody></table>                                   
                        </li>
                        <li>
                            <a class="btn btn-info btn-vote" style="background: white;color: black;padding: 4px;font-size: 12px;width: 90px;margin-top: 5px;" 
                               href="<?php echo $url_single_staff; ?>">Đánh giá</a>
                            <a class="btn btn-info btn-resulate" style="background: white;color: black;padding: 4px;font-size: 12px;width: 90px;margin-top: 5px;" 
                               href="<?php echo  build_url_evaluation(false,$v_staff_id,0);?>">Xem kết quả</a>
                        </li>
                    </ul> 
                </div>
            </div>
            <div class="clear" style="height: 10px"></div>
        </div>
        <?php   echo ($i%2 != 0 || !isset($arr_evaluation_results[$i+1]))? '</div>' :''; ?>
        <?php endfor; ?>
    </div>
    <!--button filter page-->
            <div class="clear">&nbsp;</div>
            <?php
            $v_count_staff = isset($arr_evaluation_results[0]['C_TOTAL_STAFF']) ? $arr_evaluation_results[0]['C_TOTAL_STAFF'] : '';
            $n = ceil($v_count_staff / _CONST_LIMT_STAFFT_SINGLE_PAGE);
            $filter_page_no = get_request_var('page', 1);
//            $first_page = 1;
//            $filter_page_no     == 1 ? $previous_page = 1 : $previous_page = $filter_page_no - 1;
//            $filter_page_no     == $n ? $next_page = $n : $next_page = $filter_page_no + 1;
//            $last_page          = $n;
//            $filter_page_no     <= 2 ? $i_article = 1 : $i_article = $filter_page_no - 1;
            $filter_page_no = get_request_var('page', 1);
            $first_page     = 1;
            $previous_page  = $filter_page_no <= 1 ? 1 : $filter_page_no - 1;
            $next_page      = $filter_page_no == $n ? $n : $filter_page_no + 1;
            $last_page      = $n;
            $i              = $filter_page_no <= 2 ? 1 : $filter_page_no - 1;
            ?>
            <?php if ($n > 1): ?>
                <div class="div-filter-page" align="center">
                    <?php if ($n > 1): ?>
                        <ul class="pagination">
                            <li data-val="1">
                                <a href="<?php echo build_url_evaluation(false, false, $first_page); ?>" title="<?php echo __("first page") ?>">
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
                                    <a href="<?php echo build_url_evaluation(false, false, $i) ?>">
                                        <strong><?php echo $i; ?></strong>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            <li data-val="<?php echo $n; ?>">
                                <a href="<?php echo build_url_evaluation(false, false, $last_page) ?>" title="<?php echo __("last page") ?>">
                                    <?php echo __("last page") ?>
                                </a>
                            </li>
                        </ul>
                    <?php endif; //n > 1 ?>         
                </div>
            <?php endif; //n > 1 ?>    
            <div class="clear" style="height: 10px">&nbsp;</div>

            <!--End #scopes-->
    <div class="clear"></div>
    <!--End #Main-content-->
</div>
<script>
   $("#sel_village").chained("#sel_district");
   $page = <?php echo (int) get_request_var('page', 1); ?>;
    if($page == 0)
    {
        $page = 1;
    }
    
//    $('.pagination li[data-val='+$page+']').addClass('active').html('<a>'+$page+'</a>');
    $('.pagination li[data-val='+$page+']').addClass('active');
</script> 
<?php
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;
$this->render('dsp_footer', $VIEW_DATA, $this->theme_code);