<?php
$VIEW_DATA['title'] = $this->website_name;
$VIEW_DATA['v_banner'] = $v_banner;
$VIEW_DATA['arr_all_website'] = $arr_all_website;
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;

$VIEW_DATA['arr_css'] = array('lookup', 'gp-slide', 'synthesis', 'cadre_evaluation', 'table');
$VIEW_DATA['arr_script'] = array('gp-slide');
$this->render('dsp_header', $VIEW_DATA, $this->theme_code);
$arr_all_sticky = isset($arr_all_sticky) ? $arr_all_sticky : array();
?>
<?php echo hidden('hdn_json_synthesis',  json_encode($arr_synthesis));?>
<div class="col-md-12 content">
    <div class="col-md-12 block">
        <!--Tin moi-->
        <div class="col-md-12 col_news ">
            <div class="col-lg-1 border_left_new_header "></div>
            <div id="content-row1">
                <div class="article-news">
                    <div class="border-before"></div>
                    <div class="contetn-col-news">
                        <a href=""><?php echo __('new post')?></a>
                    </div>
                    <div class="bg-col-news-after"></div>
                </div>
                <div class="marquee">
                    <marquee vspace="1" height="28px" behavior="scroll" valign="middle" direction="left" scrollamount="1" scrolldelay="20" onmouseout="this.start()" onmouseover="this.stop()">
                        <?php for($i = 0;$i <= count($arr_all_sticky );$i ++): 
                            
                            $v_category_id      = $arr_all_sticky[$i]['FK_CATEGORY'];
                            $v_slug_cat         = $arr_all_sticky[$i]['C_SLUG_CATEGORY'];
                            $v_article_id       = $arr_all_sticky[$i]['FK_ARTICLE'];
                            $v_website_id       = $arr_all_sticky[$i]['FK_WEBSITE'];
                            $v_title            = $arr_all_sticky[$i]['C_TITLE'];
                            $v_slug_art         = $arr_all_sticky[$i]['C_SLUG_ARTICLE'];
                            $v_order            = $arr_all_sticky[$i]['C_ORDER'];
                            $v_file_name        = isset($arr_all_sticky[$i]['C_FILE_NAME']) ? $arr_all_sticky[$i]['C_FILE_NAME'] : '';
                            
                            $v_url = build_url_article($v_slug_cat, $v_slug_art, $v_website_id, $v_category_id, $v_article_id) ;
                            if(trim($v_title) != '' )
                            {
                                $v__ = ($i > 0) ? '&nbsp;&nbsp; - &nbsp;&nbsp;' : '' ;
                                echo $v__ .'<a title="'.$v_title.'" href="'.$v_url.'">'.$v_title.'</a> ';
                            }
                            endfor;?>
                    </marquee>
                    
                </div>
                <div class="languge">
                   <?php foreach ($arr_all_website as $website): ?>
                    <?php $v_class = ($website['C_THEME_CODE'] == $this->theme_code) ? ' current' : ''; ?>
                    <a class="language <?php echo $v_class ?>" href="<?php echo FULL_SITE_ROOT . $website['PK_WEBSITE']; ?>" >
                        <?php
                            $v_list_code = isset($website['C_LIST_CODE']) ? $website['C_LIST_CODE'] : '';
                            $v_url_language = SITE_ROOT. 'upload/icon_language/'.$website['C_LIST_CODE'].'.png';
                            $v_path_language = SERVER_ROOT . 'upload'.DS.'icon_language'.DS.$website['C_LIST_CODE'].'.png';
                            if(is_file($v_path_language) && trim($v_list_code) != '')
                            {
                                echo "<img src='$v_url_language' />";
                            }
                        ?>
                    </a>
                    <?php endforeach;?>
                    <div class="border-right"></div>
                </div>
            </div>
        </div>
        <!--end tin moi-->
        <div class="clear"></div>
        <!--start home header-->
        <div class="col-md-12 header_block block">
            <!--start tra cuu website-->
            <div class="col-lg-4 block left-Col header_lookup">
                <form method="POST" action="<?php echo build_url_lookup()?>">
                    <div  class="title">
                        <div class="col-md-1 col-xs-1 col-sm-1">
                            <img src="<?php echo CONST_SITE_THEME_ROOT . 'images/circle.png'?>" />
                        </div>
                        <div class="col-md-10 col-xs-10 col-sm-10" style="width: 80%;">
                            <label ><?php echo __('status lookup records') ?></label>
                        </div>
                        <div class="col-md-1 col-xs-1 col-sm-1">
                            <img src="<?php echo CONST_SITE_THEME_ROOT . 'images/circle.png'?>" />
                        </div>
                    </div>
                    <div class="box-statistic">
                        <div class="col-md-12 mess-record_no">
                            Để thực hiện tra cứu tình trạng hồ sơ cấp phép, xin vui lòng nhập mã số tra cứu trên giấy biên nhận.
                        </div>
                        <div class="col-md-12 send_record_no">
                            <div class="col-md-8 block">
                                <input type="textbox" name="txt_record_no" id="txt_record_no">
                            </div>
                            <div class="col-md-4 block">
                                <button type="submit" class="btn-one-dor"><?php echo __('lookup');?></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!--end tra cuu website-->
            <!--start mot cua dien tu-->
            <div class="col-lg-4 block left-Col header_one_dor">
                <div  class="title">
                    <div class="col-md-1 col-xs-1 col-sm-1">
                        <img src="<?php echo CONST_SITE_THEME_ROOT . 'images/circle.png'?>" />
                    </div>
                    <div class="col-md-10 col-xs-10 col-sm-10" style="width: 80%;">
                        <label ><?php echo __('One Stop Services')?></label>
                    </div>
                    <div class="col-md-1 col-xs-1 col-sm-1">
                        <img src="<?php echo CONST_SITE_THEME_ROOT . 'images/circle.png'?>" />
                    </div>
                </div>
                <div class="box-statistic">
                    <div class="time">
                        <b><?php echo __('month'); ?> <span> <?php echo Date('m') . '/' . Date('Y') ?></span> 
                            <br>
                             <?php echo __('solved Bac Giang'); ?></b>
                    </div>
                    <div class="result" id="result_auto_update">
                        <span class="val"></span>
                        <br>
                        <b><?php echo __('on time')?></b>
                    </div>
                    <div class="time-update">
                          <?php 
                                $max_dateime_update_record_history_start = isset($max_dateime_update_record_history_start) ? $max_dateime_update_record_history_start : '';
                            ?>
                            <span><i>(<?php echo __('auto updated') ?>: <?php echo $max_dateime_update_record_history_start; ?>)</i></span>
                    </div>
                </div>
            </div>
            <!--end mot cua dien tu-->
            <!--start dang nhap-->
            <div class="col-lg-4 block right-Col register-send-record" >
                <div class="box-statistic">
                    <div class="col-md-12 block">
                        <a href="<?php echo build_url_submit_internet_record()?>"><img src="<?php echo CONST_SITE_THEME_ROOT?>images/guihosotructuyen.png" /></a>
                        <?php
                                $v_username =   Session::get('citizen_login_name');
                                if(trim($v_username) == '' OR $v_username  == NULL) 
                                {
                                    $v_url_register     = SITE_ROOT . 'register';
                                    $v_url_img_register = CONST_SITE_THEME_ROOT .'images/dangky-1.png';
                                    echo "<a href='$v_url_register'><img src='$v_url_img_register'></a>";
                                }
                                else
                                {
                                    $v_url_hisrory_filing       = build_url_single_account_citizen(0,true);
                                    $v_url_img_history_filing   = CONST_SITE_THEME_ROOT .'images/tracuulichsu.png';
                                    echo "<a href='$v_url_hisrory_filing'><img src='$v_url_img_history_filing'></a>";
                                }
                        ?>
                    </div>
                </div>
            </div>
            <!--end dang nhap-->
        </div>
    </div><!--end homepage header-->
    <div class="col-md-12 block">
        <div class="div-synthesis">
            <div class="div_title_bg-title-top"></div>
            <div class="div_title">
                <div class="title-border-left"></div>
                <div class="title-content">
                    <label>
                    <?php echo __('total Transaction of'); ?> <?php echo Date('m') . '/' . Date('Y') ?>
                    </label>
                </div>
                <div class="title-border-right"></div>
            </div>
            <div style="overflow: hidden;margin-top: -6px;">
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
                    $i = 1;
                    ?>
                    <?php
                        foreach ($arr_synthesis as $arr_value):
                            $v_member_code             = isset($arr_value['C_UNIT_CODE']) ? $arr_value['C_UNIT_CODE'] : '';
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
                             //liveboard link
                            $liveboard_link = '';
                            foreach($arr_all_liveboard as $arr_liveboard)
                            {
                                if($v_member_code == $arr_liveboard['C_CODE'])
                                {
                                    $liveboard_link = $arr_liveboard['C_LIVEBOARD_LINK'] . '?show=1';
                                    break;
                                }
                            }
                    ?>
                         <tr class="<?php echo ($i % 2) ? 'xam' : ''; ?>">
                            <td class="blue left" style="width: 150px !important;text-transform: uppercase">
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
                                <?php echo $count_tro_tra_trong_ky ?>
                            </td>
                            <td class="orange right center" >
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
            </div>
            <div class="div-footer">
            </div>
        </div><!--end synthesis-->
    </div><!--end col 12-->
    <div class="clear" style="height: 10px;"></div>
    <div class="col-md-12 block">
        <style>
            #receive_chart 
            {
                height: 300px;
            }
            #return_chart
            {
                height: 300px;

            }
        </style>
        
        <script language="javascript" type="text/javascript" src="<?php echo SITE_ROOT.'public/flot/jquery.flot.js'?>"></script>
        <script language="javascript" type="text/javascript" src="<?php echo SITE_ROOT.'public/flot/jquery.flot.pie.js'?>"></script>
        <script language="javascript" type="text/javascript" src="<?php echo SITE_ROOT.'public/flot/jquery.flot.categories.js'?>"></script>
        <div class="col-md-7 block">
            <div class="div-synthesis" style="margin-top: 10px">
                 <div class="div_title_bg-title-top"></div>
                <div class="div_title">
                    <div class="title-border-left"></div>
                    <div class="title-content">
                        <label>Biểu đồ tiếp nhận hồ sơ <?php echo Date('m') . '/' . Date('Y') ?></label>
                    </div>
                    <div class="title-border-right"></div>
                </div>
                <div id="receive_chart">
                </div>
            </div>
        </div>
        <div class="col-md-5 block">
            <div class="div-synthesis" style="margin-top: 10px">
                <div class="div_title_bg-title-top"></div>
                <div class="div_title">
                    <div class="title-border-left"></div>
                    <div class="title-content">
                        <label>Biểu đồ kết quả xử lý hồ sơ toàn tỉnh</label>
                    </div>
                    <div class="title-border-right"></div>
                </div>
                <div id="return_chart">
                </div>
            </div>
        </div>
    </div><!--end col 12-->
</div>
<div class="clear" style="height: 10px"></div>
<div class="col-md-12 footer-weblink">
    <ul>
        <li>
            <a href="<?php echo build_url_survey($this->website_id)?>">
                <img src="<?php echo CONST_SITE_THEME_ROOT?>images/Tham-gio-y-kien.jpg" />
            </a>
        </li>
        <li>
            <a href="#">
               <img src="<?php echo CONST_SITE_THEME_ROOT?>images/ban-do.jpg" />
            </a>
        </li>
        <li>
            <a href="<?php echo build_url_feedback($this->website_id)?>">
                <img src="<?php echo CONST_SITE_THEME_ROOT?>images/gop-y-tthc.jpg" />
            </a>
        </li>
        <li>
            <a href="<?php echo build_url_cq($this->website_id)?>">
                <img src="<?php echo CONST_SITE_THEME_ROOT?>images/hoi-dap.jpg" />
            </a>
        </li>
    </ul>
</div>
<!-- Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="">
              <span style="display: block;  font-size: 1.3em;" aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Theo dõi trực tuyến</h4>
      </div>
      <div class="modal-body">
        <iframe id="login_admin_frame" src="<?php echo $this->get_controller_url('login','admin');?>" style="width: 100% !important;">
        </iframe>
      </div>
    </div>
  </div>
</div>


<script>
    $('a.show_liveboard').click(function(e){
        e.preventDefault();
        var url_live_board = $(this).attr('href');
        var url = '<?php echo $this->get_controller_url('login','admin');?>' + '?u=' + encodeURIComponent(url_live_board) + '&c=THEO_DOI_TRUC_TUYEN';
        $('#loginModal #login_admin_frame').attr('src',url);
        $('#loginModal').modal('toggle');
    });
    
    
    var previousPoint = null, previousLabel = null;
    var data_synthesis = JSON.parse($('#hdn_json_synthesis').val());
    
    $.fn.UseTooltip = function (type) {
        $(this).bind("plothover", function (event, pos, item) {
            if (item) {
                if ((previousLabel != item.series.label) ||(previousPoint != item.dataIndex)) 
                {
                    previousPoint = item.dataIndex;
                    previousLabel = item.series.label;
                    $("#tooltip").remove();

                    var color = item.series.color;
                    var data = 0;
                    if(typeof type == 'undefined' || type == 'bar')
                    {
                        var index = item.dataIndex;
                        data = item.series.data[index][0];
                    }
                    else if(type == 'pie')
                    {
                        data = item.series.data[0][1];
                    }

                     showTooltip(pos.pageX,
                                     pos.pageY,
                                     color,
                                     "<strong>" + item.series.label + "</strong><br>" +
                                     " : <strong>" + number_format(parseInt(data),0) + "</strong>"
                                 );
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
    $(document).ready(function() {
        
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
        //tao bieu do tra ket qua
        var return_chart = $("#return_chart");
        var options = {series: {
                    pie: { 
                            show: true
                    }
            },
            legend: {
                    show: false
            },
            grid: {
                      hoverable: true
                  }
        };
        var data_return = [ {
                data: obj_sum.count_tra_ket_qua_som_han,
                label: "<?php echo __('return')?> - <?php echo __('soon')?>",
                color: '#4DA74D'
            },  {
                data: obj_sum.count_tra_ket_qua_dung_han,
                label: "<?php echo __('return')?> - <?php echo __('on time')?>",
                color: '#AFD8F8'
            },  {
                data: obj_sum.count_tra_ket_qua_qua_dung_han,
                label: "<?php echo __('return')?> - <?php echo __('overdue')?>",
                color: '#CB4B4B'
            }
        ];
        
        $.plot(return_chart, data_return, options);
        $(return_chart).UseTooltip('pie');
        //tao bieu do tiep nhan ho so 
        var data_receive = [];
        var ticks = [];
        
        for(var key in data_synthesis)
        {
            data_receive.push([data_synthesis[key].C_COUNT_TIEP_NHAN,key]);
            ticks.push([key, data_synthesis[key].C_NAME]);
        }
        
        var receive_chart = $("#receive_chart");
        var dataSet = [{ label: "<?php echo __('receive')?>", data: data_receive, color: "#EDC240"}];
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
                ticks: ticks
                
            },
            legend:false,
            grid: {
                          hoverable: true,
                          borderWidth: 1,
                          borderColor: "#545454",
                          autoHighlight: true
                      }
                  };
        $.plot(receive_chart, dataSet, options);
        $(receive_chart).UseTooltip('bar');
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
    function sum_col_table_synthesis(index, searchVal, vfloat)
    {
        if (typeof searchVal == 'undefined')
        {
            searchVal = ' ';
        }

        if (typeof vfloat == 'undefined')
        {
            vfloat = 0;
        }

        var return_val = 0;
        var val = '';
        var selector = 'td:eq(' + index + '):not(.end)';

        //tinh toan du lieu
        $('table.table_synthesis tr').find(selector).each(function() {
            val = $(this).html();
            //format number
            if (vfloat == 0) {
                $(this).html(number_format(parseInt(val), 0));
                return_val = parseInt(val) + return_val;
            }
            else
            {
                $(this).html(number_format(parseFloat(val), 2));
                return_val = parseFloat(val) + return_val;
            }
        });
        
        return return_val;
    }
    function number_format(n,d)
    {
        var number = String(n.toFixed(d).replace('.',','));
        return number.replace(/./g, function(c, i, a) {
                    return i > 0 && c !== "," && (a.length - i) % 3 === 0 ? "." + c : c;
                });
    }
</script>
<?php
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;
$this->render('dsp_footer', $VIEW_DATA, $this->theme_code);
?>
