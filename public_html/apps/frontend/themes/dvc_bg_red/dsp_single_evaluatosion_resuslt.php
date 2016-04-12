
<?php
$VIEW_DATA['title'] = $this->website_name;
$VIEW_DATA['v_banner'] = $v_banner;
$VIEW_DATA['arr_all_website'] = $arr_all_website;
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;

$VIEW_DATA['arr_css'] = array('single-page', 'synthesis', 'component', 'breadcrumb', 'cadre_evaluation');
$VIEW_DATA['arr_script'] = array();
$this->render('dsp_header', $VIEW_DATA, $this->theme_code);

$file_path_menu_top_two = __DIR__ . DS . 'menu_top_two_evaluation.php';
if (is_file($file_path_menu_top_two)) {
    require $file_path_menu_top_two;
}
?>

<div class="col-md-12 " >
    <div class="col-md-12 block">
        <div class="div_title_bg-title-top"></div>
        <div class="div_title">
            <div class="title-border-left" style="margin-left: 0;"></div>
            <div class="title-content">
                <label>
                    Kết quả đánh giá công chức tiếp nhận và giải quyết hồ sơ
                </label>
            </div>
            <div class="title-border-right" style="margin-right: 0;"></div>
        </div>             
    </div>
    <div class="col-md-12">
        <div class="col-md-12 block">
                <?php  if($arr_single_staff): ?>
                <?php
                      $v_staff_name = isset($arr_single_staff['C_NAME'])? $arr_single_staff['C_NAME'] :'';
                      $v_staff_id   = isset($arr_single_staff['PK_LIST'])? $arr_single_staff['PK_LIST'] :'';
                      $v_staff_code = isset($arr_single_staff['C_CODE'])? $arr_single_staff['C_CODE'] :'';
                      $v_xml_data   = isset($arr_single_staff['C_XML_DATA'])? $arr_single_staff['C_XML_DATA'] :'';
                      $v_member_name= isset($arr_single_staff['C_MEMBER_NAME'])? $arr_single_staff['C_MEMBER_NAME'] :'';
                ?>
                <div id="content" class="check-vote">
                    <?php 
                        $today = date('d-m-Y');
                        echo hidden('user_id', $v_staff_id);
                        echo hidden('today', $today);
                    ?>   
                    <!--end button back-->
                </div><!-- end #content -->
                <div class="clear" style="height: 10px;"></div>
                <div class="col-md-6 block logo" style="text-align: right;padding-right: 20px">
                    <div class="avatar-img">
                       <?php 
                                  $v_dir_img_staff_logo = CONST_DIRECT_VOTE_IMAGES . $v_staff_code . '.jpg';
                                   //Anh logo mac dinh
                                   $v_url_img_staff_logo = CONST_URL_VOTE_IMAGES . 'logo_default.jpg';
                                   if (file_exists($v_dir_img_staff_logo)) 
                                   {
                                       $v_url_img_staff_logo = CONST_URL_VOTE_IMAGES . $v_staff_code . '.jpg';
                                   }
                       ?>
                           <img src="<?php echo $v_url_img_staff_logo; ?>" width="200x" height="auto" />
                       </div>
                </div>
                
                <div class="col-md-6 block information">
                    <?php                 
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
                    ?>
                    <div class="col-md-12 block">
                        <div class="col-md-3 block">
                            <b><?php echo __('full name'); ?></b>
                        </div>
                        <div class="col-md-9 block">
                            :&nbsp;<?php echo $v_staff_name; ?>
                        </div>
                    </div>
                    <div class="col-md-12 block">
                        <div class="col-md-3 block">
                            <b><?php echo  __('birthday')?></b>
                        </div>
                        <div class="col-md-9 block">
                            :&nbsp;<?php echo $v_birthday; ?>
                        </div>
                    </div>
                    <div class="col-md-12 block">
                        <div class="col-md-3 block">
                            <b><?php echo __('degree'); ?></b>
                        </div>
                        <div class="col-md-9 block">
                            :&nbsp;<?php echo $v_education; ?>
                        </div>
                    </div>
                    <div class="col-md-12 block">
                        <div class="col-md-3 block">
                            <b><?php echo __('job title'); ?></b>
                        </div>
                        <div class="col-md-9 block">
                            :&nbsp;<?php echo $v_jo_title; ?>
                        </div>
                    </div>
                    <div class="col-md-12 block">
                        <div class="col-md-3 block">
                            <b><?php echo __('units'); ?></b>
                        </div>
                        <div class="col-md-9 block">
                            :&nbsp;<?php echo $v_member_name; ?>
                        </div>
                    </div>
                    <div class="col-md-12 block">
                        <div class="col-md-3 block">
                            <b><?php echo __('Số điểm'); ?></b>
                        </div>
                        <div class="col-md-9 block">
                            :&nbsp;<?php echo ((int)$point > 0) ? $point  : 0; ?>
                        </div>
                    </div>
                    <div class="col-md-12 block">
                        <br/>
                        <div class="col-md-3 block">
                            &nbsp;
                        </div>
                        <div class="col-md-9 block">
                            <a class="btn btn-info" style="color: white" href="<?php echo  build_url_single_staff($v_staff_id);?>" >Đánh giá cán bộ</a>
                        </div>
                    </div>
                </div>
                
                <?php 
                    //End detail staff
                    endif;
                ?>      
                <div class="clear" style="margin-bottom: 30px;"></div>
            <?php if ($arr_result[0]['C_TOTAL_VOTE'] <= 0): ?>
                <span class="note">Hiện tại cán bộ này chưa có lượt đánh giá nào.</span>
            <?php else: ?>
                <div class="result col-md-12 block">
                    <?php
                    foreach ($arr_result as $row):
                            ?>
                    <div class="col-md-12 block item" style="margin-bottom: 5px;">
                                <div class="col-md-2 ">
                                    <label><?php echo $row['C_NAME']; ?></label>
                                </div>
                                <div class="col-md-8 block width-item">
                                    <span class="ass-parent-status">
                                       
                                        <?php
//                                        $width = $row['C_VOTE'] * 700 / $arr_total['TOTAL'];
                                        ?>
                                        <span data-vote="<?php echo $row['C_VOTE']?>" class="item ass-child-status" style="width:0%"></span>
                                       
                                    </span>
                                    
                                </div>
                                <div class="col-md-2 "><?php echo $row['C_VOTE']; ?> đánh giá</div>
                            </div>
                            <?php
                    endforeach;
                    ?> 
                    <div class="clearfix"></div>
                    <hr />
                    <span class="total" >Tổng số lượt đánh giá: <?php echo ($arr_result[0]['C_TOTAL_VOTE'] > 0) ? $arr_result[0]['C_TOTAL_VOTE'] : 0; ?> lượt</span> 
                </div>
            <?php endif; ?>
        </div>
        <!--End #box-check-record-code-->
            <div class="col-md-12 block box-button">
                 <button class="btn btn-info" onclick="window.history.go(-1);"><?php echo __('back')?></button>
            </div>
    </div>
</div>
<script type="text/javascript">
    function btn_filter_onclick() {
        $('#frmMain').submit();
    }
    /**
      * Comment
    */
   $(document).ready(function()
   {
      build_result(); 
      $(window).resize(build_result);
   });
    function build_result() 
    {
        var total_vote = '<?php echo $arr_result[0]['C_TOTAL_VOTE']?>';
        total_vote = (parseInt(total_vote) > 0) ? parseInt(total_vote) : 0;
        $('.item.ass-child-status').each(function(i,val){
            var width =0;
            var full_width = $(this).parents('.width-item').width()|| 0; 
            var data_vote = $(this).attr('data-vote') || 0;
            num_vote  = (parseInt(data_vote)/ total_vote)* 100 ;
            if(parseInt(data_vote) > 0)
            {
                width  = (parseInt(data_vote)* full_width) / total_vote;
            }
            $(this).css({"width":width});
            if(num_vote > 0)
            {
                $(this).html(number_format(num_vote,0) + '%').css('color','#ffffff');
            }
            else
            {
                    $(this).html(number_format(num_vote,0) + '%').css('color','#000000');
            }
            
        });
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