<?php
Session::init();
?>
<?php
//du lieu header
$VIEW_DATA['title']                 = $this->website_name;
$VIEW_DATA['v_banner']              = $v_banner;
$VIEW_DATA['arr_all_website']       = $arr_all_website;
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;

$VIEW_DATA['v_keywords']            = '';
$VIEW_DATA['v_description']         = '';
?>
<?php
$VIEW_DATA['arr_css'] = array('synthesis', 'single-survey', 'component', 'single-page');
$VIEW_DATA['arr_script'] = array();
$this->render('dsp_header', $VIEW_DATA, $this->theme_code);

$arr_single_survey      = isset($arr_single_survey) ? $arr_single_survey :array();
$arr_all_member_survey  = isset($arr_all_member_survey) ? $arr_all_member_survey : array();

$v_sel_member_survey    = get_request_var('sel_survey',0);
//Trang da tra loi cau hoi

if($v_sel_member_survey <= 0)
{
    $v_sel_member_survey = isset($arr_single_survey[0]['FK_SURVEY']) ? $arr_single_survey[0]['FK_SURVEY'] : 0;
}
$v_list_id_question     = isset($arr_single_survey[0]['C_LIST_PK_SURVEY_QUESTION']) ? $arr_single_survey[0]['C_LIST_PK_SURVEY_QUESTION'] : '';
?>
   
    <div class="col-md-12 block" id="single-survey">
            <div class="col-md-3" id="left-sidebar">
                    <?php
                        $v_widget_path = __DIR__.DS.'dsp_widget.php';
                        if(is_file($v_widget_path))
                        {
                            require $v_widget_path;
                        }
                    ?>
                </div>
                <!--End #left-sidebar-->

        <div class="col-md-9" >
            <div class="col-md-12 block" id="box-content">
            <div class="col-md-12 box-content block">
                  <div class="div_title_bg-title-top"></div>
                <div class="div_title">
                    <div class="title-border-left"></div>
                    <div class="title-content">
                          <label ><?php echo __('vote referendum')?></label>    
                    </div>
                </div>
              </div>
            
            <div id="box-member" style="" class="col-md-12">
                <form name="frmMember" method="GET">                   
                <div class="Row">
                    <?php if(sizeof($arr_all_member_survey) >0) :; ?>
                    
                    <div class="right-Col col-md-12 block">
                        <select name="sel_survey" id="sel_survey" onchange=" this.form.submit()">
                            <?php for($i = 0;$i <sizeof($arr_all_member_survey); $i ++):; ?>
                                <?php
                                    $v_member_id        = $arr_all_member_survey[$i]['PK_MEMBER'];
                                    $v_member_name      = $arr_all_member_survey[$i]['C_NAME'];
                                    $v_xml_survey       = $arr_all_member_survey[$i]['C_XML_SURVEY'];
                                    ?>

                                <?php if(trim($v_xml_survey) != ''):
                                    echo " <optgroup label='&nbsp;&nbsp;$v_member_name&nbsp;&nbsp;'>";
                                        ?>
                                            <?php
                                                 $dom   = simplexml_load_string($v_xml_survey);
                                                 $xpath =  '//item';
                                                 $arr_survey = $dom->xpath($xpath);
                                                 if(sizeof($arr_survey) > 0)
                                                 {
                                                     foreach ($arr_survey as $single_survey)
                                                     {
                                                        $v_survey_id    = (string)$single_survey->attributes()->PK_SURVEY;
                                                        $v_survey_name   = (string)$single_survey->attributes()->C_NAME; 
                                                        $v_selected  = ($v_sel_member_survey == $v_survey_id) ? 'selected' : '';
                                                        echo "<option $v_selected value='$v_survey_id'>".  get_leftmost_words($v_survey_name, 20)."</option>";
                                                     }
                                                 }
                                            ?>
                                        <?php  echo ' </optgroup>';
                                    endif;
                                    ?>
                        <?php endfor;?>
                     </select>                    
                    </div>
                    <?php endif;?>
                </div>
                </form>
            </div>
            <div class="col-md-12 block" id="title-survey">
                <h1><?php echo __('vote referendum')?></h1>
            </div>
                <div class="clear" style="height: 10px;"></div>
           
                <?php
                    $v_survey_name = isset($arr_single_survey[0]['C_SURVEY_NAME']) ? $arr_single_survey[0]['C_SURVEY_NAME'] : '';
                    $v_member_name = isset($arr_single_survey[0]['C_MEMBER_NAME']) ? $arr_single_survey[0]['C_MEMBER_NAME'] : '';
                  
                     echo "<div class=\"col-md-12 block\" id=\"title-member\">
                                    <h2><span style='color:black'> Chủ đề: </span>$v_survey_name</h2>
                                    <h2 style='color: rgb(2, 177, 40);font-size: 1.4em;margin-top: 5px;display:none' id='message-success'>".__('you sent answer  successfully!')."</h2>
                            </div>";
                     
                ?>
                
            <!--End #box-member-->
             <?php if(sizeof($arr_single_survey) > 0): ?>
            <form name="frmMain" id="frmMain" method="POST" action="" style="display:<?php echo ($v_answered == 1) ? 'none' :'block' ?>" >
                 <?php echo $this->hidden('hdn_list_question_id', $v_list_id_question); ?>
                <?php echo $this->hidden('hdn_survey_id', ''); ?>
           
            <div id="box-questions" class="col-md-12 block">
                <?php for($i =0; $i<sizeof($arr_single_survey);$i ++):; ?>
                    <?php 
                        $v_question_id      = $arr_single_survey[$i]['PK_SURVEY_QUESTION'];
                        $v_survey_id        = $arr_single_survey[$i]['FK_SURVEY'];
                        $v_question_name    = html_entity_decode($arr_single_survey[$i]['C_NAME']);
                        $v_question_type    = $arr_single_survey[$i]['C_TYPE'];
                        $v_xml_answer       = $arr_single_survey[$i]['C_XML_ANSWER'];
                    ?>
                    <div class="item">
                       <div class="question-title">
                           <h3><?php echo ($i +1).'.&nbsp;'. $v_question_name; ?></h3>
                       </div>
                        <div class="clear"></div>
                        <?php
                            switch ($v_question_type):
                                case 0:
                                case 1:
                                    if(trim($v_xml_answer) != '')
                                    {
                                        $dom        = simplexml_load_string($v_xml_answer);
                                        $xpath      =  '//item';
                                        $arr_answer = $dom->xpath($xpath);
                                        for($o =0;$o <sizeof($arr_answer);$o ++)
                                        {
                                            $v_answer_name = isset($arr_answer[$o]['C_NAME']) ? html_entity_decode($arr_answer[$o]['C_NAME']) : '';
                                            $v_answer_id   =  isset($arr_answer[$o]['PK_SURVEY_ANSWER']) ? $arr_answer[$o]['PK_SURVEY_ANSWER'] : 0;
                                            if($v_question_type ==0)
                                            {
                                               echo "   <div class=\"box-answer col-md-12\">
                                                            <label><input value='chk_answer_$v_answer_id' name=\"chk_answer_{$v_question_id }[]\" class=\"chk_answer_$v_answer_id\" type=\"checkbox\"> &nbsp;$v_answer_name</label><br/>
                                                        </div>";
                                            }
                                            if($v_question_type ==1)
                                            {
                                               echo "   <div class=\"box-answer col-md-12 \">
                                                            <label><input  value='$v_answer_id'  name=\"rad_answer_{$v_question_id}\" class=\"rad_answer_$v_answer_id\" type=\"radio\"> &nbsp;$v_answer_name </label><br/>
                                                        </div>";
                                            }
                                        }
                                        echo "<input type='hidden' value='$v_question_type' name='type_$v_question_id'>";
                                    }
                                    break;
                                case 2:
                                case 3:
                                         if($v_question_type ==2)
                                            {
                                               echo "    <div class=\"col-md-5 \">
                                                                <strong>".__('enter the answer').":</strong>
                                                                <input  name=\"txt_answer_$v_question_id\" id=\"rad_answer\" type=\"text\" value=\"\">
                                                            </div>  ";
                                            }
                                            if($v_question_type ==3)
                                            {
                                               echo "   <div class=\"box-answer col-md-12\">
                                                            <div class=\"col-md-12 block\">
                                                                <strong> ".__('enter the answer').":</strong>
                                                                <textarea name=\"txt_answer_$v_question_id\" id=\"txt_anser\"  ></textarea>
                                                            </div>
                                                        </div>";
                                            }
                                            echo "<input type='hidden' value='$v_question_type' name='type_$v_question_id'>";
                                    break;
                            endswitch;
                        ?>
                        <div class="clear"></div>
                         </div>
                <!--End #box-question-->
                <?php endfor;?>
                <div class="col-md-6">
                    <label style="font-weight: bold" for="recaptcha_response_field" class="col-md-5 control-label"><?php echo __('verification Code')?> <span class="required">(*)</span></label>
                    <div class="col-md-7 " >
                           <?php echo recaptcha_get_html(_CONST_RECAPCHA_PUBLIC_KEY) ?>
                        <label id="error_capcha"  style="display: none;color: red; width:450px">Bạn chưa nhập mã xác nhận hoặc mã xác nhận chưa đúng!</label>
                    
                    <div class="col-md-12" id="btn-submit" style="margin-top: 20px;" >
                        <button style="cursor: pointer"  class="btn btn-info" onclick="btn_answer_submit_onclick()" type="button"><?php echo __('send answers');?></button>   
                    </div>
                        <div id="loading" style="margin-top: 10px;">
                        </div>
                    </div>
                   
                </div>
                
            </div>
            </form>
               <?php endif;?>
        </div>
      </div>
        <div class="clear"></div>
        <!--End #Main-content-->
    </div>
<script>
    function btn_answer_submit_onclick() 
    {
        
        var survey_id = $('#sel_survey').val();
        $('#hdn_survey_id').val(survey_id);
        var url = '<?php echo build_url_survey($this->website_id,$v_sel_member_survey); ?>';
        
        $.ajax({
            type: "POST",
            url: url,
            data: $('#frmMain').serialize(),
            beforeSend: function() 
            {
                     var img ='<center><img src="<?php echo SITE_ROOT;?>public/images/loading.gif"/></center>';
                     $('#loading').html(img);                     
            },
            success: function(data){
            if(typeof(data) != 'undefined' && data.length >0)
            {
                if(data != '1')
                 {
                     if(data == 'capcha_error')
                        {
                            $('#error_capcha').show();
                            $('#loading').html('');
                        }
                        else
                        {
                            console.log(data);
//                            alert(data);
                            $('#frmMain').show();
                            $('#message-success').hide();
                        }
                        $('#recaptcha_reload').trigger("click");
                          return false;
                 }
                 else
                 {
                     $('#error_capcha').hide();
                     $('#message-success').show();
                     $('#frmMain').hide();
                 }
                 $('#error_capcha').hide();
                 $('#loading').html('');
            }
            }
          });
    }
</script>
<?php
$VIEW_DATA['arr_all_menu_position'] = $arr_all_menu_position;
$this->render('dsp_footer', $VIEW_DATA, $this->theme_code);