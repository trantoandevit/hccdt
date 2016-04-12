<?php
$this->template->title = 'Quản lý câu hỏi khảo sát chất lượng';
$this->template->display('dsp_header.php');

//don vi thanh vien
$arr_all_village  =  $arr_all_member['arr_all_village'];
$arr_all_district =  $arr_all_member['arr_all_district'];

$arr_all_question = isset($arr_all_question) ? $arr_all_question: array();
$arr_single_survey = isset($arr_single_survey)? $arr_single_survey :array();

if(sizeof($arr_single_survey) >0)
{
    $v_survey_id     = $arr_single_survey['PK_SURVEY'];
    $v_question_name = html_entity_decode($arr_single_survey['C_NAME']);
    $v_begin_date    = $arr_single_survey['C_BEGIN_DATE'];
    $v_begin_date    = jwDate::yyyymmdd_to_ddmmyyyy($v_begin_date);
    
    $v_end_date      = $arr_single_survey['C_END_DATE'];
    $v_end_date    = jwDate::yyyymmdd_to_ddmmyyyy($v_end_date); 
    
    $v_status        = $arr_single_survey['C_STATUS'];
    $v_member_id     = $arr_single_survey['FK_MEMBER'];
}
else
{
    $v_survey_id     = 0;
    $v_question_name = isset($_REQUEST['txt_question_name']) ? $_REQUEST['txt_question_name'] : '';
    $v_begin_date    = isset($_REQUEST['txt_begin_date']) ? $_REQUEST['txt_begin_date'] : '';
    $v_end_date      = isset($_REQUEST['txt_end_date']) ? $_REQUEST['txt_end_date'] : '';
    $v_status        = isset($_REQUEST['chk_status']) ? 1 : 0;
    $v_member_id     = isset($_REQUEST['sql_member']) ? $_REQUEST['sql_member'] : 0;
}

$v_list_id_question_old = '';
for($i =0;$i <sizeof($arr_all_question);$i ++)
{
    if($i== (sizeof($arr_all_question) -1))
    {
        $v_list_id_question_old .= $arr_all_question[$i]['PK_SURVEY_QUESTION'];
    }
    else
    {
        $v_list_id_question_old .= $arr_all_question[$i]['PK_SURVEY_QUESTION'].', ';
    }
    
}

?>
<form name="frmMain" id="frmMain" method="POST" action="">
    <?php
        echo $this->hidden('controller',$this->get_controller_url());
        echo $this->hidden('hdn_item_id',$v_survey_id);
        echo $this->hidden('hdn_single_method','dsp_single_survey');
        echo $this->hidden('hdn_single_questsion_method','dsp_single_question');
        echo $this->hidden('hdn_update_method','update_survey');
        echo $this->hidden('hdn_dsp_all_method','dsp_all_survey');
        echo $this->hidden('XmlData','');
        
        echo $this->hidden('hdn_list_old_item_id',$v_list_id_question_old);
        echo $this->hidden('hdn_list_new_item_id','');
        echo $this->hidden('hdn_list_question_delete_id','');
        echo $this->hidden('hdn_list_answer_delete_id','');
    ?>
    <h2 class="module_title" style="width: auto">Chi tiết câu hỏi khảo sát</h2>
<div id="box-survey">
    <div id="box-infor">
        <div class="full-width">
            <span class="title-general-infor"><?php echo __('general info'); ?> </span>
            <div class="Row">
                <div class="left-Col">Tên câu hỏi</div>
                <div class="right-Col">
                    <input type="text" name="txt_survey_name" id="txt_survey_name" 
                                value="<?php echo $v_question_name;?>" 
                                data-allownull="no" data-validate="text" 
                                data-name="<?php echo __('question name')?>" 
                                data-xml="no" data-doc="no" 
                                autofocus="autofocus" 
                                size="70">
                </div>
            </div>
            <div class="Row">
                <div class="left-Col">Ngày bắt đầu</div>
                <div class="right-Col"><input type="text" name="txt_begin_date" id="txt_begin_date" 
                                value="<?php echo $v_begin_date;?>" 
                                data-allownull="no" data-validate="date" 
                                data-name="<?php echo __('ngày bắt đầu')?>" 
                                data-xml="no" data-doc="no" 
                                autofocus="autofocus" 
                                size="30">
                    &nbsp;<img src="<?php echo SITE_ROOT?>apps/admin/images/calendar.png" onclick="DoCal('txt_begin_date')">
                    
                </div>
            </div>
           <div class="Row">
                <div class="left-Col">Đến ngày</div>
                <div class="right-Col">
                    <input type="text" name="txt_end_date" id="txt_end_date"
                                value="<?php echo $v_end_date;?>" 
                                data-allownull="no" data-validate="date" 
                                data-name="<?php echo __('ngày kết thúc')?>" 
                                data-xml="no" data-doc="no" 
                                autofocus="autofocus" 
                                size="30">
                     &nbsp;<img src="<?php echo SITE_ROOT?>apps/admin/images/calendar.png" onclick="DoCal('txt_end_date')">
                     <br />
                     <label style="color: red;display: none"  id="error-date"><?php echo __('end date not smaller start date');?></label>
                </div>
            </div>
            
           <div class="Row">
                <div class="left-Col">Đơn vị khảo sát</div>
                <div class="right-Col">
                        <select name="sel_member" id="sel_member">
                            <option value="0">Cổng thông tin</option>
                            <?php 
                                foreach($arr_all_district as $arr_district)
                                {
                                    $v_name = $arr_district['C_NAME'];
                                    $v_id   = $arr_district['PK_MEMBER'];
                                    $v_checked ='';
                                    $v_checked = ($v_id == $v_member_id)?' selected':'';
                                    echo "<option $v_checked value='$v_id'>$v_name</option>";
                                    foreach($arr_all_village as $key => $arr_village)
                                    {
                                       $v_village_name = $arr_village['C_NAME'];
                                       $v_village_id   = $arr_village['PK_MEMBER'];
                                       $v_parent_id    = $arr_village['FK_MEMBER'];
                                       if($v_parent_id != $v_id)
                                       {
                                           continue;
                                       }
                                       $v_checked = ($v_village_id == $v_member_id)?' selected':'';
                                       echo "<option $v_checked value='$v_village_id'> ----- $v_village_name</option>";
                                       unset($arr_all_village[$key]);
                                    }
                                }
                                
                                ?>
                        </select>
                        
                </div>
            </div>
        <!--End memeber-->
             <div class="Row">
                <div class="left-Col">&nbsp;</div>
                <div class="right-Col">
                    <label><input type="checkbox" name="chk_status" <?php echo ($v_status ==1) ? 'checked' :''; ;?> id="chk_status" > Hiển thị</label>
                </div>
            </div>
        </div>
    </div>
    <!--End #box-infor-->
    <div class="button-area">
            <input type="button" class="ButtonAccept" name="btn_update" id="btn_update" class="ButtonAccept" value="<?php echo __('update');?>" onclick="  btn_update_onclick();"/>
            <input type="button" class="ButtonBack" name="btn_back" id="btn_cancel" class="ButtonBack" value="<?php echo __('go back'); ?>" onclick="btn_back_onclick();"/>
    </div>
    <div id="box-questions">
            <span class="title-general-list"><?php echo __('questions list'); ?> </span>
        <div id="box-answer">
            <?php 
            
                for($i=0 ;$i< sizeof($arr_all_question); $i ++)
                {
                    $v_question_id   = trim($arr_all_question[$i]['PK_SURVEY_QUESTION']); 
                    $v_question_name = html_entity_decode($arr_all_question[$i]['C_NAME']);
                    $v_xml_answer    = $arr_all_question[$i]['C_XML_ANSWER'];                    
                    $v_question_type = $arr_all_question[$i]['C_TYPE'];
                    
                    if($v_question_type == 0)
                    {
                        $v_question_type_name = 'Câu hỏi dạng checkbox( Nhiều lựa chọn)';
                    }
                    elseif($v_question_type == 1)
                    {
                        $v_question_type_name = 'Câu hỏi dạng raido(một lựa chọn)';
                    }
                    elseif($v_question_type == 2)
                    {
                        $v_question_type_name = 'Câu hỏi dạng single text(Hiển thị dạng text box khi trả lời)';
                    }
                    else 
                    {
                        $v_question_type_name = 'Câu hỏi dạng text(Hiển thị dạng textArea khi trả lời)';
                    }
                    
                    if(trim($v_xml_answer) == '')$v_xml_answer = '<data></data>';
                    $dom = simplexml_load_string($v_xml_answer);
                    $v_xpath = '//item';
                    $arr_answer = $dom->xpath($v_xpath);
                    
                    $question_resutls =array();
                    for($o = 0;$o< sizeof($arr_answer); $o ++)
                    {
                                    $v_answer_name      = html_entity_decode((string)$arr_answer[$o]['C_NAME']);
                                    $v_answer_id        =  (string)$arr_answer[$o]['PK_SURVEY_ANSWER'];
                                    $question_resutls[]   = array(
                                                                        'answer_name'=>$v_answer_name
                                                                        ,'answer_id' => $v_answer_id
                                                                );
                    }
                    $arr_answer_old         = json_encode($question_resutls);
                    $v_url_question_name    = urlencode($v_question_name);
                    $v_url_arr_answer_old   = urlencode($arr_answer_old);
                   
                    $v_url = "question_id=$v_question_id&amp;question_type=$v_question_type&amp;question_name=$v_url_question_name&amp;question_status=old&amp;question_answer=$v_url_arr_answer_old";
                   
                    ?>
            <div class="box-question_answer" id="question_old_<?php echo $v_question_id; ?>" data-new="0" data-old="<?php echo $v_question_id; ?>">
                <div class="div-question">
                    <div class="Row cat-question">
                        <div class="left-Col">Loại câu hỏi</div>
                        <div class="right-Col">
                            <input type="text" value="<?php echo $v_question_type_name;?>" disabled="true" name="txt_question_type_name_old_<?php echo $v_question_id ?>" class="txt_question_type_name" >
                            <input type="hidden"   value="<?php echo $v_question_type;?>" name="txt_question_type_old_<?php echo $v_question_id ?>" class="txt_question_type" >
                            <input type="hidden"  name="txt_url" class="txt_url" value='<?php echo $v_url;?>' >
                        </div>
                    </div>
                    <div class="Row question">
                        <div class="left-Col">Câu hỏi</div>
                        <div class="right-Col">
                            <input type="text"  value="<?php echo $v_question_name; ?>" disabled="true"  name="txt_question_name_old_<?php echo $v_question_id ?>" class="txt_question_name" >
                        </div>
                    </div>
                     <!--End Cau hoi-->  
                <?php
                    if(trim($v_xml_answer)!= '')
                    {
                        $dom = simplexml_load_string($v_xml_answer);
                        $v_xpath = '//item';
                        $arr_answer = $dom->xpath($v_xpath);
                        
                        $html = '';
                        for($o = 0;$o< sizeof($arr_answer); $o ++)
                        {
                            $v_answer_name = html_entity_decode((string)$arr_answer[$o]['C_NAME']);
                            $v_answer_id   =  (string)$arr_answer[$o]['PK_SURVEY_ANSWER'];

                            if(trim($v_answer_name) != '')
                            {
                                if($o == 0)
                                {
                                    echo '<div class="Row answer">
                                            <div class="left-Col">Câu trả lời</div>
                                            <div class="right-Col answer">
                                                <input type="text" disabled="true" value="'.$v_answer_name.'"  name="txt_answer_name_old_'.$v_question_id.'[]" class="txt_answer_name" >
                                                <input type="hidden" value="'.$v_answer_id.'" name="text_question_answer_id_old_'. $v_question_id.'[]" id="text_question_result" size="80">
                                            </div>
                                        </div>';
                                }
                                else
                                {
                                    echo ' <div class="Row answer">
                                            <div class="left-Col">&nbsp;</div>
                                            <div class="right-Col answer">
                                                <input type="text" disabled="true"  value="'.$v_answer_name.'" name="txt_answer_name_old_'.$v_question_id.'[]" class="txt_answer_name" >
                                                <input type="hidden" value="'.$v_answer_id.'" name="text_question_answer_id_old_'. $v_question_id.'[]" id="text_question_result" size="80">
                                            </div>
                                        </div>';
                                }
                            }
                        }
                        //End for                     
                    }
                ?>
                   
                </div>
                 <div class="Row">
                       <div class="left-Col">&nbsp;</div>
                       <div class="right-Col answer">
                           <button type="button" class="ButtonDelete" onclick="btn_delete_question_onclick(this);">Xóa</button>
                             <button  class="ButtonEdit" type="button" onclick="btn_onclick_edit_question(this);" data-status="old">Sửa câu hỏi</button>
                       </div>
                   </div>
            </div>
            <!--End #question_0-->
            
            <?php
                }
            ?>
        </div>
            <button style="float: right" type="button" class="ButtonAdd" onclick="btn_add_question_onclick();">Thêm câu hỏi</button>
        <!--End #box-answer-->
        <div id="template-answer" style="display: none">
            <div class="box-question_answer" id="question_old_" data-new="0" data-old="0">
                <div class="update-old">
                <div class="div-question">
                    <div class="Row cat-question">
                        <div class="left-Col">Loại câu hỏi</div>
                        <div class="right-Col">
                            <input type="text" disabled="true" name="txt_question_type_name" class="txt_question_type_name" >
                            <input type="hidden"  name="txt_question_type" class="txt_question_type" >
                            <input type="hidden"  name="txt_url" class="txt_url" value="" >
                        </div>
                    </div>
                    <div class="Row question">
                        <div class="left-Col">Câu hỏi</div>
                        <div class="right-Col">
                            <input type="text" disabled="true"  name="txt_question_name" class="txt_question_name" >
                        </div>
                    </div>
                    <div class="Row answer">
                       <div class="left-Col">Câu trả lời</div>
                       <div class="right-Col answer">
                           <input type="text" disabled="true"  name="txt_answer_name" class="txt_answer_name" >
                           <input type="hidden" value="0" name="text_question_answer_id_old_" class="txt_answer_id" size="80">
                       </div>
                   </div>
                </div>
                 <div class="Row">
                       <div class="left-Col">&nbsp;</div>
                       <div class="right-Col answer">
                           <button type="button" class="ButtonDelete" onclick="btn_delete_question_onclick(this);">Xóa</button>
                           <button  class="ButtonEdit btn_question_edit" type="button" onclick="btn_onclick_edit_question(this);">Sửa câu hỏi</button>
                       </div>
                   </div>
               </div>
            </div>
            <!--End #question_0-->
        </div>
        <!--End #template-answer-->
    </div>
    <!--End #box-question-->
   <div class="button-area">
            <input type="button" name="btn_update"  id="btn_update" class="ButtonAccept" value="<?php echo __('update');?>" onclick="  btn_update_onclick();"/>
            <input type="button" name="btn_back" id="btn_cancel" class="ButtonBack" value="<?php echo __('go back'); ?>" onclick="btn_back_onclick();"/>
    </div>
</div> <!--End #box-list-questions-->
<!--End #box-survey-->
</form>
<script>
function btn_update_onclick()
{
    $('#box-answer input').removeAttr('disabled');
        var f = document.frmMain;
        m = $("#controller").val() + f.hdn_update_method.value + '/0/';
        var xObj = new DynamicFormHelper('','',f);
        var begin_date      = $('#txt_begin_date').val();
        var end_date        = $('#txt_end_date').val();
//        var current_date    = getdate();
        if(paresDate_getTime(begin_date) > paresDate_getTime(end_date))
        {
            $('#error-date').show();
            return;
        }
        $('#error-date').hide();
        if (xObj.ValidateForm(f))
        {
            f.XmlData.value = xObj.GetXmlData();
            $("#frmMain").attr("action", m);
            f.submit();
        }
}
function btn_add_question_onclick()
{
    var url ='<?php echo $this->get_controller_url()?>dsp_single_question';
    
    showPopWin(url,500,500,do_add_question,true);

}

function do_add_question(returnVal)
{
    if(typeof(returnVal) != 'undefined')
    {
        question_type       = returnVal[0].question_type
        question_name       = returnVal[0].question_name
        question_status     = returnVal[0].question_status
        answer_delete_id    = returnVal[0].answer_delete_id
        question_results    = returnVal[0].question_results
        question_id         = returnVal[0].question_id;
        if(question_type == 0)
        {
           var cat_question_name = 'Câu hỏi dạng checkbox( Nhiều lựa chọn)';
        }
        else if(question_type == 1)
        {
            cat_question_name = 'Câu hỏi dạng raido(một lựa chọn)';
        }
        else if(question_type == 2)
        {
            cat_question_name = 'Câu hỏi dạng single text(Hiển thị dạng text box khi trả lời)';
        }
        else 
        {
            cat_question_name = 'Câu hỏi dạng text(Hiển thị dạng textArea khi trả lời)';
        }
        
        var list_old_item_id = $('#hdn_list_old_item_id').val() || '';
        var list_new_item_id = $('#hdn_list_new_item_id').val() || '';
        
        var html = $('#template-answer').html();
        
        if(question_status == 'new')// add question new
        {
            var id_new = $('#box-answer .box-question_answer').last().attr('data-new') || 0;
            id_new = parseInt(id_new) +1;
            if(list_new_item_id.trim() == '')
            {
                $('#hdn_list_new_item_id').val(id_new);
            }
            else
            {
                $('#hdn_list_new_item_id').val(list_new_item_id + ',' + id_new);
            }
            
            var html_new = $(html).filter('.box-question_answer').attr('data-new',id_new).attr('id','question_old_' + id_new);
            html_new = $(html_new).find('.btn_question_edit').attr('data-status','new').end();
            
            var json_str = JSON.stringify(returnVal[0].question_results);
            var url_question_name = encodeURIComponent(question_name);
            var url_json_str_answer = encodeURIComponent(json_str);
//            html_new = $(html_new).find('.txt_url').attr('name','txt_json').val(json_str).end();
            html_new = $(html_new).find('.txt_url').attr('name','txt_json').val('&question_id='+question_id +'&question_type='+question_type+'&question_name='+url_question_name+'&question_status=new&question_answer=' + url_json_str_answer).end();
           
           
            html_new = $(html_new).find('.cat-question .txt_question_type_name').val(cat_question_name).end();
            html_new = $(html_new).find('.cat-question .txt_question_type').val(question_type).attr('name','txt_question_type_new_' + id_new).end();
            html_new = $(html_new).find('.question .txt_question_name').val(question_name).attr('name','txt_question_name_new_' + id_new).end();
            if(parseInt(question_type) >1)
            {
                html_new = $(html_new).find('.answer .txt_answer_name').val('').attr('name','btn_onclick_edit_question'+id_new + '[]').end();
                html_new = $(html_new).find('.Row.answer').attr('style','display:none').end();
            }
            else
            {
                for(var i=0;i<question_results.length;i++)
                {
                        if(i == 0)
                        {
                          html_new =  $(html_new).find('.answer .txt_answer_name').val(question_results[i]['answer_name'].toString()).attr('name','txt_answer_name_new_'+id_new + '[]').end();
                        }
                        else
                        {
                            var div_answer = '';
                            div_answer = $(html).find('.Row.answer');

                            div_answer = $(div_answer).find('.left-Col').html('&nbsp;').end();
                            div_answer = $(div_answer).find('.answer .txt_answer_name').val(question_results[i]['answer_name'].toString()).attr('name','txt_answer_name_new_'+id_new + '[]').end();

                            html_new       = $(html_new).find('.div-question').append($(div_answer)).end();
                        }
                    }

            }
            if(parseInt(question_id) >0)
            {
                var box_question = $('#box-answer').find('.box-question_answer[data-new="'+ question_id +'"]') || '';
                
                    if(typeof(box_question) != 'undefined' && $(box_question).length >0)
                    {   
                        $(box_question).html($(html_new).find('.update-old'))
                        list_new_item_id = list_new_item_id.replace(id_new, '', list_new_item_id)
                        $('#hdn_list_new_item_id').val(list_new_item_id);
                    }
            }
            else
            {
                $('#box-answer').append(html_new);
            }
             
        }
        else // add question old
        {
           if(parseInt(question_id) >0)
            {
                var id_old = question_id;
                    //Cập nhật danh sach id answer da duoc xoa 
                    if(answer_delete_id.trim() != '' && typeof(answer_delete_id) != 'undefined')
                    {
                        var list_answer_delete = $('#hdn_list_answer_delete_id').val() || '';
                        if(list_answer_delete.trim() != '')
                        {
                            $('#hdn_list_answer_delete_id').val(list_answer_delete +','+ answer_delete_id);         
                        }
                        else
                        {
                             $('#hdn_list_answer_delete_id').val(answer_delete_id);         
                        }
                    }

                    var old = $(html).filter('.box-question_answer').attr('data-old',id_old).attr('id','question_old_' + id_old);
                    old = $(old).find('.btn_question_edit').attr('data-status','old').end();
                    var json_str = JSON.stringify(returnVal[0].question_results);
//                    html_new = $(html_new).find('.txt_url').val(json_str).attr('name','txt_json').end();
                    var url_question_name = encodeURIComponent(question_name);
                    var url_json_str_answer = encodeURIComponent(json_str);
                   
                    old = $(old).find('.txt_url').attr('name','txt_json').val('&question_name='+url_question_name+'&question_id='+question_id +'&question_type='+question_type+'&question_status=new&question_answer=' + url_json_str_answer).end();
                    
                    old = $(old).find('.cat-question .txt_question_type_name').val(cat_question_name).end();
                    old = $(old).find('.cat-question .txt_question_type').val(question_type).attr('name','txt_question_type_old_' + id_old).end();
                    old = $(old).find('.question .txt_question_name').val(question_name).attr('name','txt_question_name_old_' + id_old).end();
                    
                    if(parseInt(question_type) >1)
                    {            
                        old = $(old).find('.answer .txt_answer_name').val('').attr('name','btn_onclick_edit_question'+id_old + '[]').end();
                        old = $(old).find('.Row.answer').attr('style','display:none').end();
                        // old =  $(old).find('.answer button').remove().end();
                    }
                    else
                    {
                        for(var i=0;i<question_results.length;i++)
                        {
                                if(i == 0)
                                {
                                  old =  $(old).find('.answer .txt_answer_name').val(question_results[i]['answer_name'].toString()).attr('name','txt_answer_name_old_'+id_old + '[]').end();
                                  old =  $(old).find('.answer .txt_answer_id').attr('name','text_question_answer_id_old_'+id_old + '[]').attr('value',question_results[i]['answer_id'].toString()).end();
                                }
                                else
                                {
                                    var div_answer = '';
                                    div_answer = $(html).find('.Row.answer');

                                    div_answer = $(div_answer).find('.left-Col').html('&nbsp;').end();
                                    div_answer = $(div_answer).find('.answer .txt_answer_name').val(question_results[i]['answer_name'].toString()).attr('name','txt_answer_name_old_'+id_old + '[]').end();
                                    div_answer = $(div_answer).find('.answer .txt_answer_id').attr('name','text_question_answer_id_old_'+id_old + '[]').attr('value',question_results[i]['answer_id'].toString()).end();
                                    old        = $(old).find('.div-question').append($(div_answer)).end();
                                }
                            }
                    }
                    var box_question = $('#box-answer').find('.box-question_answer[data-old="'+ question_id +'"]') || '';
                    if(typeof(box_question) != 'undefined' && $(box_question).length >0)
                    { 
                        $(box_question).html($(old).find('.update-old'))
                    }
                    else
                    {
                        alert('Da xay ra loi trong qua trinh cap nhat. Xin vui long thu lai!');
                    }
            }            
        }
       
    }
}

function btn_delete_question_onclick(anchor)
{
    if(!confirm('Bạn có chắc chắn xóa đối tượng đã chọn?')) return;
      var id_item_old =  $(anchor).parents('.box-question_answer').attr('data-old') ||0;
      var id_item_new =  $(anchor).parents('.box-question_answer').attr('data-new') ||0;
      if(parseInt(id_item_new) > 0)
      {
          var list_item_new_id = $('#hdn_list_new_item_id').val() || '';
          list_item_new_id = list_item_new_id.replace(id_item_new, '', list_item_new_id);
          $('#hdn_list_new_item_id').val(list_item_new_id);
      }
      if(parseInt(id_item_old) > 0)
      {
           var list_old_delete_item_id = $('#hdn_list_question_delete_id').val() || '';
           if(list_old_delete_item_id.trim() == '')
           {
               $('#hdn_list_question_delete_id').val(id_item_old);
           }
           else
           {
               $('#hdn_list_question_delete_id').val(list_old_delete_item_id + ',' + id_item_old);
           }
           
           var list_old_item_id   = $('#hdn_list_old_item_id').val();
           list_old_item_id = list_old_item_id.replace(id_item_old, '', list_old_item_id);
           $('#hdn_list_old_item_id').val(list_old_item_id);
      }
     
     $(anchor).parents('.box-question_answer').remove();
     
}
function btn_onclick_edit_question(selector) 
{
    
    var question_json = $(selector).parents('.box-question_answer').find('.txt_url').val() || '';
    var question_id = 0;
    var status = $(selector).attr('data-status');
    var v_url =  $('#controller').val() + $('#hdn_single_questsion_method').val() + '?' + question_json + '&status=' +status;
    if(status  == 'new')
    {
        question_id = $(selector).parents('.box-question_answer').attr('data-new') || 0;
        v_url =  $('#controller').val() + $('#hdn_single_questsion_method').val() + '?' + question_json + '&status=' +status + '&question_id='+question_id;
    }
    
    if(question_json.length >0)
    {
        
        showPopWin(v_url,600,500,do_add_question);
    }
}
    /**
     * Comment: H_x Lấy số mili giây bắt đầu với mốc 01/01/1970 căn cứ vào để so sánh 2 ngày tháng
     * @param date string : dd-mm-yyyy
     * @return int mili giay tuong ung  cua ngay thang chuyen vao so voi moc 
     */
    function paresDate_getTime(str) 
    {
        date = str.split('-');
        return new Date(date[2],date[1],date[0]).getTime();
    }

</script>


<?php
$this->template->display('dsp_footer.php');