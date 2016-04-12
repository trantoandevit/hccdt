<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed'); ?>
<?php
$VIEW_DATA['title']                 = $this->website_name;
$VIEW_DATA['v_banner']              = $v_banner;
$VIEW_DATA['arr_all_website']       = $arr_all_website;
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;

$VIEW_DATA['arr_css'] = array('single-page', 'synthesis', 'component','cadre_evaluation');
$VIEW_DATA['arr_script'] = array();
$this->render('dsp_header', $VIEW_DATA, $this->theme_code);

?>
<?php
    $arr_single_staff         = isset($arr_single_staff) ? $arr_single_staff : array();
    $v_member_nam             = isset($arr_single_staff['C_MEMBER_NAME']) ? $arr_single_staff['C_MEMBER_NAME'] :'';
    $v_staff_nam              = isset($arr_single_staff['C_NAME']) ? $arr_single_staff['C_NAME'] :'';
    $v_village_short_code     = isset($arr_single_staff['C_SHORT_CODE']) ? $arr_single_staff['C_SHORT_CODE'] :'';
?>

<?php
// add menu top-two
    $file_path_menu_top_two = __DIR__ . DS . 'menu_top_two_evaluation.php';
    if (is_file($file_path_menu_top_two)) {
        require $file_path_menu_top_two;
    }
?>
<?php
    echo hidden('hdn_village_short_code', $v_village_short_code);
    echo hidden('hdn_criterial', '');
?>
<div class="col-md-12 content"  id="single-page">
        <div class="col-md-12" id="main-page">
            <div class="col-md-12 block">
                <div class="col-md-12 block title" style="text-align: center">
                    <h2 style="color: #254F77;">Đánh giá công chức tiếp nhận và giải quyết hồ sơ</h2>
                </div>
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
                <div class="col-md-3 logo" style="text-align: right;padding-right: 20px">
                    <div class="avatar-img" style="text-align: center">
                       <?php 
                                  $v_dir_img_staff_logo = CONST_DIRECT_VOTE_IMAGES . $v_staff_code . '.jpg';
                                   //Anh logo mac dinh
                                   $v_url_img_staff_logo = CONST_URL_VOTE_IMAGES . 'logo_default.jpg';
                                   if (file_exists($v_dir_img_staff_logo)) 
                                   {
                                       $v_url_img_staff_logo = CONST_URL_VOTE_IMAGES . $v_staff_code . '.jpg';
                                   }
                       ?>
                           <img src="<?php echo $v_url_img_staff_logo; ?>" width="200px" height="266px" />
                       </div>
                </div>
                <div class="col-md-9 information">
                    <table style="width: 100%; border: 1px solid #CCCCCC;height: 266px;">
                        <colgroup>
                            <col width="30%" />
                            <col width="70%" />
                        </colgroup>
                        <tr style="border-bottom: 1px solid #CCC">
                            <td style="text-align: center;color: #254F77;font-weight: bold">Thông tin cá nhân</td>
                            <td style="border-left: 1px solid #ccc">
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
                                <div class="col-md-12">
                                    <div class="col-md-3 ">
                                        <b><?php echo __('full name'); ?></b>
                                    </div>
                                    <div class="col-md-9 ">
                                        :&nbsp;<?php echo $v_staff_name; ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-3 ">
                                        <b><?php echo  __('birthday')?></b>
                                    </div>
                                    <div class="col-md-9 ">
                                        :&nbsp;<?php echo $v_birthday; ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-3 ">
                                        <b><?php echo __('degree'); ?></b>
                                    </div>
                                    <div class="col-md-9 ">
                                        :&nbsp;<?php echo $v_education; ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-3 ">
                                        <b><?php echo __('job title'); ?></b>
                                    </div>
                                    <div class="col-md-9 ">
                                        :&nbsp;<?php echo $v_jo_title; ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-3 ">
                                        <b><?php echo __('units'); ?></b>
                                    </div>
                                    <div class="col-md-9 ">
                                        :&nbsp;<?php echo $v_member_name; ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr >
                            <td style="text-align: center;color: #254F77;font-weight: bold">Kết quả đánh giá</td>
                            <td style="border-left: 1px solid #ccc">
                                <div class="col-md-12">
                                    <div class="col-md-3 ">
                                        <b><?php echo __('Số điểm'); ?></b>
                                    </div>
                                    <div class="col-md-9 ">
                                        :&nbsp;<?php echo ((int)$point > 0) ? $point  : 0; ?>
                                    </div>
                                </div>
                                <?php 
                                    foreach($arr_ressult_vote as $row):
                                ?>
                                <div class="col-md-12">
                                    <div class="col-md-3">
                                        <b><?php echo $row['C_NAME']?></b>
                                    </div>
                                    <div class="col-md-6 width-item">
                                        <span class="ass-parent-status" style="background-color: white">
                                            <span data-vote="<?php echo $row['C_VOTE']?>" class="item ass-child-status" style="height: 20px;"></span>
                                            <span class="evaluation_rate" style="position: absolute;top: 0px;color:white">

                                            </span>
                                        </span>
                                    </div>
                                    <div class="col-md-3">
                                        <?php echo $row['C_VOTE']; ?> bình chọn
                                    </div>
                                </div>
                                <?php endforeach;?>
                            </td>
                        </tr>
                    </table>
                </div>
                <?php endif; ?>                 
                <div id="list-item-vote" class="col-md-12 block">
                     <div id="list">
                            <ul>
                                <?php
                                    if(is_array($criterail)):
                                        foreach($criterail as $row):
                                ?>
                                    <li id="vote-<?php echo $row['PK_LIST']; ?>" class="update_vote" data-id="<?php echo $row['PK_LIST']; ?>" onclick="criterial_onclick(this)">
                                        <img src="<?php echo FULL_SITE_ROOT; ?>public/images/<?php echo $row['IMAGE_LINK']; ?>"  />
                                        <a href="javascript:void(0)"><?php echo $row['C_NAME']; ?>
                                            <span >
                                                <?php echo __('total vote')?>
                                                <br/><strong><?php echo isset($row['C_VOTE']) ? (((int)$row['C_VOTE'] >0)? $row['C_VOTE']: 0) :0; ?></strong>
                                                <center class="criterial" style="display: none;">
                                                    <span style="width: 40%;height: 3px;background: #B20000">&nbsp;</span>
                                                </center>
                                            </span>
                                        </a>
                                    </li>
                                <?php
                                        endforeach;
                                    endif;
                                ?>
                            </ul>
                        </div>     
                </div>
                <div id="box-check-record-code" class="col-md-12 block">
                     <form name="frmMain_check_record" class="form-horizontal" id="frmMain_check_record" action="" method="POST">
                         <div class="form-group">
                             <div class="row">
                                 <label class="col-md-2">&nbsp;</label>
                                 <div class="col-md-8">
                                     <div class="col-md-3 block" style="padding-left: 0px;line-height: 34px;">
                                         Vui lòng nhập mã hồ sơ <span class="required">(*)</span>
                                     </div>
                                     <div class="col-md-6">
                                         <input  type="text" class="form-control" name="txt_record_code" id="txt_record_code">
                                     </div>
                                     <div class="col-md-2">
                                         <button type="button" class="btn btn-success" id="btn_evaluation" onclick="update_vote_onclick()">Đánh giá</button>
                                     </div>
                                 </div>
                                 <div class="col-md-3">&nbsp;</div>
                                 <label id="record-check-code-error" style="display: none; color: red; margin-top: 10px;">Mã hồ sơ không chính xác xin vui lòng kiểm tra lại</label>
                             </div>
                         </div>
                    </form>
                </div>
                <!--End #box-check-record-code-->
                <div class="col-md-12 box-button">
                     <button class="btn btn-info" id="btn-check-record" onclick="window.history.go(-1);"><?php echo __('back')?></button>
                     <div id="div_load_img"></div>
                </div>
            </div>
        </div>
</div>
<script type="text/javascript">
    $(document).ready(function()
    {
       build_result(); 
    });
    function build_result() 
    {
        var total_vote = '<?php echo $arr_ressult_vote[0]['C_TOTAL_VOTE']?>';
        total_vote = (parseInt(total_vote) > 0) ? parseInt(total_vote) : 0;
        $('.ass-child-status').each(function(i,val){
            var width = 0;
            var data_vote = $(this).attr('data-vote') || 0;
            if(parseInt(data_vote) > 0)
            {
                width  = (parseInt(data_vote)/ total_vote)* 100 ;
            }
            $(this).css('width',width + '%');
            $(this).parent().find('.evaluation_rate').html(number_format(width,0) + '%');
            if(width < 10)
            {
                $(this).parent().find('.evaluation_rate').css('color','black')
            }
        });
    }
    function criterial_onclick(criterial)
    {
        $('.criterial').hide();
        $(criterial).find('.criterial').show();
        $('#hdn_criterial').val($(criterial).attr('data-id'));
    }
    function number_format(n,d)
    {
        var number = String(n.toFixed(d).replace('.',','));
        return number.replace(/./g, function(c, i, a) {
                    return i > 0 && c !== "," && (a.length - i) % 3 === 0 ? "." + c : c;
                });
    }
    function update_vote_onclick() 
    {
        var today              = $('#today').val();
        var user_id            = $('#user_id').val();
        var url                = '<?php echo build_url_evaluation_update(); ?>';
        var fk_criterial       = $('#hdn_criterial').val();
        var village_short_code = $('#hdn_village_short_code').val();
        var record_code        = $('#txt_record_code').val();
        
        if(fk_criterial == '' || fk_criterial == 'undefined')
        {
            alert('Bạn cần chọn tiêu chí đánh giá !');
            return false;
        }
        
        $.ajax({
            type: 'POST',
            url: url,
            data: {user_id:user_id,today:today,
                    fk_criterial:fk_criterial,village_short_code:village_short_code,
                    record_code:record_code},
            beforeSend: function() {
                $('#btn_evaluation').attr("disabled", "disabled");
                var img ='<center><img src="<?php echo SITE_ROOT;?>public/images/loading.gif"/></center>';
                $('#div_load_img').html(img);
            },
            success: function(data) 
            {
                $('#btn_evaluation').removeAttr("disabled");
                $('#div_load_img').html('');
                
                if(typeof(data) != 'undefined' && data == '1') 
                {
                    alert('<?php echo __('successful vote'); ?>');
                    location.reload();
                }
                else if(data == '0')//ma hs ko dung
                {
                    $('#record-check-code-error').show();
                }
                else if(data == '-1')//da thuc hien danh ma
                {
                    alert('<?php echo __('you further evaluation, please wait 30 seconds') ?>');
                }
            }
        });
    }   
      
</script>
<?php
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;
$this->render('dsp_footer', $VIEW_DATA, $this->theme_code);
